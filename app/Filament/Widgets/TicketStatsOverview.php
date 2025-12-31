<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TicketStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        
        // Base query for scoping
        $baseQuery = Ticket::query()->where(function (Builder $query) use ($user) {
            if (!$user->is_admin) {
                $query->whereHas('category', function (Builder $q) use ($user) {
                    $q->whereHas('users', function (Builder $sq) use ($user) {
                        $sq->where('users.id', $user->id);
                    });
                });
            }
        });

        return [
            Stat::make('Toplam Talep', (clone $baseQuery)->count())
                ->description('Yetkiniz dahilindeki tüm talepler')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Bekleyen Talepler', (clone $baseQuery)->whereNotIn('status', ['çözüldü', 'iptal'])->count())
                ->description('İşlem bekleyen')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Bugünkü Talepler', (clone $baseQuery)->whereDate('created_at', today())->count())
                ->description('Son 24 saat')
                ->color('primary'),
        ];
    }
}
