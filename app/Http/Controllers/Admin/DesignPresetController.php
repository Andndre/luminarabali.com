<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesignPreset;
use App\Services\SectionPropsValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignPresetController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        $presets = DesignPreset::query()
            ->when($request->filled('section_type'), fn ($q) => $q->where('section_type', $request->input('section_type')))
            ->latest()
            ->get(['id', 'name', 'category', 'section_type', 'props']);

        return response()->json(['presets' => $presets]);
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'section_type' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!is_array(config("invitation_components.{$value}"))) {
                    $fail('Tipe section tidak dikenal.');
                }
            }],
            'props' => 'required|array',
        ]);

        $props = (new SectionPropsValidator())->validate(
            $request->input('section_type'),
            $request->input('props')
        );

        $preset = DesignPreset::create([
            'name' => $request->input('name'),
            'category' => $request->input('category'),
            'section_type' => $request->input('section_type'),
            'props' => $props,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'preset' => $preset], 201);
    }

    public function destroy($id)
    {
        $this->authorizeSuperAdmin();

        DesignPreset::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    private function authorizeSuperAdmin(): void
    {
        $currentUser = \App\Models\User::find(Auth::id());

        if (!$currentUser || $currentUser->division !== 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
    }
}
