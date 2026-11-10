<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Kurum logosunun açık ve koyu tema kopyalarını üretir.
 *
 * Logo koyu yazılı da yüklense beyaz yazılı da yüklense yazı kısmı ayırt edilir ve
 * açık temada koyu, koyu temada beyaz gösterilir. Yazı; saydam zemine değen, renksiz
 * (siyah-gri-beyaz) ve renkli bir alana (ikon) değmeyen piksel kümeleridir. Böylece
 * ikonun içindeki beyaz figürler ve renkler olduğu gibi kalır.
 */
class BrandLogo
{
    /** Bu doygunluğun altındaki pikseller renksiz sayılır. */
    private const MAX_SATURATION = 48;

    /** Bu saydamlığın üstündeki pikseller zemin sayılır (0 opak, 127 tam saydam). */
    private const BACKGROUND_ALPHA = 110;

    private const LIGHT_TEXT = [15, 23, 42];   // açık temada yazı rengi

    private const DARK_TEXT = [255, 255, 255]; // koyu temada yazı rengi

    /** Açık zeminde (açık tema) gösterilecek kopya. */
    public static function forLight(string $path): string
    {
        return self::variant($path, 'light') ?? $path;
    }

    /** Koyu zeminde (koyu tema) gösterilecek kopya. */
    public static function forDark(string $path): string
    {
        return self::variant($path, 'dark') ?? $path;
    }

    private static function variant(string $path, string $mode): ?string
    {
        $disk = Storage::disk('public');
        $target = dirname($path).'/logo-'.$mode.'-'.pathinfo($path, PATHINFO_FILENAME).'.png';

        if ($disk->exists($target)) {
            return $target;
        }

        if (! function_exists('imagecreatefromstring') || ! $disk->exists($path)) {
            return null;
        }

        $image = @imagecreatefromstring($disk->get($path));
        if ($image === false) {
            return null;
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        [$r, $g, $b] = $mode === 'light' ? self::LIGHT_TEXT : self::DARK_TEXT;
        foreach (self::textPixels($image) as [$x, $y]) {
            $alpha = (imagecolorat($image, $x, $y) >> 24) & 0x7F;
            imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, $r, $g, $b, $alpha));
        }

        ob_start();
        imagepng($image);
        $png = ob_get_clean();
        imagedestroy($image);

        return $disk->put($target, $png) ? $target : null;
    }

    /**
     * @return list<array{int, int}>
     */
    private static function textPixels(\GdImage $image): array
    {
        $width = imagesx($image);
        $height = imagesy($image);

        // 0: zemin, 1: renksiz (yazı adayı), 2: renkli
        $kind = [];
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorat($image, $x, $y);
                $alpha = ($rgba >> 24) & 0x7F;
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;

                $kind[$y * $width + $x] = match (true) {
                    $alpha > self::BACKGROUND_ALPHA => 0,
                    max($r, $g, $b) - min($r, $g, $b) <= self::MAX_SATURATION => 1,
                    default => 2,
                };
            }
        }

        $text = [];
        $seen = [];
        foreach ($kind as $start => $type) {
            if ($type !== 1 || isset($seen[$start])) {
                continue;
            }

            // Renksiz komşu pikselleri tek küme olarak topla
            $component = [];
            $touchesBackground = false;
            $touchesColor = false;
            $stack = [$start];
            $seen[$start] = true;

            while ($stack) {
                $index = array_pop($stack);
                $x = $index % $width;
                $y = intdiv($index, $width);
                $component[] = [$x, $y];

                for ($dy = -1; $dy <= 1; $dy++) {
                    for ($dx = -1; $dx <= 1; $dx++) {
                        if ($dx === 0 && $dy === 0) {
                            continue;
                        }
                        $nx = $x + $dx;
                        $ny = $y + $dy;
                        if ($nx < 0 || $ny < 0 || $nx >= $width || $ny >= $height) {
                            $touchesBackground = true;

                            continue;
                        }
                        $neighbor = $ny * $width + $nx;
                        if ($kind[$neighbor] === 0) {
                            $touchesBackground = true;
                        } elseif ($kind[$neighbor] === 2) {
                            $touchesColor = true;
                        } elseif (! isset($seen[$neighbor])) {
                            $seen[$neighbor] = true;
                            $stack[] = $neighbor;
                        }
                    }
                }
            }

            if ($touchesBackground && ! $touchesColor) {
                array_push($text, ...$component);
            }
        }

        return $text;
    }
}
