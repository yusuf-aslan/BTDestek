<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Activity;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class DashboardOverview extends Widget
{
    protected string $view = 'filament.widgets.dashboard-overview';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    public function getViewData(): array
    {
        $user = Auth::user();
        $isAdmin = $user?->is_admin;

        // Kapsam sorgusu
        $scopedQuery = function () use ($user, $isAdmin) {
            $q = Ticket::query();
            if (!$isAdmin) {
                $q->whereHas('category', function (Builder $cq) use ($user) {
                    $cq->whereHas('users', fn (Builder $uq) => $uq->where('users.id', $user->id));
                });
            }
            return $q;
        };

        // Ana istatistikler
        $totalTickets   = (clone $scopedQuery())->count();
        $pendingTickets = (clone $scopedQuery())->whereNotIn('status', ['çözüldü', 'iptal'])->count();
        $todayTickets   = (clone $scopedQuery())->whereDate('created_at', today())->count();
        $resolvedTickets = (clone $scopedQuery())->where('status', 'çözüldü')->count();
        $urgentTickets  = (clone $scopedQuery())->where('priority', 'acil')->whereNotIn('status', ['çözüldü', 'iptal'])->count();

        // Son 7 günün trendleri
        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trendData[] = [
                'label' => $date->format('d.m'),
                'count' => (clone $scopedQuery())->whereDate('created_at', $date->format('Y-m-d'))->count(),
                'resolved' => (clone $scopedQuery())->whereDate('resolved_at', $date->format('Y-m-d'))->count(),
            ];
        }
        $trendMax = max(array_column($trendData, 'count') ?: [1]);

        // Bekleyen talepler (en son 8 adet)
        $pendingList = (clone $scopedQuery())
            ->whereNotIn('status', ['çözüldü', 'iptal'])
            ->with(['category.department', 'assignedTo'])
            ->latest()
            ->limit(8)
            ->get();

        // Teknisyen performans (sadece admin)
        $techPerformance = [];
        if ($isAdmin) {
            $techPerformance = User::query()
                ->whereHas('categories')
                ->withCount([
                    'tickets as active_tickets' => fn (Builder $q) => $q->whereNotIn('status', ['çözüldü', 'iptal']),
                    'resolvedTickets as resolved_today' => fn (Builder $q) => $q->where('status', 'çözüldü')->whereDate('resolved_at', today()),
                    'resolvedTickets as total_resolved' => fn (Builder $q) => $q->where('status', 'çözüldü'),
                ])
                ->orderByDesc('total_resolved')
                ->limit(10)
                ->get();
        }

        // Aktif duyurular
        $announcements = \App\Models\Announcement::where('is_active', true)->get();

        return [
            'isAdmin'         => $isAdmin,
            'totalTickets'    => $totalTickets,
            'pendingTickets'  => $pendingTickets,
            'todayTickets'    => $todayTickets,
            'resolvedTickets' => $resolvedTickets,
            'urgentTickets'   => $urgentTickets,
            'trendData'       => $trendData,
            'trendMax'        => $trendMax ?: 1,
            'pendingList'     => $pendingList,
            'techPerformance' => $techPerformance,
            'announcements'   => $announcements,
        ];
    }
}
