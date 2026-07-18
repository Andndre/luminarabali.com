<?php

namespace Database\Seeders;

use App\Models\InvitationPage;
use App\Models\InvitationTemplate;
use App\Models\User;
use App\Services\TemplateInstantiator;
use Illuminate\Database\Seeder;

/**
 * Page demo publik `/invitation/john-silvia`, dibuat dari template gaya A
 * (Terracotta Dawn) lewat TemplateInstantiator — jadi section-nya otomatis
 * mengikuti master template (treatment/variant/bg_effect SP1), tidak ada
 * definisi section yang diduplikasi di sini.
 *
 * Ada supaya `migrate:fresh --seed` mengembalikan halaman demo. Sebelumnya
 * tidak ada seeder yang membuat page ini, sehingga reset DB menghapusnya
 * permanen (dan WahDemoSeeder justru mensyaratkan page ini sudah ada).
 */
class DemoInvitationPageSeeder extends Seeder
{
    public function run(): void
    {
        if (InvitationPage::where('slug', 'john-silvia')->exists()) {
            $this->command?->info('Page john-silvia sudah ada — dilewati.');

            return;
        }

        $template = InvitationTemplate::where('slug', 'terracotta-dawn')->first()
            ?? InvitationTemplate::first();

        if (! $template) {
            $this->command?->warn('Belum ada template — page demo dilewati.');

            return;
        }

        $page = app(TemplateInstantiator::class)->instantiate($template, [
            'title' => 'John & Silvia',
            'slug' => 'john-silvia',
            'groom_name' => 'John',
            'bride_name' => 'Silvia',
            'event_date' => now()->addMonths(6),
            'published_status' => 'published',
            'created_by' => User::where('division', 'super_admin')->value('id') ?? User::value('id'),
        ]);

        $this->command?->info("Page demo john-silvia dibuat dari template \"{$template->name}\" ({$page->sections->count()} section).");
    }
}
