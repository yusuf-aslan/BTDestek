<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Kurum logosundaki koyu yazı koyu temada kaybolduğu için yazısı açık renge
 * çevrilmiş bir kopya üretir. Renkli kısımlar (ikon vb.) olduğu gibi kalır.
 */
class BrandLogo
{
    /** Bu değerin altındaki doygunlukta ve parlaklıktaki pikseller "yazı" sayılır. */
    private const MAX_SATURATION = 48;

    private const MAX_BRIGHTNESS = 120;

    /**
     * @param  string  $path  public diskteki logo yolu (ör. settings/logo.png)
     * @return string|null public diskteki koyu tema kopyasının yolu; üretilemezse null
     */
    public static function darkVariant(string $path): ?string
    {
        $disk = Storage::disk('public');
        $variant = dirname($path).'/dark-'.pathinfo($path, PATHINFO_FILENAME).'.png';

        if ($disk->exists($variant)) {
            return $variant;
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

        $width = imagesx($image);
        $height = imagesy($image);

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgba = imagecolorat($image, $x, $y);
                $alpha = ($rgba >> 24) & 0x7F;
                if ($alpha === 127) {
                    continue;
                }

                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;

                if (max($r, $g, $b) - min($r, $g, $b) <= self::MAX_SATURATION && max($r, $g, $b) <= self::MAX_BRIGHTNESS) {
                    imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, 255, 255, 255, $alpha));
                }
            }
        }

        ob_start();
        imagepng($image);
        $png = ob_get_clean();
        imagedestroy($image);

        return $disk->put($variant, $png) ? $variant : null;
    }
}
