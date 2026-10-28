<?php

namespace App\Support;

use App\Models\GeneralSetting;
use App\Models\Holiday;
use Carbon\CarbonInterface;

/**
 * Destek sayfasından talep alınıp alınmadığına karar verir.
 * Ana sayfadaki uyarı ile talep kaydı aynı kuralı kullansın diye tek yerde tutulur.
 */
class TicketIntake
{
    private const EMERGENCY = 'Acil durumlar için lütfen nöbetçi müdür ile görüşünüz.';

    /** @param  array{name: string, is_half_day: bool, closes_at: ?string, message: ?string}|null  $holiday */
    private function __construct(
        public readonly ?string $closedReason,
        public readonly ?string $notice,
        public readonly ?array $holiday,
    ) {}

    public static function at(?GeneralSetting $settings, ?CarbonInterface $now = null): self
    {
        $now ??= now();

        if (! $settings) {
            return new self(null, null, null);
        }

        $holiday = Holiday::forDate($now, (bool) ($settings->official_holidays_enabled ?? true));

        if ($holiday && ! $settings->holiday_tickets_allowed) {
            if (! $holiday['is_half_day']) {
                return new self(
                    $holiday['message'] ?: "{$holiday['name']} nedeniyle bugün sistem üzerinden talep kabul edilmemektedir. ".self::EMERGENCY,
                    null,
                    $holiday,
                );
            }

            $closesAt = $holiday['closes_at'] ?? '13:00';

            if ($now->format('H:i') >= $closesAt) {
                return new self(
                    $holiday['message'] ?: "Bugün {$holiday['name']} nedeniyle yarım gündür; talep kabulü saat {$closesAt} itibarıyla sona ermiştir. ".self::EMERGENCY,
                    null,
                    $holiday,
                );
            }

            $notice = "Bugün {$holiday['name']} nedeniyle yarım gündür; talep kabulü saat {$closesAt} itibarıyla sona erecektir.";
        }

        if ($now->isWeekend() && ! $settings->weekend_tickets_allowed) {
            return new self('Hafta sonları sistem üzerinden talep kabul edilmemektedir. '.self::EMERGENCY, null, $holiday);
        }

        if (! $settings->allow_tickets_outside_work_hours) {
            $start = substr((string) $settings->work_hours_start, 0, 5);
            $end = substr((string) $settings->work_hours_end, 0, 5);
            $time = $now->format('H:i');

            if ($time < $start || $time >= $end) {
                return new self(
                    "Sistemimiz günlük talep kabul saatleri dışındadır. Taleplerinizi hafta içi {$start} - {$end} saatleri arasında iletebilirsiniz.",
                    null,
                    $holiday,
                );
            }
        }

        return new self(null, $notice ?? null, $holiday);
    }

    public function isClosed(): bool
    {
        return $this->closedReason !== null;
    }
}
