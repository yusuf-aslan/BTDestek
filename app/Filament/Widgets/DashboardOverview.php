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
        $pendingTickets = (clone $scopedQuery())->whereIn('status', \App\Models\TicketStatus::keysFor('pending'))->count();
        $todayTickets   = (clone $scopedQuery())->whereDate('created_at', today())->count();
        $resolvedTickets = (clone $scopedQuery())->whereIn('status', \App\Models\TicketStatus::keysFor('solved'))->count();
        $urgentTickets  = (clone $scopedQuery())->where('priority', 'acil')->whereIn('status', \App\Models\TicketStatus::keysFor('pending'))->count();

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
            ->whereIn('status', \App\Models\TicketStatus::keysFor('pending'))
            ->with(['category.department', 'assignedTo'])
            ->latest()
            ->limit(8)
            ->get();

        // Teknisyen performans (sadece admin)
        $techPerformance = [];
        if ($isAdmin) {
            $requestedPeriod = request()->string('performance_period')->toString();
            $performancePeriod = in_array($requestedPeriod, ['monthly', 'all'], true)
                ? $requestedPeriod
                : 'monthly';
            $requestedSort = request()->string('performance_sort')->toString();
            $performanceSort = in_array($requestedSort, ['resolved', 'activity'], true)
                ? $requestedSort
                : 'resolved';
            $isMonthlyPerformance = $performancePeriod === 'monthly';
            $monthStart = now()->startOfMonth()->toDateString();
            $monthEnd = now()->endOfMonth()->toDateString();

            $techPerformanceQuery = User::query()
                ->whereHas('categories')
                ->withCount([
                    'tickets as active_tickets' => fn (Builder $q) => $q->whereIn('status', \App\Models\TicketStatus::keysFor('pending')),
                    'resolvedTickets as resolved_today' => fn (Builder $q) => $q->whereIn('status', \App\Models\TicketStatus::keysFor('solved'))->whereDate('resolved_at', today()),
                    'resolvedTickets as total_resolved' => fn (Builder $q) => $q
                        ->whereIn('status', \App\Models\TicketStatus::keysFor('solved'))
                        ->when($isMonthlyPerformance, fn (Builder $query) => $query->whereBetween('resolved_at', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])),
                    'activities as period_activity_count' => fn (Builder $q) => $q
                        ->when($isMonthlyPerformance, fn (Builder $query) => $query->whereBetween('activity_date', [$monthStart, $monthEnd])),
                ])
                ->withSum([
                    'activities as period_activity_minutes' => fn (Builder $q) => $q
                        ->when($isMonthlyPerformance, fn (Builder $query) => $query->whereBetween('activity_date', [$monthStart, $monthEnd])),
                ], 'duration');

            if ($performanceSort === 'activity') {
                $techPerformanceQuery
                    ->orderByDesc('period_activity_minutes')
                    ->orderByDesc('total_resolved');
            } else {
                $techPerformanceQuery->orderByDesc('total_resolved');
            }

            $techPerformance = $techPerformanceQuery
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
            'performancePeriod' => $performancePeriod ?? 'monthly',
            'performanceSort' => $performanceSort ?? 'resolved',
            'announcements'   => $announcements,
        ];
    }
}
