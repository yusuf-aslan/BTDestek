<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Activity;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;

class DashboardOverview extends Widget
{
    private const VISIBLE_CATEGORIES = 4;

    protected string $view = 'filament.widgets.dashboard-overview';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    // Otomatik yenileme Livewire isteğiyle gelir, adres çubuğundaki filtreler o istekte yoktur.
    #[Locked]
    public string $performancePeriod = 'monthly';

    #[Locked]
    public string $performanceSort = 'resolved';

    public function mount(): void
    {
        $period = request()->string('performance_period')->toString();
        $sort = request()->string('performance_sort')->toString();

        $this->performancePeriod = in_array($period, ['monthly', 'all'], true) ? $period : 'monthly';
        $this->performanceSort = in_array($sort, ['resolved', 'activity'], true) ? $sort : 'resolved';
    }

    public function getViewData(): array
    {
        $user = Auth::user();
        $isAdmin = $user?->is_admin;

        // Kapsam sorgusu
        $scopedQuery = fn () => Ticket::query()->visibleTo($user);

        $pendingKeys = \App\Models\TicketStatus::keysFor('pending');
        $solvedKeys = \App\Models\TicketStatus::keysFor('solved');
        $today = today()->toDateString();

        // Ana istatistikler: her kart, sayısını kategori dökümüyle birlikte taşır
        $categoryNames = Category::query()->pluck('name', 'id');
        $card = fn (string $key, string $icon, string $label, string $empty, Builder $query, string $tab, array $filters = []) => [
            'key' => $key,
            'icon' => $icon,
            'label' => $label,
            'empty' => $empty,
            'url' => $this->ticketsUrl($tab, $filters),
            ...$this->categoryBreakdown($query, $categoryNames, $tab, $filters),
        ];

        $cards = [
            $card('total', '📋', 'Toplam Talep', 'Henüz talep yok', $scopedQuery(), 'all'),
            $card('pending', '⏳', 'Bekleyen', 'Bekleyen talep yok', $scopedQuery()->whereIn('status', $pendingKeys), 'pending'),
            $card('today', '📬', 'Bugün Gelen', 'Bugün talep gelmedi', $scopedQuery()->whereDate('created_at', $today), 'all', [
                'created_at' => ['from' => $today, 'until' => $today],
            ]),
            $card('resolved', '✅', 'Çözülen', 'Çözülen talep yok', $scopedQuery()->whereIn('status', $solvedKeys), 'solved'),
            $card('urgent', '🔴', 'Acil & Bekleyen', 'Acil talep yok', $scopedQuery()->where('priority', 'acil')->whereIn('status', $pendingKeys), 'pending', [
                'priority' => ['value' => 'acil'],
            ]),
        ];

        $pendingTickets = $cards[1]['total'];
        $urgentTickets = $cards[4]['total'];
        $urgentUrl = $cards[4]['url'];
        $pendingUrl = $cards[1]['url'];

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

        // Bekleyen talepler: liste sınırlı olduğu için acil olanlar yeni gelenlerin altında kaybolmasın
        $pendingList = (clone $scopedQuery())
            ->whereIn('status', $pendingKeys)
            ->with(['category', 'assignedTo'])
            ->orderByRaw("CASE priority WHEN 'acil' THEN 0 WHEN 'yüksek' THEN 1 ELSE 2 END")
            ->latest()
            ->limit(8)
            ->get();

        // Teknisyen performans (sadece admin)
        $techPerformance = [];
        if ($isAdmin) {
            $isMonthlyPerformance = $this->performancePeriod === 'monthly';
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

            if ($this->performanceSort === 'activity') {
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
            'cards'           => $cards,
            'pendingTickets'  => $pendingTickets,
            'pendingUrl'      => $pendingUrl,
            'urgentTickets'   => $urgentTickets,
            'urgentUrl'       => $urgentUrl,
            'trendData'       => $trendData,
            'trendMax'        => $trendMax ?: 1,
            'pendingList'     => $pendingList,
            'techPerformance' => $techPerformance,
            'announcements'   => $announcements,
        ];
    }

    /**
     * Sorguyu kategoriye göre sayar. Kart taşmasın diye en kalabalık kategoriler
     * satır olarak döner, kalanı "Diğer" altında toplanır.
     *
     * @return array{total: int, rows: list<array{name: string, count: int, url: string}>, others: ?array{count: int, names: string}}
     */
    private function categoryBreakdown(Builder $query, Collection $categoryNames, string $tab, array $filters): array
    {
        $rows = $query
            ->selectRaw('category_id, count(*) as aggregate')
            ->groupBy('category_id')
            ->pluck('aggregate', 'category_id')
            ->map(fn ($count, $categoryId) => [
                'name' => $categoryNames[$categoryId],
                'count' => (int) $count,
                'url' => $this->ticketsUrl($tab, [...$filters, 'category_id' => ['value' => $categoryId]]),
            ])
            ->sortBy([['count', 'desc'], ['name', 'asc']])
            ->values();

        $others = $rows->slice(self::VISIBLE_CATEGORIES);

        return [
            'total' => $rows->sum('count'),
            'rows' => $rows->take(self::VISIBLE_CATEGORIES)->all(),
            'others' => $others->isEmpty() ? null : [
                'count' => $others->sum('count'),
                'names' => $others->map(fn (array $row) => "{$row['name']}: {$row['count']}")->implode(', '),
            ],
        ];
    }

    /** Talep listesini istenen sekme ve filtrelerle açan bağlantı. */
    private function ticketsUrl(string $tab, array $filters = []): string
    {
        return route('filament.admin.resources.tickets.index', array_filter([
            'tab' => $tab,
            'filters' => $filters,
        ]));
    }
}
