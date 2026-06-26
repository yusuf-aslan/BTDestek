<?php

namespace App\Exports;

use App\Models\Activity;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActivitiesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Teknisyen',
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
            $activity->id,
            $activity->user->name ?? '-',
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
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
