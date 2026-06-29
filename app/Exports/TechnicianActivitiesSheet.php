<?php

namespace App\Exports;

use App\Models\Activity;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Builder;

class TechnicianActivitiesSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected User $user,
        protected ?string $dateFrom,
        protected ?string $dateTo,
    ) {}

    public function title(): string
    {
        return 'Faaliyetler';
    }

    public function query(): Builder
    {
        return Activity::query()
            ->where('user_id', $this->user->id)
            ->when($this->dateFrom, fn ($q) => $q->whereDate('activity_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('activity_date', '<=', $this->dateTo))
            ->orderBy('activity_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'Faaliyet Tarihi',
            'Faaliyet Türü',
            'Harcanan Süre (Dakika)',
            'Bölüm / Oda',
            'Yapılan İşin Açıklaması',
            'Kayıt Tarihi',
        ];
    }

    public function map($activity): array
    {
        return [
            $activity->activity_date ? $activity->activity_date->format('d.m.Y') : '-',
            $activity->activity_type,
            $activity->duration . ' dk',
            $activity->department,
            $activity->description,
            $activity->created_at ? $activity->created_at->format('d.m.Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
