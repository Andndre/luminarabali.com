<?php

namespace App\Services;

use App\Models\InvitationPage;
use App\Models\InvitationSection;
use App\Models\InvitationTemplate;
use Illuminate\Support\Facades\DB;

class TemplateInstantiator
{
    /**
     * Copy-on-create: buat page dari template dan salin section master
     * (template_id, page_id null) menjadi salinan milik page, satu transaksi.
     * Titik tunggal instansiasi — seam untuk gerbang kredit (spec §14b).
     */
    public function instantiate(InvitationTemplate $template, array $pageAttributes): InvitationPage
    {
        return DB::transaction(function () use ($template, $pageAttributes) {
            $page = InvitationPage::create(
                array_merge($pageAttributes, ['template_id' => $template->id])
            );

            $masters = InvitationSection::where('template_id', $template->id)
                ->whereNull('page_id')
                ->orderBy('order_index')
                ->get();

            $idMap = [];

            // Parent dibuat lebih dulu supaya parent_id anak bisa di-remap.
            $ordered = $masters->sortBy(fn ($s) => $s->parent_id === null ? 0 : 1)->values();

            foreach ($ordered as $master) {
                $copy = InvitationSection::create([
                    'page_id' => $page->id,
                    'template_id' => null,
                    'parent_id' => $master->parent_id !== null ? ($idMap[$master->parent_id] ?? null) : null,
                    'section_type' => $master->section_type,
                    'order_index' => $master->order_index,
                    'props' => $master->props,
                    'custom_css' => $master->custom_css,
                    'is_visible' => $master->is_visible,
                ]);
                $idMap[$master->id] = $copy->id;
            }

            return $page->load('sections');
        });
    }
}
