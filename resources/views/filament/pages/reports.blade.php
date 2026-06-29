<x-filament-panels::page>

{{-- ============================================================
     TEMA UYUMLU STİLLER (Light + Dark)
============================================================ --}}
<style>
    /* --- Filtre Kutusu --- */
    .rp-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .dark .rp-card {
        background: #1e293b;
        border-color: #334155;
        box-shadow: 0 1px 4px rgba(0,0,0,0.3);
    }

    /* --- Filtre Label --- */
    .rp-label {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        margin-bottom: 0.35rem;
    }
    .dark .rp-label { color: #94a3b8; }

    /* --- Filtre Section Başlık --- */
    .rp-section-title {
        font-size: 0.7rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.75rem;
    }
    .dark .rp-section-title { color: #64748b; }

    /* --- Input / Select --- */
    .rp-input {
        display: block;
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: #0f172a;
        background: #f8fafc;
        outline: none;
        transition: border-color 0.15s;
        box-sizing: border-box;
    }
    .rp-input:focus { border-color: #0ea5e9; }
    .dark .rp-input {
        background: #0f172a;
        border-color: #475569;
        color: #e2e8f0;
    }
    .dark .rp-input:focus { border-color: #38bdf8; }

    /* --- Tablo Kartı --- */
    .rp-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .dark .rp-table-card {
        background: #1e293b;
        border-color: #334155;
    }

    /* --- Tablo Başlık --- */
    .rp-th {
        padding: 0.65rem 1rem;
        text-align: left;
        font-weight: 700;
        color: #475569;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .dark .rp-th {
        background: #0f172a;
        color: #94a3b8;
        border-bottom-color: #334155;
    }

    /* --- Tablo Satır (çift) --- */
    .rp-tr-even { background: #fafafa; }
    .dark .rp-tr-even { background: #172033; }

    /* --- Tablo Satır (tek) --- */
    .rp-tr-odd { background: #ffffff; }
    .dark .rp-tr-odd { background: #1e293b; }

    /* --- Tablo Satır Hover --- */
    .rp-tr:hover { opacity: 0.9; }

    /* --- Tablo Hücre --- */
    .rp-td {
        padding: 0.65rem 1rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.8125rem;
    }
    .dark .rp-td {
        color: #cbd5e1;
        border-bottom-color: #263348;
    }
    .rp-td-muted { color: #64748b; }
    .dark .rp-td-muted { color: #94a3b8; }

    /* --- Tablo Footer --- */
    .rp-tfoot-tr {
        background: #f1f5f9;
        border-top: 2px solid #e2e8f0;
    }
    .dark .rp-tfoot-tr {
        background: #0f172a;
        border-top-color: #334155;
    }
    .rp-tfoot-label { padding: 0.65rem 1rem; font-weight: 700; color: #334155; font-size: 0.8125rem; }
    .dark .rp-tfoot-label { color: #e2e8f0; }

    /* --- Sekme Konteyneri --- */
    .rp-tabs-bar {
        display: flex;
        gap: 0;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 1.25rem;
    }
    .dark .rp-tabs-bar { border-bottom-color: #334155; }

    /* --- Sekme Butonu (pasif) --- */
    .rp-tab {
        padding: 0.6rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        background: transparent;
        color: #94a3b8;
        transition: color 0.15s;
    }
    .dark .rp-tab { color: #64748b; }

    /* --- Sekme Butonu (aktif — talepler) --- */
    .rp-tab-active-tickets {
        color: #0ea5e9;
        border-bottom: 2px solid #0ea5e9;
        margin-bottom: -2px;
    }

    /* --- Sekme Butonu (aktif — faaliyetler) --- */
    .rp-tab-active-activities {
        color: #10b981;
        border-bottom: 2px solid #10b981;
        margin-bottom: -2px;
    }

    /* --- Boş Durum --- */
    .rp-empty {
        text-align: center;
        padding: 3rem;
        color: #94a3b8;
    }
    .rp-empty p { font-size: 0.875rem; }
    .dark .rp-empty { color: #64748b; }

    /* --- Büyük Boş Durum (kullanıcı seçilmedi) --- */
    .rp-no-user {
        text-align: center;
        padding: 4rem 2rem;
        background: #ffffff;
        border: 2px dashed #e2e8f0;
        border-radius: 0.75rem;
        color: #94a3b8;
    }
    .dark .rp-no-user {
        background: #1e293b;
        border-color: #334155;
        color: #64748b;
    }
    .rp-no-user-title { font-weight: 600; font-size: 1rem; color: #475569; margin-bottom: 0.4rem; }
    .dark .rp-no-user-title { color: #94a3b8; }
    .rp-no-user-sub { font-size: 0.8rem; }

    /* --- Genel İstatistik Kutusu --- */
    .rp-stat-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.875rem 1rem;
    }
    .dark .rp-stat-box {
        background: #0f172a;
        border-color: #334155;
    }
    .rp-stat-label { font-size: 0.7rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem; }
    .dark .rp-stat-label { color: #94a3b8; }
    .rp-stat-value { font-size: 1.5rem; font-weight: 700; color: #334155; }
    .dark .rp-stat-value { color: #e2e8f0; }

    /* --- Bölüm ayırıcı --- */
    .rp-divider { border-top: 1px solid #e2e8f0; margin-top: 2rem; padding-top: 1.5rem; }
    .dark .rp-divider { border-top-color: #334155; }

    /* --- Takip No --- */
    .rp-tracking { color: #0ea5e9; font-weight: 600; font-family: monospace; }
    .dark .rp-tracking { color: #38bdf8; }
</style>

    {{-- ============================================================
         BÖLÜM 1: FİLTRE ALANI
    ============================================================ --}}
    <div class="rp-card">
        <p class="rp-section-title">Teknisyen Raporu Filtresi</p>
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 0.75rem; align-items: end;">

            <div>
                <label class="rp-label">Teknisyen</label>
                <select wire:model.live="selectedUserId" class="rp-input">
                    <option value="">— Teknisyen Seçin —</option>
                    @foreach($this->users as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="rp-label">Başlangıç Tarihi</label>
                <input type="date" wire:model.live="dateFrom" class="rp-input" />
            </div>

            <div>
                <label class="rp-label">Bitiş Tarihi</label>
                <input type="date" wire:model.live="dateTo" class="rp-input" />
            </div>

            <div>
                @if($this->selectedUserId)
                    <button wire:click="exportReport"
                        style="display:inline-flex;align-items:center;gap:0.4rem;background:#16a34a;color:#fff;border:none;border-radius:0.5rem;padding:0.56rem 1rem;font-size:0.8rem;font-weight:600;cursor:pointer;white-space:nowrap;transition:opacity .15s;"
                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:1rem;height:1rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        Excel İndir
                    </button>
                @else
                    <button disabled style="display:inline-flex;align-items:center;gap:0.4rem;background:#94a3b8;color:#fff;border:none;border-radius:0.5rem;padding:0.56rem 1rem;font-size:0.8rem;font-weight:600;cursor:not-allowed;opacity:0.5;white-space:nowrap;">
                        Excel İndir
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ============================================================
         BÖLÜM 2: ÖZET KARTLAR
    ============================================================ --}}
    @php $stats = $this->stats; @endphp
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 1.5rem;">

        <div style="background:linear-gradient(135deg,#0ea5e9,#0284c7);border-radius:.75rem;padding:1.1rem 1.25rem;color:#fff;box-shadow:0 4px 12px rgba(14,165,233,.25);">
            <p style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;opacity:.85;margin-bottom:.4rem;">Çözülen Talep</p>
            <p style="font-size:1.9rem;font-weight:700;line-height:1;margin:0;">{{ $stats['resolved_tickets'] }}</p>
            <p style="font-size:.68rem;opacity:.7;margin-top:.3rem;">Seçili tarih aralığında çözülen</p>
        </div>

        <div style="background:linear-gradient(135deg,#8b5cf6,#6d28d9);border-radius:.75rem;padding:1.1rem 1.25rem;color:#fff;box-shadow:0 4px 12px rgba(139,92,246,.25);">
            <p style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;opacity:.85;margin-bottom:.4rem;">Atanan Talep</p>
            <p style="font-size:1.9rem;font-weight:700;line-height:1;margin:0;">{{ $stats['total_tickets'] }}</p>
            <p style="font-size:.68rem;opacity:.7;margin-top:.3rem;">Tüm durumlar</p>
        </div>

        <div style="background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:.75rem;padding:1.1rem 1.25rem;color:#fff;box-shadow:0 4px 12px rgba(245,158,11,.25);">
            <p style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;opacity:.85;margin-bottom:.4rem;">Ort. Çözüm Süresi</p>
            <p style="font-size:1.5rem;font-weight:700;line-height:1;margin:0;">{{ $stats['avg_resolution_mins'] }}</p>
            <p style="font-size:.68rem;opacity:.7;margin-top:.3rem;">Talep başına ortalama</p>
        </div>

        <div style="background:linear-gradient(135deg,#10b981,#059669);border-radius:.75rem;padding:1.1rem 1.25rem;color:#fff;box-shadow:0 4px 12px rgba(16,185,129,.25);">
            <p style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;opacity:.85;margin-bottom:.4rem;">Faaliyet Sayısı</p>
            <p style="font-size:1.9rem;font-weight:700;line-height:1;margin:0;">{{ $stats['activity_count'] }}</p>
            <p style="font-size:.68rem;opacity:.7;margin-top:.3rem;">Bilet dışı faaliyetler</p>
        </div>

        <div style="background:linear-gradient(135deg,#ec4899,#be185d);border-radius:.75rem;padding:1.1rem 1.25rem;color:#fff;box-shadow:0 4px 12px rgba(236,72,153,.25);">
            <p style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;opacity:.85;margin-bottom:.4rem;">Toplam Faaliyet Süresi</p>
            <p style="font-size:1.5rem;font-weight:700;line-height:1;margin:0;">{{ $stats['activity_total_mins'] }}</p>
            <p style="font-size:.68rem;opacity:.7;margin-top:.3rem;">Tüm faaliyetler toplamı</p>
        </div>
    </div>

    {{-- ============================================================
         BÖLÜM 3: SEKMELER + TABLOLAR
    ============================================================ --}}
    @if($this->selectedUserId)

        <div class="rp-tabs-bar">
            <button wire:click="$set('activeTab', 'tickets')"
                class="rp-tab {{ $activeTab === 'tickets' ? 'rp-tab-active-tickets' : '' }}">
                🎫 Kapatılan Talepler ({{ $this->tickets->count() }})
            </button>
            <button wire:click="$set('activeTab', 'activities')"
                class="rp-tab {{ $activeTab === 'activities' ? 'rp-tab-active-activities' : '' }}">
                📋 Faaliyetler ({{ $this->activities->count() }})
            </button>
        </div>

        {{-- Talepler Tablosu --}}
        @if($activeTab === 'tickets')
            <div class="rp-table-card">
                @if($this->tickets->isEmpty())
                    <div class="rp-empty"><p>Seçilen tarih aralığında çözülen talep bulunamadı.</p></div>
                @else
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th class="rp-th">Takip No</th>
                                    <th class="rp-th">Talep Sahibi</th>
                                    <th class="rp-th">Bölüm</th>
                                    <th class="rp-th">Kategori</th>
                                    <th class="rp-th">Konu</th>
                                    <th class="rp-th">Çözüm Tarihi</th>
                                    <th class="rp-th">Çözüm Süresi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->tickets as $ticket)
                                    <tr class="rp-tr {{ $loop->odd ? 'rp-tr-odd' : 'rp-tr-even' }}">
                                        <td class="rp-td rp-tracking">{{ $ticket->tracking_number }}</td>
                                        <td class="rp-td">{{ $ticket->name }}</td>
                                        <td class="rp-td rp-td-muted">{{ $ticket->category?->department?->name ?? '-' }}</td>
                                        <td class="rp-td rp-td-muted">{{ $ticket->category?->name ?? '-' }}</td>
                                        <td class="rp-td" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $ticket->subject }}</td>
                                        <td class="rp-td rp-td-muted" style="white-space:nowrap;">{{ $ticket->resolved_at?->format('d.m.Y H:i') ?? '-' }}</td>
                                        <td class="rp-td rp-td-muted">
                                            @if($ticket->resolved_at)
                                                @php $mins = $ticket->created_at->diffInMinutes($ticket->resolved_at); $h = floor($mins/60); $m = $mins % 60; @endphp
                                                {{ $h > 0 ? "{$h}s {$m}dk" : "{$m}dk" }}
                                            @else -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

        {{-- Faaliyetler Tablosu --}}
        @if($activeTab === 'activities')
            <div class="rp-table-card">
                @if($this->activities->isEmpty())
                    <div class="rp-empty"><p>Seçilen tarih aralığında faaliyet kaydı bulunamadı.</p></div>
                @else
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th class="rp-th">Tarih</th>
                                    <th class="rp-th">Faaliyet Türü</th>
                                    <th class="rp-th">Süre</th>
                                    <th class="rp-th">Bölüm</th>
                                    <th class="rp-th">Açıklama</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->activities as $activity)
                                    <tr class="rp-tr {{ $loop->odd ? 'rp-tr-odd' : 'rp-tr-even' }}">
                                        <td class="rp-td" style="white-space:nowrap;font-weight:600;">{{ $activity->activity_date?->format('d.m.Y') }}</td>
                                        <td class="rp-td">
                                            @php
                                                $badgeColors = [
                                                    'Telefon Desteği'          => '#0ea5e9',
                                                    'Saha Çalışması'           => '#8b5cf6',
                                                    'Toplantı'                 => '#f59e0b',
                                                    'Rutin Bakım/Onarım'       => '#10b981',
                                                    'Sistem/Altyapı Çalışması' => '#ec4899',
                                                    'Diğer'                    => '#64748b',
                                                ];
                                                $bc = $badgeColors[$activity->activity_type] ?? '#64748b';
                                            @endphp
                                            <span style="display:inline-block;padding:.2rem .55rem;border-radius:999px;font-size:.7rem;font-weight:700;color:#fff;background:{{ $bc }};">
                                                {{ $activity->activity_type }}
                                            </span>
                                        </td>
                                        <td class="rp-td" style="font-weight:600;color:#10b981;">{{ $activity->duration }} dk</td>
                                        <td class="rp-td rp-td-muted">{{ $activity->department }}</td>
                                        <td class="rp-td">{{ $activity->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="rp-tfoot-tr">
                                    <td colspan="2" class="rp-tfoot-label">TOPLAM</td>
                                    <td class="rp-tfoot-label" style="color:#10b981;">{{ $this->activities->sum('duration') }} dk</td>
                                    <td class="rp-tfoot-label" colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        @endif

    @else
        {{-- Kullanıcı seçilmedi --}}
        <div class="rp-no-user">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:3rem;height:3rem;margin:0 auto 1rem;opacity:.4;display:block;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
            <p class="rp-no-user-title">Rapor görüntülemek için teknisyen seçin</p>
            <p class="rp-no-user-sub">Yukarıdan bir teknisyen ve tarih aralığı seçerek o kişinin talep ve faaliyet raporunu görüntüleyebilirsiniz.</p>
        </div>
    @endif

    {{-- ============================================================
         BÖLÜM 4: SİSTEM GENELİ İSTATİSTİKLER
    ============================================================ --}}
    @php $general = $this->generalStats; @endphp
    <div class="rp-divider">
        <p class="rp-section-title">Sistem Geneli</p>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;">
            <div class="rp-stat-box">
                <p class="rp-stat-label">Toplam Talep</p>
                <p class="rp-stat-value">{{ $general['total_tickets'] }}</p>
            </div>
            <div class="rp-stat-box">
                <p class="rp-stat-label">Açık Talep</p>
                <p class="rp-stat-value" style="color:#f59e0b;">{{ $general['open_tickets'] }}</p>
            </div>
            <div class="rp-stat-box">
                <p class="rp-stat-label">Çözülen Talep (Tüm)</p>
                <p class="rp-stat-value" style="color:#10b981;">{{ $general['resolved_tickets'] }}</p>
            </div>
            <div class="rp-stat-box">
                <p class="rp-stat-label">Toplam Faaliyet (Tüm)</p>
                <p class="rp-stat-value" style="color:#8b5cf6;">{{ $general['total_activities'] }}</p>
            </div>
        </div>
    </div>

</x-filament-panels::page>
