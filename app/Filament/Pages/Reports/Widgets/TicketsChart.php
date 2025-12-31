<?php

namespace App\Filament\Pages\Reports\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TicketsChart extends ChartWidget
{
    protected ?string $heading = 'Son 30 Günün Talep Grafiği';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Ticket::select(DB::raw('date(created_at) as date'), DB::raw('count(*) as aggregate'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Talep Sayısı',
                    'data' => $data->pluck('aggregate')->toArray(),
                    'fill' => 'start',
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
