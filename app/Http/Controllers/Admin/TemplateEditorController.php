<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvitationPage;
use App\Models\InvitationTemplate;
use App\Models\InvitationSection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TemplateEditorController extends Controller
{
    public function editor($id)
    {
        $currentUserId = Auth::id();
        $currentUser = \App\Models\User::find($currentUserId);

        if ($currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $template = InvitationTemplate::with(['sections', 'creator'])->findOrFail($id);
        return view('admin.templates.editor-native', compact('template'));
    }

    public function load($id)
    {
        $this->authorizeSuperAdmin();

        $template = InvitationTemplate::with(['sections' => function ($query) {
            $query->orderBy('order_index');
        }])->findOrFail($id);

        return response()->json([
            'template' => $template,
            'sections' => $template->sections->map(fn($section) => $this->transformSection($section))->values()->toArray(),
        ]);
    }

    public function saveSection(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:invitation_templates,id',
            'global_custom_css' => 'nullable|string',
            'html_content' => 'nullable|string',
            'cover_content' => 'nullable|string',
            'meta_data' => 'nullable|string',
        ]);

        $templateId = (int) $request->template_id;
        $template = InvitationTemplate::findOrFail($templateId);

        $metaData = null;
        if ($request->meta_data) {
            $metaData = json_decode($request->meta_data, true);
        }

        $template->update([
            'global_custom_css' => $request->global_custom_css,
            'html_content' => $request->html_content,
            'cover_content' => $request->cover_content,
            'meta_data' => $metaData,
        ]);

        InvitationPage::where('template_id', $templateId)
            ->pluck('slug')
            ->each(fn ($slug) => Cache::forget("invitation:{$slug}"));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Berhasil disimpan!']);
        }

        return redirect()->back()->with('success', 'Berhasil disimpan!');
    }

    public function updateSection(Request $request, $id)
    {
        $this->authorizeSuperAdmin();

        $section = InvitationSection::findOrFail($id);
        $validated = (new \App\Services\SectionPropsValidator())->validate(
            $section->section_type,
            $request->input('props', $section->props ?? [])
        );
        $section->update(array_merge(
            $request->only(['custom_css', 'is_visible']),
            ['props' => $validated]
        ));

        return response()->json(['success' => true, 'section' => $section]);
    }

    public function deleteSection($id)
    {
        $this->authorizeSuperAdmin();

        $section = InvitationSection::findOrFail($id);
        $section->delete();

        return response()->json(['success' => true, 'message' => 'Section deleted']);
    }

    public function reorderSections(Request $request)
    {
        $this->authorizeSuperAdmin();

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

        $template = InvitationTemplate::findOrFail($id);
        $template->update(['status' => 'published']);

        return response()->json(['success' => true, 'message' => 'Template published']);
    }

    public function preview($id)
    {
        $template = InvitationTemplate::with(['sections' => function ($query) {
            $query->orderBy('order_index');
        }])->findOrFail($id);

        return view('admin.templates.preview', compact('template'));
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
            if (!isset($tempIdMapping[$parentId])) {
                throw ValidationException::withMessages([
                    'sections' => ['Invalid section structure: parent must be created before child.'],
                ]);
            }

            return (int) $tempIdMapping[$parentId];
        }

        if (!in_array($parentId, $persistedIds, true)) {
            throw ValidationException::withMessages([
                'sections' => ['Invalid parent_id reference found in sections payload.'],
            ]);
        }

        return (int) $parentId;
    }

    private function authorizeSuperAdmin(): void
    {
        $currentUser = \App\Models\User::find(\Illuminate\Support\Facades\Auth::id());

        if (!$currentUser || $currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    private function allowedSectionTypes(): array
    {
        return [
            'cover',
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
