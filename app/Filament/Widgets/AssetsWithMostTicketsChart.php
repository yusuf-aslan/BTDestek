<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;

class AssetsWithMostTicketsChart extends ChartWidget
{
    protected ?string $heading = 'En Çok Arıza Kaydı Açılan Envanterler';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Asset::query()
            ->withCount('tickets')
            ->orderByDesc('tickets_count')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Talep Sayısı',
                    'data' => $data->pluck('tickets_count')->toArray(),
                    'backgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}