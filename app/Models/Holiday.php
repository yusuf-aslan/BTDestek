<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Holiday extends Model
{
    /**
     * Her yıl aynı güne denk gelen resmi tatiller (ay-gün).
     * Dini bayramlar ve idari izinler her yıl Tatil Günleri ekranından girilir.
     */
    public const OFFICIAL = [
        '01-01' => ['name' => 'Yılbaşı'],
        '04-23' => ['name' => 'Ulusal Egemenlik ve Çocuk Bayramı'],
        '05-01' => ['name' => 'Emek ve Dayanışma Günü'],
        '05-19' => ['name' => 'Atatürk\'ü Anma, Gençlik ve Spor Bayramı'],
        '07-15' => ['name' => 'Demokrasi ve Millî Birlik Günü'],
        '08-30' => ['name' => 'Zafer Bayramı'],
        '10-28' => ['name' => 'Cumhuriyet Bayramı Arifesi', 'closes_at' => '13:00'],
        '10-29' => ['name' => 'Cumhuriyet Bayramı'],
    ];

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_half_day',
        'closes_at',
        'message',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_half_day' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Holiday $holiday) {
            $holiday->end_date ??= $holiday->start_date;

            if (! $holiday->is_half_day) {
                $holiday->closes_at = null;
            }
        });
    }

    /**
     * Verilen günün tatil bilgisi; tatil değilse null.
     * Girilen kayıt aynı günkü resmi tatilin önüne geçer (ör. arifenin kapanış saati değiştirilebilir).
     *
     * @return array{name: string, is_half_day: bool, closes_at: ?string, message: ?string}|null
     */
    public static function forDate(CarbonInterface $date, bool $includeOfficial = true): ?array
    {
        $day = $date->toDateString();

        $holiday = static::query()
            ->whereDate('start_date', '<=', $day)
            ->whereDate('end_date', '>=', $day)
            ->orderByDesc('start_date')
            ->first();

        if ($holiday) {
            return [
                'name' => $holiday->name,
                'is_half_day' => $holiday->is_half_day,
                'closes_at' => $holiday->is_half_day && $holiday->closes_at ? substr($holiday->closes_at, 0, 5) : null,
                'message' => $holiday->message,
            ];
        }

        $official = $includeOfficial ? (self::OFFICIAL[$date->format('m-d')] ?? null) : null;

        return $official ? [
            'name' => $official['name'],
            'is_half_day' => isset($official['closes_at']),
            'closes_at' => $official['closes_at'] ?? null,
            'message' => null,
        ] : null;
    }

    /** Ayarlar ve liste ekranında gösterilecek resmi tatiller, ilgili yılın tarihleriyle. */
    public static function officialFor(int $year): array
    {
        return collect(self::OFFICIAL)
            ->map(fn (array $holiday, string $monthDay) => $holiday + ['date' => Carbon::parse("{$year}-{$monthDay}")])
            ->values()
            ->all();
    }

    public function dateRangeLabel(): string
    {
        $start = $this->start_date->translatedFormat('j F Y');

        return $this->end_date && ! $this->end_date->isSameDay($this->start_date)
            ? $start.' - '.$this->end_date->translatedFormat('j F Y')
            : $start;
    }
}
