<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationPage;
use App\Models\InvitationSection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class InvitationEditorController extends Controller
{
    public function editor($id)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $page = InvitationPage::with(['sections', 'template'])->findOrFail($id);
        return view('admin.invitations.editor-react', compact('page'));
    }

    public function load($id)
    {
        $page = InvitationPage::with(['sections' => function ($query) {
            $query->orderBy('order_index');
        }])->findOrFail($id);

        return response()->json([
            'template' => [
                'id' => $page->id,
                'name' => $page->title,
                'slug' => $page->slug,
                'global_custom_css' => data_get($page->meta_data, 'global_custom_css', ''),
                'is_active' => $page->published_status === 'published',
                'published_status' => $page->published_status,
                'event_date' => optional($page->event_date)->toDateTimeString(),
            ],
            'sections' => $page->sections->map(fn ($section) => $this->transformSection($section))->values()->toArray(),
        ]);
    }

    public function saveSection(Request $request)
    {
        $allowedSectionTypes = implode(',', $this->allowedSectionTypes());

        $request->validate([
            'page_id' => 'required|exists:invitation_pages,id',
            'global_custom_css' => 'nullable|string',
            'sections' => 'present|array',
            'sections.*.id' => 'required|string',
            'sections.*.parent_id' => 'nullable|string',
            'sections.*.section_type' => 'required|string|in:'.$allowedSectionTypes,
            'sections.*.order_index' => 'required|integer',
            'sections.*.props' => 'required|array',
            'sections.*.custom_css' => 'nullable|string',
            'sections.*.is_visible' => 'nullable|boolean',
        ]);

        $pageId = (int) $request->page_id;
        $globalCustomCss = $request->global_custom_css;
        $sections = $request->sections ?? [];

        $nonTempIds = collect($sections)
            ->pluck('id')
            ->filter(fn ($id) => ! str_starts_with($id, 'temp-'))
            ->values();

        if ($nonTempIds->count() !== $nonTempIds->unique()->count()) {
            throw ValidationException::withMessages([
                'sections' => ['Duplicate section id found in payload.'],
            ]);
        }

        $savedSections = DB::transaction(function () use ($pageId, $sections, $nonTempIds, $globalCustomCss) {
            $page = InvitationPage::findOrFail($pageId);
            $metaData = $page->meta_data ?? [];
            $metaData['global_custom_css'] = $globalCustomCss;
            $page->update(['meta_data' => $metaData]);

            $existingSections = InvitationSection::where('page_id', $pageId)
                ->get()
                ->keyBy(fn ($section) => (string) $section->id);

            if ($nonTempIds->count() > 0) {
                $missingIds = $nonTempIds->diff($existingSections->keys());
                if ($missingIds->isNotEmpty()) {
                    throw ValidationException::withMessages([
                        'sections' => ['Some section ids are invalid for this invitation.'],
                    ]);
                }
            }

            $deleteQuery = InvitationSection::where('page_id', $pageId);
            if ($nonTempIds->count() > 0) {
                $deleteQuery->whereNotIn('id', $nonTempIds->all());
            }
            $deleteQuery->delete();

            $persistedIds = $existingSections->keys()->map(fn ($id) => (string) $id)->all();
            $tempIdMapping = [];
            $savedSections = [];

            foreach ($sections as $sectionData) {
                $incomingId = (string) $sectionData['id'];
                $parentId = $this->resolveParentId(
                    $sectionData['parent_id'] ?? null,
                    $tempIdMapping,
                    $persistedIds,
                );

                $payload = [
                    'page_id' => $pageId,
                    'parent_id' => $parentId,
                    'section_type' => $sectionData['section_type'],
                    'order_index' => $sectionData['order_index'],
                    'props' => $sectionData['props'] ?? [],
                    'custom_css' => $sectionData['custom_css'] ?? null,
                    'is_visible' => $sectionData['is_visible'] ?? true,
                ];

                if (str_starts_with($incomingId, 'temp-')) {
                    $newSection = InvitationSection::create($payload);
                    $newId = (string) $newSection->id;
                    $tempIdMapping[$incomingId] = $newId;
                    $persistedIds[] = $newId;
                    $savedSections[] = [
                        'temp_id' => $incomingId,
                        'id' => $newSection->id,
                    ];
                    continue;
                }

                /** @var InvitationSection $section */
                $section = $existingSections->get($incomingId);
                $section->update($payload);
            }

            return $savedSections;
        });

        return response()->json([
            'success' => true,
            'message' => 'Sections saved',
            'sections' => $savedSections,
        ]);
    }

    public function updateSection(Request $request, $id)
    {
        $section = InvitationSection::findOrFail($id);
        $section->update($request->only(['props', 'custom_css', 'is_visible']));

        return response()->json(['success' => true, 'section' => $section]);
    }

    public function deleteSection($id)
    {
        $section = InvitationSection::findOrFail($id);
        $section->delete();

        return response()->json(['success' => true, 'message' => 'Section deleted']);
    }

    public function reorderSections(Request $request)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:invitation_sections,id',
            'sections.*.order_index' => 'required|integer',
        ]);

        foreach ($request->sections as $sectionData) {
            $section = InvitationSection::find($sectionData['id']);
            if ($section) {
                $section->update(['order_index' => $sectionData['order_index']]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function publish(Request $request, $id)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $page = InvitationPage::findOrFail($id);
        $page->update(['published_status' => 'published']);

        return response()->json(['success' => true, 'message' => 'Invitation published']);
    }

    private function transformSection(InvitationSection $section): array
    {
        return [
            'id' => (string) $section->id,
            'parent_id' => $section->parent_id ? (string) $section->parent_id : null,
            'section_type' => $section->section_type,
            'order_index' => $section->order_index,
            'props' => $section->props ?? [],
            'custom_css' => $section->custom_css,
            'is_visible' => (bool) $section->is_visible,
        ];
    }

    private function resolveParentId(?string $parentId, array $tempIdMapping, array $persistedIds): ?int
    {
        if (empty($parentId)) {
            return null;
        }

        if (str_starts_with($parentId, 'temp-')) {
            if (! isset($tempIdMapping[$parentId])) {
                throw ValidationException::withMessages([
                    'sections' => ['Invalid section structure: parent must be created before child.'],
                ]);
            }

            return (int) $tempIdMapping[$parentId];
        }

        if (! in_array($parentId, $persistedIds, true)) {
            throw ValidationException::withMessages([
                'sections' => ['Invalid parent_id reference found in sections payload.'],
            ]);
        }

        return (int) $parentId;
    }

    private function allowedSectionTypes(): array
    {
        return [
            'section_one_col',
            'section_two_col',
            'section_three_col',
            'hero',
            'text',
            'image',
            'button',
            'divider',
            'spacer',
            'countdown',
            'gallery',
            'map',
            'music',
            'rsvp',
            'video',
        ];
    }
}
