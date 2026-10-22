<x-filament-panels::page>

{{-- ============================================================
     TEMA UYUMLU STİLLER (Light + Dark)
============================================================ --}}
<style>
    .si-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 26rem), 1fr));
        gap: 1.5rem;
        align-items: start;
    }

    /* --- Anahtar / Değer Tablosu --- */
    .si-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
    .si-table th, .si-table td {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f1f5f9;
        text-align: left;
        vertical-align: top;
    }
    .si-table tr:last-child th, .si-table tr:last-child td { border-bottom: 0; }
    .si-table th { width: 42%; font-weight: 500; color: #64748b; padding-right: 1rem; }
    .si-table td { color: #0f172a; font-weight: 600; word-break: break-word; }
    .dark .si-table th, .dark .si-table td { border-color: #1e293b; }
    .dark .si-table th { color: #94a3b8; }
    .dark .si-table td { color: #e2e8f0; }

    .si-mono { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 0.8rem; }
    .si-muted { color: #64748b; font-weight: 400; font-size: 0.8rem; }
    .dark .si-muted { color: #94a3b8; }

    /* --- Durum Noktaları (eklenti / izin listesi) --- */
    .si-checks { display: flex; flex-wrap: wrap; gap: 0.5rem; }
    .si-check {
        display: inline-flex; align-items: center; gap: 0.375rem;
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.75rem; font-weight: 600;
        border: 1px solid #e2e8f0;
        color: #334155;
        background: #f8fafc;
    }
    .dark .si-check { border-color: #334155; color: #cbd5e1; background: #0f172a; }
    .si-dot { width: 0.5rem; height: 0.5rem; border-radius: 9999px; flex: none; }
    .si-dot-ok { background: #16a34a; }
    .si-dot-bad { background: #dc2626; }

    /* --- Uyarı Kutusu --- */
    .si-alert {
        display: flex; gap: 0.75rem; align-items: flex-start;
        padding: 0.875rem 1rem;
        border-radius: 0.75rem;
        border: 1px solid #fde68a;
        background: #fffbeb;
        color: #92400e;
        font-size: 0.875rem;
    }
    .dark .si-alert { border-color: #78350f; background: rgba(120, 53, 15, 0.25); color: #fcd34d; }
    .si-alert ul { margin: 0; padding-left: 1rem; list-style: disc; }
    .si-alert li + li { margin-top: 0.25rem; }

    /* --- Sürüm Kontrolü --- */
    .si-version { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 18rem), 1fr)); gap: 1rem; }
    .si-commit {
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.875rem 1rem;
        background: #f8fafc;
    }
    .dark .si-commit { border-color: #334155; background: #0f172a; }
    .si-commit-label {
        font-size: 0.7rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase;
        color: #64748b; margin-bottom: 0.375rem;
    }
    .dark .si-commit-label { color: #94a3b8; }
    .si-commit-msg { font-weight: 600; color: #0f172a; font-size: 0.875rem; margin: 0.25rem 0; }
    .dark .si-commit-msg { color: #e2e8f0; }

    .si-commits { margin-top: 1rem; border-top: 1px solid #f1f5f9; }
    .dark .si-commits { border-color: #1e293b; }
    .si-commits li {
        display: flex; gap: 0.75rem; align-items: baseline;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }
    .dark .si-commits li { border-color: #1e293b; }
    .si-commits li:last-child { border-bottom: 0; }
    .si-commits a { color: #d97706; }
    .dark .si-commits a { color: #fbbf24; }
    .si-commits .si-commit-title { flex: 1; min-width: 0; color: #0f172a; }
    .dark .si-commits .si-commit-title { color: #e2e8f0; }

    .si-status-row { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; margin-bottom: 1rem; }
</style>

@php
    $badgeColor = fn (bool $ok) => $ok ? 'success' : 'danger';
@endphp

{{-- ============================================================
     SÜRÜM KONTROLÜ
============================================================ --}}
<div wire:init="loadVersion">
<x-filament::section icon="heroicon-o-code-bracket-square">
    <x-slot name="heading">Sürüm Kontrolü</x-slot>
    <x-slot name="description">Sunucudaki kod ile GitHub'daki son sürüm karşılaştırılır. Sonuç 10 dakika önbellekte tutulur.</x-slot>

    @if (! $version)
        <div class="si-muted" style="display:flex;align-items:center;gap:.5rem;">
            <x-filament::loading-indicator style="width:1.25rem;height:1.25rem;" />
            GitHub kontrol ediliyor...
        </div>
    @else
        @php
            [$statusLabel, $statusColor] = match ($version['status']) {
                'identical' => ['Güncel', 'success'],
                'behind' => [$version['behind_by'].' commit geride', 'warning'],
                'ahead' => ['GitHub\'dan '.$version['ahead_by'].' commit önde', 'info'],
                'diverged' => ['Ayrışmış ('.$version['behind_by'].' geride, '.$version['ahead_by'].' önde)', 'danger'],
                default => ['Belirlenemedi', 'gray'],
            };
            $local = $version['local'];
            $remote = $version['remote'];
        @endphp

        <div class="si-status-row">
            <x-filament::badge :color="$statusColor" size="lg">{{ $statusLabel }}</x-filament::badge>
            @if ($local['modified_files'])
                <x-filament::badge color="warning">Sunucuda {{ $local['modified_files'] }} dosyada commit'lenmemiş değişiklik var</x-filament::badge>
            @endif
            <span class="si-muted">
                {{ $version['repo'] }} · {{ $version['branch'] }} · Son kontrol: {{ $version['checked_at']->format('d.m.Y H:i') }}
            </span>
        </div>

        @if ($version['error'])
            <div class="si-alert" style="margin-bottom:1rem;">
                <x-filament::icon icon="heroicon-o-exclamation-triangle" style="width:1.25rem;height:1.25rem;flex:none;" />
                <div>{{ $version['error'] }}</div>
            </div>
        @endif

        <div class="si-version">
            <div class="si-commit">
                <div class="si-commit-label">Sunucudaki Sürüm</div>
                @if ($local['sha'])
                    <div class="si-mono">{{ substr($local['sha'], 0, 7) }} @if ($local['branch'])<span class="si-muted">({{ $local['branch'] }})</span>@endif</div>
                    @if ($local['message'])
                        <div class="si-commit-msg">{{ $local['message'] }}</div>
                    @endif
                    @if ($local['date'])
                        <div class="si-muted">{{ $local['author'] }} · {{ $local['date']->timezone(config('app.timezone'))->format('d.m.Y H:i') }} ({{ $local['date']->diffForHumans() }})</div>
                    @endif
                @else
                    <div class="si-muted">Commit bilgisi okunamadı.</div>
                @endif
            </div>

            <div class="si-commit">
                <div class="si-commit-label">GitHub'daki Son Sürüm</div>
                @if ($remote)
                    <div class="si-mono">
                        <a href="{{ $remote['url'] }}" target="_blank" rel="noopener" style="color:inherit;text-decoration:underline;">{{ substr($remote['sha'], 0, 7) }}</a>
                    </div>
                    <div class="si-commit-msg">{{ $remote['message'] }}</div>
                    <div class="si-muted">{{ $remote['author'] }} · {{ $remote['date']->timezone(config('app.timezone'))->format('d.m.Y H:i') }} ({{ $remote['date']->diffForHumans() }})</div>
                @else
                    <div class="si-muted">GitHub bilgisi alınamadı.</div>
                @endif
            </div>
        </div>

        @if (count($version['commits']))
            <div class="si-commit-label" style="margin-top:1.25rem;">Sunucuda Olmayan Commit'ler (en yeni üstte{{ $version['behind_by'] > count($version['commits']) ? ', ilk '.count($version['commits']) : '' }})</div>
            <ul class="si-commits">
                @foreach ($version['commits'] as $commit)
                    <li>
                        <a href="{{ $commit['url'] }}" target="_blank" rel="noopener" class="si-mono">{{ substr($commit['sha'], 0, 7) }}</a>
                        <span class="si-commit-title">{{ $commit['message'] }}</span>
                        <span class="si-muted" style="white-space:nowrap;">{{ $commit['date']->diffForHumans() }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="si-muted" style="margin-top:.75rem;">
                Güncellemek için sunucuda <span class="si-mono">git pull</span> ve ardından gerekiyorsa <span class="si-mono">composer install</span>, <span class="si-mono">php artisan migrate --force</span>, <span class="si-mono">php artisan optimize</span> çalıştırın.
            </div>
        @endif
    @endif
</x-filament::section>
</div>

{{-- ============================================================
     GITHUB BAĞLANTISI
============================================================ --}}
<form wire:submit="saveGithub">
    {{ $this->form }}
</form>

{{-- ============================================================
     UYARILAR
============================================================ --}}
@if (count($info['warnings']))
    <div class="si-alert">
        <x-filament::icon icon="heroicon-o-shield-exclamation" style="width:1.25rem;height:1.25rem;flex:none;margin-top:.1rem;" />
        <ul>
            @foreach ($info['warnings'] as $warning)
                <li>{{ $warning }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ============================================================
     BİLGİ KARTLARI
============================================================ --}}
<div class="si-grid">
    <x-filament::section icon="heroicon-o-cube">
        <x-slot name="heading">Uygulama</x-slot>
        <table class="si-table">
            @foreach ($info['application'] as $label => $value)
                <tr><th>{{ $label }}</th><td>{{ $value }}</td></tr>
            @endforeach
        </table>
    </x-filament::section>

    <x-filament::section icon="heroicon-o-puzzle-piece">
        <x-slot name="heading">Sürümler</x-slot>
        <table class="si-table">
            @foreach ($info['packages'] as $label => $value)
                <tr><th>{{ $label }}</th><td class="si-mono">{{ $value }}</td></tr>
            @endforeach
        </table>
    </x-filament::section>

    <x-filament::section icon="heroicon-o-server">
        <x-slot name="heading">Sunucu</x-slot>
        <table class="si-table">
            @foreach ($info['server'] as $label => $value)
                <tr><th>{{ $label }}</th><td>{{ $value }}</td></tr>
            @endforeach
        </table>
    </x-filament::section>

    <x-filament::section icon="heroicon-o-circle-stack">
        <x-slot name="heading">Veritabanı</x-slot>
        <table class="si-table">
            @foreach ($info['database'] as $label => $value)
                <tr>
                    <th>{{ $label }}</th>
                    <td>
                        @if ($label === 'Durum')
                            <x-filament::badge :color="$badgeColor($value === 'Bağlı')">{{ $value }}</x-filament::badge>
                        @elseif ($label === 'Bekleyen Migration' && is_int($value) && $value > 0)
                            <x-filament::badge color="warning">{{ $value }}</x-filament::badge>
                        @else
                            {{ $value }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </x-filament::section>

    <x-filament::section icon="heroicon-o-code-bracket">
        <x-slot name="heading">PHP</x-slot>
        <table class="si-table" style="margin-bottom:1rem;">
            @foreach ($info['php']['settings'] as $label => $value)
                <tr><th class="{{ str_contains($label, '_') ? 'si-mono' : '' }}">{{ $label }}</th><td>{{ $value }}</td></tr>
            @endforeach
        </table>
        <div class="si-commit-label">Gerekli Eklentiler</div>
        <div class="si-checks">
            @foreach ($info['php']['extensions'] as $extension => $loaded)
                <span class="si-check" title="{{ $loaded ? 'Yüklü' : 'Yüklü değil' }}">
                    <span class="si-dot {{ $loaded ? 'si-dot-ok' : 'si-dot-bad' }}"></span>{{ $extension }}
                </span>
            @endforeach
        </div>
    </x-filament::section>

    <x-filament::section icon="heroicon-o-adjustments-horizontal">
        <x-slot name="heading">Sürücüler ve Kuyruk</x-slot>
        <table class="si-table">
            @foreach ($info['drivers'] as $label => $value)
                <tr>
                    <th>{{ $label }}</th>
                    <td>
                        @if ($label === 'Başarısız İş' && is_int($value) && $value > 0)
                            <x-filament::badge color="danger">{{ $value }}</x-filament::badge>
                        @else
                            {{ $value }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </x-filament::section>

    <x-filament::section icon="heroicon-o-folder-open">
        <x-slot name="heading">Depolama ve İzinler</x-slot>
        <table class="si-table" style="margin-bottom:1rem;">
            <tr><th>Log Dosyası</th><td>{{ $info['storage']['Log Dosyası'] }}</td></tr>
            <tr><th>Yüklenen Dosyalar</th><td>{{ $info['storage']['Storage Boyutu'] }}</td></tr>
        </table>
        <div class="si-commit-label">Yazma İzinleri</div>
        <div class="si-checks">
            @foreach ($info['storage']['checks'] as $path => $ok)
                <span class="si-check" title="{{ $ok ? 'Sorun yok' : 'Sorunlu' }}">
                    <span class="si-dot {{ $ok ? 'si-dot-ok' : 'si-dot-bad' }}"></span><span class="si-mono">{{ $path }}</span>
                </span>
            @endforeach
        </div>
    </x-filament::section>
</div>

</x-filament-panels::page>
