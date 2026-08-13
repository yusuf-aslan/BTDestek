<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TicketStatsOverview extends BaseWidget
{
    public static function canView(): bool { return false; }

    protected function getStats(): array
    {
        $user = Auth::user();
        
        // Base query for scoping
        $baseQuery = Ticket::query()->visibleTo($user);

        return [
            Stat::make('Toplam Talep', (clone $baseQuery)->count())
                ->description('Yetkiniz dahilindeki tüm talepler')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Bekleyen Talepler', (clone $baseQuery)->whereIn('status', \App\Models\TicketStatus::keysFor('pending'))->count())
                ->description('İşlem bekleyen')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Bugünkü Talepler', (clone $baseQuery)->whereDate('created_at', today())->count())
                ->description('Son 24 saat')
                ->color('primary'),
        ];
    }
}
