<?php

namespace App\Filament\Pages\Reports\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TicketsPerDepartmentChart extends ChartWidget
{
    protected ?string $heading = 'Bölüm Bazlı Talep Dağılımı';

    protected function getData(): array
    {
        $data = Ticket::query()
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->join('departments', 'categories.department_id', '=', 'departments.id')
            ->select('departments.name', DB::raw('count(tickets.id) as total'))
            ->groupBy('departments.name')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Talep Sayısı',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', 
                        '#ec4899', '#6366f1', '#14b8a6', '#f97316', '#06b6d4'
                    ],
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
