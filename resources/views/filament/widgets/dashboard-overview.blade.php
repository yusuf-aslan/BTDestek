{{-- Variables ($isAdmin, $totalTickets, etc.) are injected by Filament from getViewData() --}}

<style>
/* ─── DASHBOARD OVERRIDE ─────────────────────────────────── */
.db-wrap { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; width: 100%; }

/* ── STAT CARDS ──────────────────────────────── */
.db-stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 24px; }
@media (max-width: 1280px) { .db-stats { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px)  { .db-stats { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px)  { .db-stats { grid-template-columns: 1fr; } }

.db-card {
    border-radius: 16px; padding: 20px 22px;
    display: flex; flex-direction: column; gap: 8px;
    position: relative; overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,.10);
    transition: transform .18s ease, box-shadow .18s ease;
}
.db-card:hover { transform: translateY(-3px); box-shadow: 0 8px 32px rgba(0,0,0,.18); }
.db-card::before { content:''; position:absolute; top:-20px; right:-20px; width:80px; height:80px; border-radius:50%; background:rgba(255,255,255,.12); }
.db-card::after  { content:''; position:absolute; top:16px; right:24px; font-size:28px; opacity:.85; }

.db-card-icon { font-size: 28px; line-height: 1; }
.db-card-value { font-size: 34px; font-weight: 800; color: #fff; line-height: 1; }
.db-card-label { font-size: 12px; font-weight: 600; color: rgba(255,255,255,.80); text-transform: uppercase; letter-spacing: .8px; }
.db-card-sub   { font-size: 11px; color: rgba(255,255,255,.60); margin-top: 2px; }

.db-card-total    { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
.db-card-pending  { background: linear-gradient(135deg, #f59e0b, #f97316); }
.db-card-today    { background: linear-gradient(135deg, #3b82f6, #06b6d4); }
.db-card-resolved { background: linear-gradient(135deg, #10b981, #059669); }
.db-card-urgent   { background: linear-gradient(135deg, #ef4444, #dc2626); }

/* Dark mode stat cards */
.dark .db-card-total    { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
.dark .db-card-pending  { background: linear-gradient(135deg, #d97706, #ea580c); }
.dark .db-card-today    { background: linear-gradient(135deg, #2563eb, #0891b2); }
.dark .db-card-resolved { background: linear-gradient(135deg, #059669, #047857); }
.dark .db-card-urgent   { background: linear-gradient(135deg, #dc2626, #b91c1c); }

/* ── CONTENT GRID ──────────────────────────────── */
.db-content { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 24px; }
@media (max-width: 1024px) { .db-content { grid-template-columns: 1fr; } }

/* ── PANELS ──────────────────────────────── */
.db-panel {
    border-radius: 16px; overflow: hidden;
    border: 1px solid rgba(156,163,175,.15);
    background: #fff;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
}
.dark .db-panel { background: #1e2535; border-color: rgba(255,255,255,.08); }

.db-panel-header {
    padding: 16px 20px;
    border-bottom: 1px solid rgba(156,163,175,.12);
    display: flex; align-items: center; gap: 10px;
    background: rgba(99,102,241,.04);
}
.dark .db-panel-header { background: rgba(99,102,241,.08); border-color: rgba(255,255,255,.06); }
.db-panel-title { font-size: 14px; font-weight: 700; color: #374151; letter-spacing: .3px; }
.dark .db-panel-title { color: #e5e7eb; }
.db-panel-badge {
    margin-left: auto; background: #6366f1; color: #fff;
    font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 99px;
}

/* ── TREND CHART ──────────────────────────────── */
.db-trend-wrap { padding: 16px 20px 12px; }
.db-trend-bars { display: flex; align-items: flex-end; gap: 8px; height: 100px; }
.db-trend-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; }
.db-trend-bar-wrap { flex: 1; width: 100%; display: flex; flex-direction: column; justify-content: flex-end; gap: 2px; }
.db-trend-bar {
    border-radius: 6px 6px 0 0;
    transition: height .4s ease;
    min-height: 2px;
}
.db-trend-bar-new      { background: linear-gradient(180deg, #6366f1, #8b5cf6); }
.db-trend-bar-resolved { background: linear-gradient(180deg, #10b981, #059669); opacity: .7; }
.dark .db-trend-bar-new      { background: linear-gradient(180deg, #818cf8, #a78bfa); }
.dark .db-trend-bar-resolved { background: linear-gradient(180deg, #34d399, #10b981); }
.db-trend-label { font-size: 10px; color: #9ca3af; font-weight: 500; white-space: nowrap; }
.db-trend-legend { display: flex; gap: 16px; padding: 10px 0 0; }
.db-trend-legend-item { display: flex; align-items: center; gap: 5px; font-size: 11px; color: #6b7280; }
.dark .db-trend-legend-item { color: #9ca3af; }
.db-trend-legend-dot { width: 10px; height: 10px; border-radius: 3px; }

/* ── TICKET LIST ──────────────────────────────── */
.db-ticket-item {
    display: flex; align-items: center; gap: 10px;
    padding: 11px 20px;
    border-bottom: 1px solid rgba(156,163,175,.08);
    transition: background .15s;
}
.db-ticket-item:last-child { border-bottom: none; }
.db-ticket-item:hover { background: rgba(99,102,241,.05); }
.dark .db-ticket-item:hover { background: rgba(99,102,241,.10); }

.db-ticket-priority {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
    box-shadow: 0 0 0 3px rgba(0,0,0,.05);
}
.db-priority-acil    { background: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,.15); }
.db-priority-yüksek  { background: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.15); }
.db-priority-orta    { background: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
.db-priority-düşük   { background: #9ca3af; box-shadow: 0 0 0 3px rgba(156,163,175,.15); }

.db-ticket-no {
    font-size: 11px; font-weight: 700; color: #6366f1;
    background: rgba(99,102,241,.08); padding: 2px 7px; border-radius: 6px;
    text-decoration: none; white-space: nowrap; flex-shrink: 0;
    transition: background .15s;
}
.dark .db-ticket-no { color: #818cf8; background: rgba(99,102,241,.15); }
.db-ticket-no:hover { background: rgba(99,102,241,.18); text-decoration: none; }

.db-ticket-subject { font-size: 13px; color: #374151; flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.dark .db-ticket-subject { color: #d1d5db; }

.db-ticket-dept { font-size: 11px; color: #9ca3af; white-space: nowrap; flex-shrink: 0; }

.db-ticket-badge {
    font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 99px;
    white-space: nowrap; flex-shrink: 0;
}
.db-badge-acil    { background: rgba(239,68,68,.12); color: #dc2626; }
.dark .db-badge-acil    { background: rgba(239,68,68,.20); color: #f87171; }
.db-badge-yüksek  { background: rgba(245,158,11,.12); color: #d97706; }
.dark .db-badge-yüksek  { background: rgba(245,158,11,.20); color: #fbbf24; }
.db-badge-orta    { background: rgba(59,130,246,.12); color: #2563eb; }
.dark .db-badge-orta    { background: rgba(59,130,246,.20); color: #60a5fa; }
.db-badge-düşük   { background: rgba(156,163,175,.12); color: #6b7280; }
.dark .db-badge-düşük   { background: rgba(156,163,175,.20); color: #9ca3af; }

/* ── TECHNICIAN TABLE ──────────────────────────────── */
.db-tech-table { width: 100%; border-collapse: collapse; }
.db-tech-th { padding: 10px 16px; font-size: 11px; font-weight: 700; color: #9ca3af; text-align: left; text-transform: uppercase; letter-spacing: .6px; border-bottom: 1px solid rgba(156,163,175,.12); }
.dark .db-tech-th { color: #6b7280; border-color: rgba(255,255,255,.06); }
.db-tech-tr { transition: background .15s; }
.db-tech-tr:hover td { background: rgba(99,102,241,.04); }
.dark .db-tech-tr:hover td { background: rgba(99,102,241,.08); }
.db-tech-td { padding: 10px 16px; font-size: 13px; color: #374151; border-bottom: 1px solid rgba(156,163,175,.06); }
.dark .db-tech-td { color: #d1d5db; border-color: rgba(255,255,255,.04); }
.db-tech-name { font-weight: 600; display: flex; align-items: center; gap: 8px; }
.db-tech-avatar {
    width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: #fff; flex-shrink: 0;
}
.db-tech-stat { display: inline-flex; align-items: center; justify-content: center; min-width: 28px; padding: 2px 8px; border-radius: 99px; font-size: 12px; font-weight: 700; }
.db-tech-stat-active   { background: rgba(245,158,11,.12); color: #d97706; }
.dark .db-tech-stat-active   { background: rgba(245,158,11,.20); color: #fbbf24; }
.db-tech-stat-today    { background: rgba(16,185,129,.12); color: #059669; }
.dark .db-tech-stat-today    { background: rgba(16,185,129,.20); color: #34d399; }
.db-tech-stat-resolved { background: rgba(99,102,241,.12); color: #4f46e5; }
.dark .db-tech-stat-resolved { background: rgba(99,102,241,.20); color: #818cf8; }

/* ── ANNOUNCEMENTS ──────────────────────────────── */
.db-announce-item { padding: 14px 20px; border-bottom: 1px solid rgba(156,163,175,.08); }
.db-announce-item:last-child { border-bottom: none; }
.db-announce-type { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 99px; display: inline-block; margin-bottom: 4px; }
.db-announce-info    { background: rgba(59,130,246,.12); color: #2563eb; }
.dark .db-announce-info    { background: rgba(59,130,246,.20); color: #60a5fa; }
.db-announce-warning { background: rgba(245,158,11,.12); color: #d97706; }
.dark .db-announce-warning { background: rgba(245,158,11,.20); color: #fbbf24; }
.db-announce-danger  { background: rgba(239,68,68,.12); color: #dc2626; }
.dark .db-announce-danger  { background: rgba(239,68,68,.20); color: #f87171; }
.db-announce-title { font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 2px; }
.dark .db-announce-title { color: #e5e7eb; }
.db-announce-content { font-size: 12px; color: #6b7280; line-height: 1.5; }
.dark .db-announce-content { color: #9ca3af; }

/* ── EMPTY STATE ──────────────────────────────── */
.db-empty { text-align: center; padding: 32px 20px; }
.db-empty-icon { font-size: 36px; margin-bottom: 8px; opacity: .5; }
.db-empty-text { font-size: 13px; color: #9ca3af; }

/* ── FULL ROW PANELS ──────────────────────────────── */
.db-full-row { margin-bottom: 16px; }

/* ── SECTION TITLE ──────────────────────────────── */
.db-section-title {
    font-size: 13px; font-weight: 700; color: #374151;
    margin: 0 0 14px; display: flex; align-items: center; gap: 8px;
}
.dark .db-section-title { color: #e5e7eb; }
.db-section-line { flex: 1; height: 1px; background: rgba(156,163,175,.15); }
</style>

<div class="db-wrap">

    {{-- ── STAT CARDS ────────────────────────────────────────── --}}
    <div class="db-stats">

        <div class="db-card db-card-total">
            <div class="db-card-icon">📋</div>
            <div class="db-card-value">{{ $totalTickets }}</div>
            <div class="db-card-label">Toplam Talep</div>
            <div class="db-card-sub">Yetkiniz dahilindeki tümü</div>
        </div>

        <div class="db-card db-card-pending">
            <div class="db-card-icon">⏳</div>
            <div class="db-card-value">{{ $pendingTickets }}</div>
            <div class="db-card-label">Bekleyen</div>
            <div class="db-card-sub">İşlem gerektiriyor</div>
        </div>

        <div class="db-card db-card-today">
            <div class="db-card-icon">📬</div>
            <div class="db-card-value">{{ $todayTickets }}</div>
            <div class="db-card-label">Bugün Gelen</div>
            <div class="db-card-sub">Son 24 saat</div>
        </div>

        <div class="db-card db-card-resolved">
            <div class="db-card-icon">✅</div>
            <div class="db-card-value">{{ $resolvedTickets }}</div>
            <div class="db-card-label">Çözülen</div>
            <div class="db-card-sub">Toplam kapatılan</div>
        </div>

        <div class="db-card db-card-urgent">
            <div class="db-card-icon">🔴</div>
            <div class="db-card-value">{{ $urgentTickets }}</div>
            <div class="db-card-label">Acil & Bekleyen</div>
            <div class="db-card-sub">Öncelikli talepler</div>
        </div>

    </div>

    {{-- ── ACİL UYARILAR ───────────────────────────────────────── --}}
    @if($urgentTickets > 0)
    <div style="background:linear-gradient(135deg,rgba(239,68,68,.08),rgba(239,68,68,.04));border:1px solid rgba(239,68,68,.25);border-radius:12px;padding:12px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
        <span style="font-size:20px;">🚨</span>
        <span style="font-size:13px;font-weight:600;color:#dc2626;">{{ $urgentTickets }} adet <strong>acil</strong> talep işlem bekliyor!</span>
        <a href="{{ route('filament.admin.resources.tickets.index', ['tableFilters[status][values][0]' => 'bekliyor','tableFilters[priority][value]' => 'acil']) }}" style="margin-left:auto;font-size:12px;font-weight:600;color:#dc2626;text-decoration:none;padding:4px 12px;border:1px solid rgba(239,68,68,.35);border-radius:8px;transition:background .15s;" onmouseover="this.style.background='rgba(239,68,68,.08)'" onmouseout="this.style.background='transparent'">Görüntüle →</a>
    </div>
    @endif

    {{-- ── AKTİF DUYURULAR ─────────────────────────────────────── --}}
    @if($announcements->count() > 0)
    <div class="db-panel db-full-row">
        <div class="db-panel-header">
            <span>📢</span>
            <span class="db-panel-title">Aktif Duyurular</span>
            <span class="db-panel-badge">{{ $announcements->count() }}</span>
        </div>
        @foreach($announcements as $ann)
        <div class="db-announce-item">
            <div class="db-announce-type db-announce-{{ $ann->type }}">{{ strtoupper($ann->type) }}</div>
            <div class="db-announce-title">{{ $ann->title }}</div>
            <div class="db-announce-content">{{ $ann->content }}</div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── ANA İÇERİK: TALEP LİSTESİ + TREND ──────────────────── --}}
    <div class="db-content">

        {{-- Bekleyen Talepler --}}
        <div class="db-panel">
            <div class="db-panel-header">
                <span>⏳</span>
                <span class="db-panel-title">Bekleyen Talepler</span>
                @if($pendingList->count() > 0)
                <span class="db-panel-badge">{{ $pendingList->count() }}</span>
                @endif
            </div>
            @if($pendingList->isEmpty())
                <div class="db-empty">
                    <div class="db-empty-icon">🎉</div>
                    <div class="db-empty-text">Bekleyen talep yok, harika!</div>
                </div>
            @else
                @foreach($pendingList as $ticket)
                <div class="db-ticket-item">
                    <div class="db-ticket-priority db-priority-{{ $ticket->priority }}"></div>
                    <a href="{{ route('filament.admin.resources.tickets.edit', $ticket) }}"
                       class="db-ticket-no">{{ $ticket->tracking_number }}</a>
                    <div class="db-ticket-subject" title="{{ $ticket->subject }}">{{ $ticket->subject }}</div>
                    @if($ticket->department_room)
                    <div class="db-ticket-dept">{{ Str::limit($ticket->department_room, 20) }}</div>
                    @endif
                    <span class="db-ticket-badge db-badge-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
                </div>
                @endforeach
            @endif
        </div>

        {{-- Sağ: Trend + (Duyurular varsa boş, varsa) --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- 7 Günlük Trend --}}
            <div class="db-panel">
                <div class="db-panel-header">
                    <span>📈</span>
                    <span class="db-panel-title">Son 7 Günlük Talep Trendi</span>
                </div>
                <div class="db-trend-wrap">
                    <div class="db-trend-bars">
                        @foreach($trendData as $day)
                        @php
                            $newH  = $trendMax > 0 ? round(($day['count']    / $trendMax) * 80) : 0;
                            $resH  = $trendMax > 0 ? round(($day['resolved'] / $trendMax) * 80) : 0;
                        @endphp
                        <div class="db-trend-col">
                            <div class="db-trend-bar-wrap">
                                <div class="db-trend-bar db-trend-bar-new"
                                     style="height:{{ max($newH,2) }}px;"
                                     title="Gelen: {{ $day['count'] }}"></div>
                                <div class="db-trend-bar db-trend-bar-resolved"
                                     style="height:{{ max($resH,1) }}px;"
                                     title="Çözülen: {{ $day['resolved'] }}"></div>
                            </div>
                            <div class="db-trend-label">{{ $day['label'] }}</div>
                        </div>
                        @endforeach
                    </div>
                    <div class="db-trend-legend">
                        <div class="db-trend-legend-item">
                            <div class="db-trend-legend-dot" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);"></div>
                            <span>Gelen Talep</span>
                        </div>
                        <div class="db-trend-legend-item">
                            <div class="db-trend-legend-dot" style="background:linear-gradient(135deg,#10b981,#059669);"></div>
                            <span>Çözülen</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Özet mini kutu --}}
            <div class="db-panel" style="padding: 16px 20px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    @php
                        $weekTotal   = array_sum(array_column($trendData,'count'));
                        $weekResolved = array_sum(array_column($trendData,'resolved'));
                        $rate = $weekTotal > 0 ? round(($weekResolved / $weekTotal) * 100) : 0;
                    @endphp
                    <div style="text-align:center;padding:12px;background:rgba(99,102,241,.06);border-radius:10px;">
                        <div style="font-size:22px;font-weight:800;color:#6366f1;">{{ $weekTotal }}</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:2px;">Bu Hafta Gelen</div>
                    </div>
                    <div style="text-align:center;padding:12px;background:rgba(16,185,129,.06);border-radius:10px;">
                        <div style="font-size:22px;font-weight:800;color:#10b981;">{{ $rate }}%</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:2px;">Çözüm Oranı</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── TEKNİSYEN PERFORMANS ─────────────────────────────────── --}}
    @if($isAdmin && $techPerformance->count() > 0)
    <div class="db-panel db-full-row" style="margin-bottom:16px;">
        <div class="db-panel-header">
            <span>👤</span>
            <span class="db-panel-title">Teknisyen Performans Tablosu</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="db-tech-table">
                <thead>
                    <tr>
                        <th class="db-tech-th" style="width:36px;">#</th>
                        <th class="db-tech-th">Teknisyen</th>
                        <th class="db-tech-th" style="text-align:center;">Aktif İşler</th>
                        <th class="db-tech-th" style="text-align:center;">Bugün Çözülen</th>
                        <th class="db-tech-th" style="text-align:center;">Toplam Çözülen</th>
                        <th class="db-tech-th" style="text-align:center;">Doluluk</th>
                    </tr>
                </thead>
                <tbody>
                    @php $techMax = $techPerformance->max('total_resolved') ?: 1; @endphp
                    @foreach($techPerformance as $i => $tech)
                    <tr class="db-tech-tr">
                        <td class="db-tech-td" style="color:#9ca3af;font-size:12px;">{{ $i+1 }}</td>
                        <td class="db-tech-td">
                            <div class="db-tech-name">
                                <div class="db-tech-avatar">{{ strtoupper(substr($tech->name,0,1)) }}</div>
                                {{ $tech->name }}
                            </div>
                        </td>
                        <td class="db-tech-td" style="text-align:center;">
                            <span class="db-tech-stat db-tech-stat-active">{{ $tech->active_tickets }}</span>
                        </td>
                        <td class="db-tech-td" style="text-align:center;">
                            <span class="db-tech-stat db-tech-stat-today">{{ $tech->resolved_today }}</span>
                        </td>
                        <td class="db-tech-td" style="text-align:center;">
                            <span class="db-tech-stat db-tech-stat-resolved">{{ $tech->total_resolved }}</span>
                        </td>
                        <td class="db-tech-td" style="min-width:120px;">
                            @php $pct = $techMax > 0 ? round(($tech->total_resolved / $techMax) * 100) : 0; @endphp
                            <div style="background:rgba(156,163,175,.15);border-radius:99px;height:6px;overflow:hidden;">
                                <div style="background:linear-gradient(90deg,#6366f1,#8b5cf6);height:100%;width:{{ $pct }}%;border-radius:99px;transition:width .4s ease;"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
