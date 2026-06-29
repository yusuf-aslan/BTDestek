<x-filament-panels::page>

    {{-- ============================================================
         BÖLÜM 1: FİLTRE ALANI (Kullanıcı + Tarih)
    ============================================================ --}}
    <div style="background: var(--fi-color-white, #fff); border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <p style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 1rem;">Teknisyen Raporu Filtresi</p>
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 0.75rem; align-items: end;">

            {{-- Kullanıcı --}}
            <div>
                <label style="display:block; font-size:0.75rem; font-weight:600; color:#64748b; margin-bottom:0.35rem;">Teknisyen</label>
                <select wire:model.live="selectedUserId"
                    style="display:block; width:100%; border:1px solid #cbd5e1; border-radius:0.5rem; padding:0.5rem 0.75rem; font-size:0.875rem; color:inherit; background:transparent; outline:none;">
                    <option value="">— Teknisyen Seçin —</option>
                    @foreach($this->users as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Başlangıç Tarihi --}}
            <div>
                <label style="display:block; font-size:0.75rem; font-weight:600; color:#64748b; margin-bottom:0.35rem;">Başlangıç Tarihi</label>
                <input type="date" wire:model.live="dateFrom"
                    style="display:block; width:100%; border:1px solid #cbd5e1; border-radius:0.5rem; padding:0.5rem 0.75rem; font-size:0.875rem; color:inherit; background:transparent; outline:none;" />
            </div>

            {{-- Bitiş Tarihi --}}
            <div>
                <label style="display:block; font-size:0.75rem; font-weight:600; color:#64748b; margin-bottom:0.35rem;">Bitiş Tarihi</label>
                <input type="date" wire:model.live="dateTo"
                    style="display:block; width:100%; border:1px solid #cbd5e1; border-radius:0.5rem; padding:0.5rem 0.75rem; font-size:0.875rem; color:inherit; background:transparent; outline:none;" />
            </div>

            {{-- Excel Butonu --}}
            <div>
                @if($this->selectedUserId)
                    <button wire:click="exportReport"
                        style="display:inline-flex; align-items:center; gap:0.4rem; background:#16a34a; color:#fff; border:none; border-radius:0.5rem; padding:0.55rem 1rem; font-size:0.8rem; font-weight:600; cursor:pointer; white-space:nowrap;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:1rem;height:1rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Excel İndir
                    </button>
                @else
                    <button disabled
                        style="display:inline-flex; align-items:center; gap:0.4rem; background:#94a3b8; color:#fff; border:none; border-radius:0.5rem; padding:0.55rem 1rem; font-size:0.8rem; font-weight:600; cursor:not-allowed; white-space:nowrap; opacity:0.6;">
                        Excel İndir
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ============================================================
         BÖLÜM 2: ÖZET KARTLAR (Seçili Teknisyen & Tarih)
    ============================================================ --}}
    @php $stats = $this->stats; @endphp

    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 1.5rem;">

        {{-- Çözülen Talep --}}
        <div style="background:linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border-radius:0.75rem; padding:1.1rem 1.25rem; color:#fff; box-shadow:0 4px 12px rgba(14,165,233,0.3);">
            <p style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; opacity:0.85; margin-bottom:0.4rem;">Çözülen Talep</p>
            <p style="font-size:1.8rem; font-weight:700; line-height:1;">{{ $stats['resolved_tickets'] }}</p>
            <p style="font-size:0.7rem; opacity:0.75; margin-top:0.3rem;">resolved_by</p>
        </div>

        {{-- Atanan Talep --}}
        <div style="background:linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); border-radius:0.75rem; padding:1.1rem 1.25rem; color:#fff; box-shadow:0 4px 12px rgba(139,92,246,0.3);">
            <p style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; opacity:0.85; margin-bottom:0.4rem;">Atanan Talep</p>
            <p style="font-size:1.8rem; font-weight:700; line-height:1;">{{ $stats['total_tickets'] }}</p>
            <p style="font-size:0.7rem; opacity:0.75; margin-top:0.3rem;">Seçili tarih aralığı</p>
        </div>

        {{-- Ort. Çözüm Süresi --}}
        <div style="background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius:0.75rem; padding:1.1rem 1.25rem; color:#fff; box-shadow:0 4px 12px rgba(245,158,11,0.3);">
            <p style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; opacity:0.85; margin-bottom:0.4rem;">Ort. Çözüm Süresi</p>
            <p style="font-size:1.5rem; font-weight:700; line-height:1;">{{ $stats['avg_resolution_mins'] }}</p>
            <p style="font-size:0.7rem; opacity:0.75; margin-top:0.3rem;">Talep başına ortalama</p>
        </div>

        {{-- Faaliyet Sayısı --}}
        <div style="background:linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius:0.75rem; padding:1.1rem 1.25rem; color:#fff; box-shadow:0 4px 12px rgba(16,185,129,0.3);">
            <p style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; opacity:0.85; margin-bottom:0.4rem;">Faaliyet Sayısı</p>
            <p style="font-size:1.8rem; font-weight:700; line-height:1;">{{ $stats['activity_count'] }}</p>
            <p style="font-size:0.7rem; opacity:0.75; margin-top:0.3rem;">Bilet dışı faaliyetler</p>
        </div>

        {{-- Toplam Faaliyet Süresi --}}
        <div style="background:linear-gradient(135deg, #ec4899 0%, #be185d 100%); border-radius:0.75rem; padding:1.1rem 1.25rem; color:#fff; box-shadow:0 4px 12px rgba(236,72,153,0.3);">
            <p style="font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; opacity:0.85; margin-bottom:0.4rem;">Toplam Faaliyet Süresi</p>
            <p style="font-size:1.5rem; font-weight:700; line-height:1;">{{ $stats['activity_total_mins'] }}</p>
            <p style="font-size:0.7rem; opacity:0.75; margin-top:0.3rem;">Tüm faaliyetler toplamı</p>
        </div>
    </div>

    {{-- ============================================================
         BÖLÜM 3: SEKMELER (Talepler / Faaliyetler)
    ============================================================ --}}
    @if($this->selectedUserId)
        {{-- Sekme Başlıkları --}}
        <div style="display:flex; gap:0; border-bottom:2px solid #e2e8f0; margin-bottom:1.25rem;">
            <button wire:click="$set('activeTab', 'tickets')"
                style="padding:0.6rem 1.25rem; font-size:0.875rem; font-weight:600; border:none; cursor:pointer; background:transparent;
                       {{ $activeTab === 'tickets' ? 'color:#0ea5e9; border-bottom:2px solid #0ea5e9; margin-bottom:-2px;' : 'color:#94a3b8;' }}">
                🎫 Kapatılan Talepler ({{ $this->tickets->count() }})
            </button>
            <button wire:click="$set('activeTab', 'activities')"
                style="padding:0.6rem 1.25rem; font-size:0.875rem; font-weight:600; border:none; cursor:pointer; background:transparent;
                       {{ $activeTab === 'activities' ? 'color:#10b981; border-bottom:2px solid #10b981; margin-bottom:-2px;' : 'color:#94a3b8;' }}">
                📋 Faaliyetler ({{ $this->activities->count() }})
            </button>
        </div>

        {{-- Talepler Tablosu --}}
        @if($activeTab === 'tickets')
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:0.75rem; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                @if($this->tickets->isEmpty())
                    <div style="text-align:center; padding:3rem; color:#94a3b8;">
                        <p style="font-size:0.875rem;">Seçilen tarih aralığında bu teknisyen tarafından çözülen talep bulunamadı.</p>
                    </div>
                @else
                    <table style="width:100%; border-collapse:collapse; font-size:0.8125rem;">
                        <thead>
                            <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                                <th style="padding:0.65rem 1rem; text-align:left; font-weight:600; color:#475569; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em;">Takip No</th>
                                <th style="padding:0.65rem 1rem; text-align:left; font-weight:600; color:#475569; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em;">Talep Sahibi</th>
                                <th style="padding:0.65rem 1rem; text-align:left; font-weight:600; color:#475569; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em;">Bölüm</th>
                                <th style="padding:0.65rem 1rem; text-align:left; font-weight:600; color:#475569; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em;">Kategori</th>
                                <th style="padding:0.65rem 1rem; text-align:left; font-weight:600; color:#475569; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em;">Konu</th>
                                <th style="padding:0.65rem 1rem; text-align:left; font-weight:600; color:#475569; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em;">Çözüm Tarihi</th>
                                <th style="padding:0.65rem 1rem; text-align:left; font-weight:600; color:#475569; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em;">Çözüm Süresi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->tickets as $ticket)
                                <tr style="border-bottom:1px solid #f1f5f9; {{ $loop->odd ? 'background:#fafafa;' : '' }}">
                                    <td style="padding:0.65rem 1rem; color:#0ea5e9; font-weight:600; font-family:monospace;">{{ $ticket->tracking_number }}</td>
                                    <td style="padding:0.65rem 1rem; color:#334155;">{{ $ticket->name }}</td>
                                    <td style="padding:0.65rem 1rem; color:#64748b;">{{ $ticket->category?->department?->name ?? '-' }}</td>
                                    <td style="padding:0.65rem 1rem; color:#64748b;">{{ $ticket->category?->name ?? '-' }}</td>
                                    <td style="padding:0.65rem 1rem; color:#334155; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $ticket->subject }}</td>
                                    <td style="padding:0.65rem 1rem; color:#64748b; white-space:nowrap;">{{ $ticket->resolved_at?->format('d.m.Y H:i') ?? '-' }}</td>
                                    <td style="padding:0.65rem 1rem; color:#64748b;">
                                        @if($ticket->resolved_at)
                                            @php
                                                $mins = $ticket->created_at->diffInMinutes($ticket->resolved_at);
                                                $h = floor($mins / 60); $m = $mins % 60;
                                            @endphp
                                            {{ $h > 0 ? "{$h}s {$m}dk" : "{$m}dk" }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endif

        {{-- Faaliyetler Tablosu --}}
        @if($activeTab === 'activities')
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:0.75rem; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                @if($this->activities->isEmpty())
                    <div style="text-align:center; padding:3rem; color:#94a3b8;">
                        <p style="font-size:0.875rem;">Seçilen tarih aralığında faaliyet kaydı bulunamadı.</p>
                    </div>
                @else
                    <table style="width:100%; border-collapse:collapse; font-size:0.8125rem;">
                        <thead>
                            <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                                <th style="padding:0.65rem 1rem; text-align:left; font-weight:600; color:#475569; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em;">Tarih</th>
                                <th style="padding:0.65rem 1rem; text-align:left; font-weight:600; color:#475569; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em;">Faaliyet Türü</th>
                                <th style="padding:0.65rem 1rem; text-align:left; font-weight:600; color:#475569; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em;">Süre</th>
                                <th style="padding:0.65rem 1rem; text-align:left; font-weight:600; color:#475569; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em;">Bölüm</th>
                                <th style="padding:0.65rem 1rem; text-align:left; font-weight:600; color:#475569; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em;">Açıklama</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->activities as $activity)
                                <tr style="border-bottom:1px solid #f1f5f9; {{ $loop->odd ? 'background:#fafafa;' : '' }}">
                                    <td style="padding:0.65rem 1rem; color:#64748b; white-space:nowrap; font-weight:600;">{{ $activity->activity_date?->format('d.m.Y') }}</td>
                                    <td style="padding:0.65rem 1rem;">
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
                                        <span style="display:inline-block; padding:0.2rem 0.55rem; border-radius:999px; font-size:0.7rem; font-weight:600; color:#fff; background:{{ $bc }};">
                                            {{ $activity->activity_type }}
                                        </span>
                                    </td>
                                    <td style="padding:0.65rem 1rem; color:#334155; font-weight:600;">{{ $activity->duration }} dk</td>
                                    <td style="padding:0.65rem 1rem; color:#64748b;">{{ $activity->department }}</td>
                                    <td style="padding:0.65rem 1rem; color:#334155;">{{ $activity->description }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:#f8fafc; border-top:2px solid #e2e8f0;">
                                <td colspan="2" style="padding:0.65rem 1rem; font-weight:700; color:#334155;">TOPLAM</td>
                                <td style="padding:0.65rem 1rem; font-weight:700; color:#10b981;">{{ $this->activities->sum('duration') }} dk</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>
        @endif

    @else
        {{-- Kullanıcı seçilmemişse boş durum --}}
        <div style="text-align:center; padding:4rem 2rem; background:#fff; border:2px dashed #e2e8f0; border-radius:0.75rem; color:#94a3b8;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:3rem; height:3rem; margin:0 auto 1rem; color:#cbd5e1; display:block;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
            <p style="font-weight:600; font-size:1rem; color:#475569; margin-bottom:0.4rem;">Rapor görüntülemek için teknisyen seçin</p>
            <p style="font-size:0.8rem;">Yukarıdan bir teknisyen ve tarih aralığı seçerek o kişinin talep ve faaliyet raporunu görüntüleyebilirsiniz.</p>
        </div>
    @endif

    {{-- ============================================================
         BÖLÜM 4: GENEL SİSTEM İSTATİSTİKLERİ (Her zaman görünür)
    ============================================================ --}}
    @php $general = $this->generalStats; @endphp
    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0;">
        <p style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.75rem;">Sistem Geneli</p>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;">
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:0.5rem; padding:0.875rem 1rem;">
                <p style="font-size:0.7rem; color:#64748b; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Toplam Talep</p>
                <p style="font-size:1.5rem; font-weight:700; color:#334155;">{{ $general['total_tickets'] }}</p>
            </div>
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:0.5rem; padding:0.875rem 1rem;">
                <p style="font-size:0.7rem; color:#64748b; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Açık Talep</p>
                <p style="font-size:1.5rem; font-weight:700; color:#f59e0b;">{{ $general['open_tickets'] }}</p>
            </div>
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:0.5rem; padding:0.875rem 1rem;">
                <p style="font-size:0.7rem; color:#64748b; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Çözülen Talep (Tüm)</p>
                <p style="font-size:1.5rem; font-weight:700; color:#10b981;">{{ $general['resolved_tickets'] }}</p>
            </div>
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:0.5rem; padding:0.875rem 1rem;">
                <p style="font-size:0.7rem; color:#64748b; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Toplam Faaliyet (Tüm)</p>
                <p style="font-size:1.5rem; font-weight:700; color:#8b5cf6;">{{ $general['total_activities'] }}</p>
            </div>
        </div>
    </div>

</x-filament-panels::page>
