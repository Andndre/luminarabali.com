<?php

namespace Database\Seeders;

use App\Models\InvitationAsset;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Stok ornamen bawaan (SVG buatan sendiri, palet gold) untuk media library
 * collection=ornament — mengisi grid picker di inspector Studio.
 * Idempoten: updateOrCreate per file_path.
 */
class OrnamentSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('division', 'super_admin')->value('id') ?? 1;

        foreach ($this->ornaments() as $name => [$file, $svg, $w, $h]) {
            $path = "ornaments/{$file}";
            Storage::disk('public')->put($path, trim($svg));

            InvitationAsset::updateOrCreate(
                ['file_path' => $path],
                [
                    'page_id' => null,
                    'asset_name' => $name,
                    'file_type' => 'image',
                    'mime_type' => 'image/svg+xml',
                    'file_size' => strlen(trim($svg)),
                    'dimensions' => ['width' => $w, 'height' => $h],
                    'uploaded_by' => $adminId,
                    'visibility' => 'team',
                    'collection' => 'ornament',
                ]
            );
        }

        $this->command?->info('5 ornamen SVG tersedia di media library (collection=ornament).');
    }

    private function ornaments(): array
    {
        $gold = '#c9a24b';
        $goldLight = '#e2c078';

        // Sulur pojok — dahan melengkung dari pojok kiri-atas, daun almond + beri.
        $sprig = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 240" fill="none">
  <g stroke="{$gold}" stroke-width="2.5" stroke-linecap="round">
    <path d="M8 8 C72 16 142 46 198 120"/>
    <path d="M8 8 C24 68 50 124 98 170"/>
    <path d="M8 8 C52 10 98 24 140 54"/>
  </g>
  <g fill="{$gold}">
    <path d="M62 18 q14 -13 29 -3 q-12 15 -29 3z"/>
    <path d="M106 32 q16 -11 31 2 q-14 13 -31 -2z"/>
    <path d="M150 58 q17 -7 27 8 q-16 9 -27 -8z"/>
    <path d="M180 94 q17 -3 23 14 q-18 5 -23 -14z"/>
    <path d="M20 54 q-13 14 -3 29 q15 -12 3 -29z"/>
    <path d="M38 100 q-11 17 2 31 q13 -14 -2 -31z"/>
    <path d="M64 140 q-7 17 8 27 q9 -16 -8 -27z"/>
    <circle cx="198" cy="120" r="4"/>
    <circle cx="98" cy="170" r="4"/>
    <circle cx="140" cy="54" r="3.5"/>
    <circle cx="8" cy="8" r="5"/>
  </g>
  <g fill="{$goldLight}">
    <circle cx="84" cy="24" r="2.5"/>
    <circle cx="128" cy="44" r="2.5"/>
    <circle cx="28" cy="78" r="2.5"/>
    <circle cx="50" cy="122" r="2.5"/>
    <circle cx="170" cy="80" r="2.5"/>
  </g>
</svg>
SVG;

        // Versi kanan = mirror horizontal.
        $sprigRight = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 240" fill="none">
  <g transform="translate(240 0) scale(-1 1)">
    {$this->inner($sprig)}
  </g>
</svg>
SVG;

        // Flourish tengah — pembatas simetris: garis, gulungan, belah ketupat.
        $flourish = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 480 80" fill="none">
  <g stroke="{$gold}" stroke-width="2.5" stroke-linecap="round">
    <path d="M36 40 H206"/>
    <path d="M274 40 H444"/>
    <path d="M206 40 c-22 0 -32 -17 -17 -26 c11 -6 22 5 13 13"/>
    <path d="M274 40 c22 0 32 -17 17 -26 c-11 -6 -22 5 -13 13"/>
    <path d="M206 40 c-22 0 -32 17 -17 26 c11 6 22 -5 13 -13"/>
    <path d="M274 40 c22 0 32 17 17 26 c-11 6 -22 -5 -13 -13"/>
  </g>
  <g fill="{$gold}">
    <rect x="230" y="30" width="20" height="20" transform="rotate(45 240 40)"/>
    <circle cx="36" cy="40" r="3.5"/>
    <circle cx="444" cy="40" r="3.5"/>
    <path d="M96 40 q12 -14 28 -9 q-9 14 -28 9z"/>
    <path d="M356 31 q16 -5 28 9 q-19 5 -28 -9z"/>
  </g>
  <g fill="{$goldLight}">
    <circle cx="140" cy="40" r="2.5"/>
    <circle cx="340" cy="40" r="2.5"/>
    <rect x="234" y="34" width="12" height="12" transform="rotate(45 240 40)"/>
  </g>
</svg>
SVG;

        // Garland atas — untaian swag menggantung, liontin daun di tiap lengkung.
        $garland = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 70" fill="none">
  <g stroke="{$gold}" stroke-width="2.5" stroke-linecap="round">
    <path d="M0 10 Q100 52 200 10 Q300 52 400 10 Q500 52 600 10 Q700 52 800 10"/>
  </g>
  <g fill="{$gold}">
    <circle cx="0" cy="10" r="4"/><circle cx="200" cy="10" r="4"/>
    <circle cx="400" cy="10" r="4"/><circle cx="600" cy="10" r="4"/>
    <circle cx="800" cy="10" r="4"/>
    <path d="M100 33 q7 11 0 20 q-7 -9 0 -20z"/>
    <path d="M300 33 q7 11 0 20 q-7 -9 0 -20z"/>
    <path d="M500 33 q7 11 0 20 q-7 -9 0 -20z"/>
    <path d="M700 33 q7 11 0 20 q-7 -9 0 -20z"/>
  </g>
  <g fill="{$goldLight}">
    <circle cx="100" cy="58" r="2.5"/><circle cx="300" cy="58" r="2.5"/>
    <circle cx="500" cy="58" r="2.5"/><circle cx="700" cy="58" r="2.5"/>
  </g>
</svg>
SVG;

        // Renda bawah — lengkung membusur ke atas dari tepi bawah + titik puncak.
        $lace = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 60" fill="none">
  <g stroke="{$gold}" stroke-width="2.5" stroke-linecap="round">
    <path d="M0 52 Q100 12 200 52 Q300 12 400 52 Q500 12 600 52 Q700 12 800 52"/>
    <path d="M0 58 H800" stroke-width="2"/>
  </g>
  <g fill="{$gold}">
    <circle cx="100" cy="26" r="3.5"/><circle cx="300" cy="26" r="3.5"/>
    <circle cx="500" cy="26" r="3.5"/><circle cx="700" cy="26" r="3.5"/>
  </g>
  <g fill="{$goldLight}">
    <circle cx="200" cy="48" r="2.5"/><circle cx="400" cy="48" r="2.5"/>
    <circle cx="600" cy="48" r="2.5"/>
  </g>
</svg>
SVG;

        return [
            'Sulur Pojok Kiri' => ['sulur-pojok-kiri.svg', $sprig, 240, 240],
            'Sulur Pojok Kanan' => ['sulur-pojok-kanan.svg', $sprigRight, 240, 240],
            'Flourish Tengah' => ['flourish-tengah.svg', $flourish, 480, 80],
            'Garland Atas' => ['garland-atas.svg', $garland, 800, 70],
            'Renda Bawah' => ['renda-bawah.svg', $lace, 800, 60],
        ];
    }

    /** Ambil isi di antara tag <svg> untuk digunakan ulang dalam mirror. */
    private function inner(string $svg): string
    {
        return preg_replace('/^\s*<svg[^>]*>|<\/svg>\s*$/', '', trim($svg));
    }
}
