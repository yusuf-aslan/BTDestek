<?php

namespace App\Filament\Pages\Reports\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TicketsPerCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Kategori Bazlı Dağılım';

    protected function getData(): array
    {
        $data = Ticket::select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Talep Sayısı',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6', '#ef4444', '#f59e0b', '#10b981', '#8b5cf6', 
                        '#ec4899', '#6366f1', '#14b8a6', '#f97316', '#06b6d4'
                    ],
                ],
            ],
            'labels' => $data->pluck('category.name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}