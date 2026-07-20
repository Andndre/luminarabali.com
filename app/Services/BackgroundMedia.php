<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Memilih media latar sebuah section dari props-nya.
 *
 * Dipakai dua penggambar yang markup-nya berbeda: _section-shell (lapisan .sec-bg untuk
 * section biasa) dan komponen cover (gate position:fixed + layar sticky, dengan lapisan
 * blur dan jendela sendiri). Yang boleh sama hanya keputusannya — foto tunggal, slideshow,
 * berkas video, atau YouTube — jadi keputusan itu tinggal di sini, bukan disalin dua kali.
 */
class BackgroundMedia
{
    /** Berkas yang boleh masuk ke <video>. Tautan halaman (Vimeo dsb) bukan berkas. */
    public const VIDEO_EXTENSIONS = ['mp4', 'webm', 'ogv', 'ogg', 'mov', 'm4v'];

    /**
     * @return array{
     *     type: string, image: ?string, slides: array<int, string>, video: ?string,
     *     youtube: ?string, poster: ?string, slideSeconds: int, keyframes: string, has: bool
     * }
     */
    public static function resolve(array $props): array
    {
        $image = self::url($props['bg_image'] ?? null);
        $poster = self::url($props['bg_poster'] ?? null) ?: $image;

        $type = in_array($props['bg_media_type'] ?? null, ['image', 'slideshow', 'video'], true)
            ? $props['bg_media_type']
            : 'image';

        $slides = [];
        $video = null;
        $youtube = null;

        if ($type === 'slideshow') {
            foreach (is_array($props['bg_images'] ?? null) ? $props['bg_images'] : [] as $item) {
                $src = self::url(is_array($item) ? ($item['url'] ?? null) : $item);
                if ($src) {
                    $slides[] = $src;
                }
            }
            // Satu foto bukan slideshow; jadikan foto tunggal supaya tidak ada animasi sia-sia.
            if (count($slides) === 1) {
                $image = $image ?: $slides[0];
                $slides = [];
                $type = 'image';
            }
        } elseif ($type === 'video') {
            // Satu kolom, dua sumber. Tautan YouTube harus dicek LEBIH DULU: URL apa pun
            // lolos self::url() apa adanya, jadi tanpa cek ini tautan YouTube berakhir
            // sebagai src <video> yang tidak bisa diputar. Hanya ID-nya yang dipakai.
            $youtube = InvitationRenderer::youtubeId($props['bg_video'] ?? null);
            if (! $youtube) {
                $candidate = self::url($props['bg_video'] ?? null);
                $ext = $candidate
                    ? strtolower(pathinfo(parse_url($candidate, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION))
                    : '';
                $video = in_array($ext, self::VIDEO_EXTENSIONS, true) ? $candidate : null;
            }
            if (! $video && ! $youtube) {
                // Video belum diisi → posternya yang jadi foto latar biasa.
                $image = $poster;
                $type = 'image';
            }
        }

        return [
            'type' => $type,
            'image' => $image,
            'slides' => $slides,
            'video' => $video,
            'youtube' => $youtube,
            'poster' => $poster,
            'slideSeconds' => max(2, min(30, (int) ($props['bg_slide_seconds'] ?? 5))),
            'keyframes' => self::slideKeyframes(count($slides)),
            'has' => (bool) ($image || $slides || $video || $youtube),
        ];
    }

    /**
     * Crossfade: tiap slide tampil 1/n siklus dengan jendela redup yang beririsan dengan
     * slide berikutnya. Persentasenya bergantung jumlah slide, jadi keyframe digenerate
     * per-n. Namanya memakai n saja: dua section dengan jumlah slide sama menghasilkan
     * definisi identik, jadi duplikatnya tidak berbahaya.
     */
    public static function slideKeyframes(int $n): string
    {
        if ($n < 2) {
            return '';
        }

        $fade = 6; // persen siklus untuk satu transisi
        $hold = 100 / $n;
        $pct = fn (float $v) => rtrim(rtrim(number_format($v, 2, '.', ''), '0'), '.');

        return '@keyframes sec-bgslide-'.$n
            .'{0%{opacity:0}'.$pct($fade).'%{opacity:1}'.$pct($hold).'%{opacity:1}'
            .$pct($hold + $fade).'%{opacity:0}100%{opacity:0}}';
    }

    /** Poster untuk latar YouTube: milik sendiri, atau thumbnail video itu (dua lapis). */
    public static function youtubePosterCss(string $videoId, ?string $poster): string
    {
        if ($poster) {
            return "url('".$poster."')";
        }

        // maxres tidak tersedia untuk semua video dan kegagalannya diam; mq selalu ada.
        return "url('https://i.ytimg.com/vi/{$videoId}/maxresdefault.jpg'),"
            ."url('https://i.ytimg.com/vi/{$videoId}/mqdefault.jpg')";
    }

    private static function url(mixed $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        return Str::startsWith($value, ['http://', 'https://', '/'])
            ? $value
            : '/storage/'.ltrim($value, '/');
    }
}
