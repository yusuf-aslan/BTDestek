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
    private const CLOSED = 'Talepler yalnızca mesai saatleri içinde alınmaktadır.';

    /** @param  array{name: string, is_half_day: bool, closes_at: ?string, message: ?string}|null  $holiday */
    private function __construct(
        public readonly ?string $closedReason,
        public readonly ?string $notice,
        public readonly ?string $workHours,
        public readonly ?array $holiday,
    ) {}

    public static function at(?GeneralSetting $settings, ?CarbonInterface $now = null): self
    {
        $now ??= now();

        if (! $settings) {
            return new self(null, null, null, null);
        }

        $workHours = self::workHoursLabel($settings);
        $holiday = Holiday::forDate($now, (bool) ($settings->official_holidays_enabled ?? true));
        $closed = fn (?string $reason = null) => new self($reason ?: self::CLOSED, null, $workHours, $holiday);

        if ($holiday && ! $settings->holiday_tickets_allowed) {
            if (! $holiday['is_half_day']) {
                return $closed($holiday['message']);
            }

            $closesAt = $holiday['closes_at'] ?? '13:00';

            if ($now->format('H:i') >= $closesAt) {
                return $closed($holiday['message']);
            }

            $notice = "Bugün mesai bitiş saati: {$closesAt}";
        }

        if ($now->isWeekend() && ! $settings->weekend_tickets_allowed) {
            return $closed();
        }

        if (! $settings->allow_tickets_outside_work_hours) {
            $time = $now->format('H:i');

            if ($time < self::time($settings->work_hours_start) || $time >= self::time($settings->work_hours_end)) {
                return $closed();
            }
        }

        return new self(null, $notice ?? null, $workHours, $holiday);
    }

    public function isClosed(): bool
    {
        return $this->closedReason !== null;
    }

    /** Talep kaydı reddedilince gösterilen tek satırlık mesaj. */
    public function message(): ?string
    {
        return $this->closedReason
            ? trim($this->closedReason.($this->workHours ? " Mesai saatleri: {$this->workHours}." : ''))
            : null;
    }

    /** Ör. "Hafta içi 08:00 - 16:30"; mesai dışı talep alınıyorsa saat yazılmaz. */
    private static function workHoursLabel(GeneralSetting $settings): ?string
    {
        if ($settings->allow_tickets_outside_work_hours) {
            return null;
        }

        $range = self::time($settings->work_hours_start).' - '.self::time($settings->work_hours_end);

        return ($settings->weekend_tickets_allowed ? 'Her gün ' : 'Hafta içi ').$range;
    }

    private static function time(mixed $value): string
    {
        return substr((string) $value, 0, 5);
    }
}
