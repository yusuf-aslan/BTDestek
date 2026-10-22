<?php

namespace App\Support;

class UploadLimits
{
    /**
     * PHP (upload_max_filesize, post_max_size) ve Livewire geçici yükleme sınırlarının en küçüğü, KB cinsinden.
     */
    public static function serverLimitKb(): int
    {
        $limits = array_filter([
            self::iniToKb(ini_get('upload_max_filesize')),
            self::iniToKb(ini_get('post_max_size')),
            self::livewireLimitKb(),
        ], fn ($kb) => $kb > 0);

        return $limits ? min($limits) : 0;
    }

    public static function serverLimitLabel(): string
    {
        $kb = self::serverLimitKb();

        return $kb >= 1024 ? floor($kb / 1024).' MB' : $kb.' KB';
    }

    /**
     * Panelde girilen sınır ile sunucu sınırının küçüğü, KB cinsinden.
     */
    public static function effectiveKb(?int $settingMb): int
    {
        $settingKb = max(1, (int) ($settingMb ?: 200)) * 1024;
        $serverKb = self::serverLimitKb();

        return $serverKb > 0 ? min($settingKb, $serverKb) : $settingKb;
    }

    private static function livewireLimitKb(): int
    {
        foreach ((array) config('livewire.temporary_file_upload.rules') as $rule) {
            if (is_string($rule) && preg_match('/^max:(\d+)$/', $rule, $m)) {
                return (int) $m[1];
            }
        }

        // Livewire varsayılanı 12 MB
        return 12288;
    }

    private static function iniToKb(string|false $value): int
    {
        if ($value === false || $value === '' || $value === '0' || $value === '-1') {
            return 0;
        }

        $number = (float) $value;

        return (int) match (strtolower(substr(trim($value), -1))) {
            'g' => $number * 1024 * 1024,
            'm' => $number * 1024,
            'k' => $number,
            default => $number / 1024,
        };
    }
}
