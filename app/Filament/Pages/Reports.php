<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Activity;
use App\Exports\TechnicianReportExport;
use Filament\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;

class Reports extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $title = 'Raporlar ve İstatistikler';

    protected static ?string $navigationLabel = 'Raporlar';

    protected static string|\UnitEnum|null $navigationGroup = 'Ayarlar';
    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.reports';

    // --- Filtreler ---
    public ?int $selectedUserId = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    // --- Aktif sekme ---
    public string $activeTab = 'tickets';

    public static function canAccess(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    // ----------------------------------------------------------------
    // Computed: Seçili kullanıcı
    // ----------------------------------------------------------------
    public function getSelectedUserProperty(): ?User
    {
        if (!$this->selectedUserId) return null;
        return User::find($this->selectedUserId);
    }

    // ----------------------------------------------------------------
    // Özet kartlar için hesaplamalar
    // ----------------------------------------------------------------
    public function getStatsProperty(): array
    {
        if (!$this->selectedUserId) {
            return [
                'resolved_tickets'    => '-',
                'total_tickets'       => '-',
                'avg_resolution_mins' => '-',
                'activity_count'      => '-',
                'activity_total_mins' => '-',
            ];
        }

        // Çözülen talepler (resolved_by)
        $resolvedQuery = Ticket::where('resolved_by', $this->selectedUserId)
            ->where('status', 'çözüldü')
            ->when($this->dateFrom, fn ($q) => $q->whereDate('resolved_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn ($q) => $q->whereDate('resolved_at', '<=', $this->dateTo));

        $resolvedCount = $resolvedQuery->count();

        // Atanan talepler (assigned_to) — tüm durumlar
        $assignedCount = Ticket::where('assigned_to', $this->selectedUserId)
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->count();

        // Ortalama çözüm süresi (dakika cinsinden)
        $avgMins = Ticket::where('resolved_by', $this->selectedUserId)
            ->where('status', 'çözüldü')
            ->whereNotNull('resolved_at')
            ->when($this->dateFrom, fn ($q) => $q->whereDate('resolved_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn ($q) => $q->whereDate('resolved_at', '<=', $this->dateTo))
            ->get()
            ->avg(fn ($t) => $t->created_at->diffInMinutes($t->resolved_at));

        // Faaliyetler
        $activityQuery = Activity::where('user_id', $this->selectedUserId)
            ->when($this->dateFrom, fn ($q) => $q->whereDate('activity_date', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn ($q) => $q->whereDate('activity_date', '<=', $this->dateTo));

        $activityCount    = $activityQuery->count();
        $activityTotalMin = $activityQuery->sum('duration');

        // Dakika → saat:dakika formatlama
        $formatMins = function (?float $mins): string {
            if ($mins === null || $mins == 0) return '-';
            $h = floor($mins / 60);
            $m = round($mins % 60);
            return $h > 0 ? "{$h}s {$m}dk" : "{$m} dk";
        };

        return [
            'resolved_tickets'    => $resolvedCount,
            'total_tickets'       => $assignedCount,
            'avg_resolution_mins' => $formatMins($avgMins),
            'activity_count'      => $activityCount,
            'activity_total_mins' => $formatMins($activityTotalMin),
        ];
    }

    // ----------------------------------------------------------------
    // Talepler tablosu verisi
    // ----------------------------------------------------------------
    public function getTicketsProperty()
    {
        if (!$this->selectedUserId) return collect();

        return Ticket::where('resolved_by', $this->selectedUserId)
            ->where('status', 'çözüldü')
            ->when($this->dateFrom, fn ($q) => $q->whereDate('resolved_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn ($q) => $q->whereDate('resolved_at', '<=', $this->dateTo))
            ->with('category.department')
            ->orderByDesc('resolved_at')
            ->get();
    }

    // ----------------------------------------------------------------
    // Faaliyetler tablosu verisi
    // ----------------------------------------------------------------
    public function getActivitiesProperty()
    {
        if (!$this->selectedUserId) return collect();

        return Activity::where('user_id', $this->selectedUserId)
            ->when($this->dateFrom, fn ($q) => $q->whereDate('activity_date', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn ($q) => $q->whereDate('activity_date', '<=', $this->dateTo))
            ->orderByDesc('activity_date')
            ->get();
    }

    // ----------------------------------------------------------------
    // Kullanıcı listesi (dropdown için)
    // ----------------------------------------------------------------
    public function getUsersProperty()
    {
        return User::orderBy('name')->pluck('name', 'id');
    }

    // ----------------------------------------------------------------
    // Excel Export
    // ----------------------------------------------------------------
    public function exportReport()
    {
        if (!$this->selectedUserId) return;

        $user = User::find($this->selectedUserId);
        $filename = 'teknisyen-raporu-' . \Illuminate\Support\Str::slug($user->name) . '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new TechnicianReportExport($user, $this->dateFrom, $this->dateTo),
            $filename
        );
    }

    // ----------------------------------------------------------------
    // Genel istatistik kartları (kullanıcıdan bağımsız, sayfanın altı)
    // ----------------------------------------------------------------
    public function getGeneralStatsProperty(): array
    {
        return [
            'total_tickets'     => Ticket::count(),
            'open_tickets'      => Ticket::whereIn('status', ['yeni', 'işlemde', 'beklemede'])->count(),
            'resolved_tickets'  => Ticket::where('status', 'çözüldü')->count(),
            'total_activities'  => Activity::count(),
        ];
    }
}
