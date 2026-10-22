<?php

namespace App\Services;

use Composer\InstalledVersions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SystemInfoService
{
    private const REQUIRED_EXTENSIONS = [
        'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json',
        'fileinfo', 'curl', 'gd', 'intl', 'zip', 'bcmath', 'exif',
    ];

    private const PACKAGES = [
        'laravel/framework' => 'Laravel',
        'filament/filament' => 'Filament',
        'livewire/livewire' => 'Livewire',
        'maatwebsite/excel' => 'Laravel Excel',
        'phpoffice/phpspreadsheet' => 'PhpSpreadsheet',
        'barryvdh/laravel-dompdf' => 'Laravel DomPDF',
        'dompdf/dompdf' => 'DomPDF',
    ];

    public function all(): array
    {
        return [
            'application' => $this->application(),
            'packages' => $this->packages(),
            'server' => $this->server(),
            'php' => $this->php(),
            'database' => $this->database(),
            'drivers' => $this->drivers(),
            'storage' => $this->storage(),
            'warnings' => $this->warnings(),
        ];
    }

    public function application(): array
    {
        return [
            'Uygulama Adı' => config('app.name'),
            'Ortam' => app()->environment(),
            'Debug Modu' => config('app.debug') ? 'Açık' : 'Kapalı',
            'URL' => config('app.url'),
            'Saat Dilimi' => config('app.timezone'),
            'Dil' => config('app.locale'),
            'Bakım Modu' => app()->isDownForMaintenance() ? 'Açık' : 'Kapalı',
            'Config Önbelleği' => app()->configurationIsCached() ? 'Var' : 'Yok',
            'Route Önbelleği' => app()->routesAreCached() ? 'Var' : 'Yok',
            'Event Önbelleği' => app()->eventsAreCached() ? 'Var' : 'Yok',
        ];
    }

    public function packages(): array
    {
        $packages = ['PHP' => PHP_VERSION];

        foreach (self::PACKAGES as $package => $label) {
            if (InstalledVersions::isInstalled($package)) {
                $packages[$label] = InstalledVersions::getPrettyVersion($package);
            }
        }

        return $packages;
    }

    public function server(): array
    {
        $memory = $this->systemMemory();
        $load = function_exists('sys_getloadavg') ? @sys_getloadavg() : false;

        return [
            'Sunucu Adı' => gethostname() ?: '-',
            'İşletim Sistemi' => php_uname('s').' '.php_uname('r'),
            'Mimari' => php_uname('m'),
            'Web Sunucusu' => $_SERVER['SERVER_SOFTWARE'] ?? '-',
            'PHP SAPI' => PHP_SAPI,
            'Docker' => file_exists('/.dockerenv') ? 'Evet' : 'Hayır',
            'Çalışma Süresi' => $this->uptime() ?? '-',
            'Sistem Yükü (1/5/15 dk)' => $load ? implode(' / ', array_map(fn ($v) => number_format($v, 2), $load)) : '-',
            'Bellek' => $memory
                ? $this->formatBytes($memory['used']).' / '.$this->formatBytes($memory['total']).' (%'.round($memory['used'] / $memory['total'] * 100).')'
                : '-',
            'Disk' => $this->diskUsage(base_path()),
            'Sunucu Saati' => now()->format('d.m.Y H:i:s'),
        ];
    }

    public function php(): array
    {
        $loaded = array_map('strtolower', get_loaded_extensions());

        return [
            'settings' => [
                'Sürüm' => PHP_VERSION,
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time').' sn',
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'max_input_vars' => ini_get('max_input_vars'),
                'OPcache' => function_exists('opcache_get_status') && @opcache_get_status(false)['opcache_enabled'] ? 'Açık' : 'Kapalı',
                'Yüklü Eklenti Sayısı' => count($loaded),
            ],
            'extensions' => collect(self::REQUIRED_EXTENSIONS)
                ->mapWithKeys(fn ($ext) => [$ext => in_array($ext, $loaded)])
                ->all(),
        ];
    }

    public function database(): array
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        try {
            $pdo = DB::connection()->getPdo();
            $version = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
            $size = null;
            $tables = null;

            if (in_array($driver, ['mysql', 'mariadb'])) {
                $row = DB::selectOne('SELECT COUNT(*) AS tables, SUM(data_length + index_length) AS size FROM information_schema.tables WHERE table_schema = DATABASE()');
                $size = (int) $row->size;
                $tables = (int) $row->tables;
            }

            return [
                'Durum' => 'Bağlı',
                'Sürücü' => $driver,
                'Sunucu Sürümü' => $version,
                'Sunucu' => config("database.connections.{$connection}.host", '-'),
                'Veritabanı' => DB::connection()->getDatabaseName(),
                'Tablo Sayısı' => $tables ?? '-',
                'Boyut' => $size !== null ? $this->formatBytes($size) : '-',
                'Bekleyen Migration' => $this->pendingMigrations(),
            ];
        } catch (Throwable $e) {
            return [
                'Durum' => 'Bağlantı hatası',
                'Sürücü' => $driver,
                'Hata' => $e->getMessage(),
            ];
        }
    }

    public function drivers(): array
    {
        $queue = config('queue.default');

        return [
            'Cache' => config('cache.default'),
            'Session' => config('session.driver'),
            'Queue' => $queue,
            'Bekleyen İş' => $this->tableCount(config("queue.connections.{$queue}.table", 'jobs')),
            'Başarısız İş' => $this->tableCount(config('queue.failed.table', 'failed_jobs')),
            'Mail' => config('mail.default'),
            'Dosya Sistemi' => config('filesystems.default'),
            'Log Kanalı' => config('logging.default'),
        ];
    }

    public function storage(): array
    {
        $paths = [
            'storage/app' => storage_path('app'),
            'storage/framework' => storage_path('framework'),
            'storage/logs' => storage_path('logs'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];

        $checks = [];
        foreach ($paths as $label => $path) {
            $checks[$label] = is_dir($path) && is_writable($path);
        }
        $checks['public/storage bağlantısı'] = file_exists(public_path('storage'));

        $log = storage_path('logs/laravel.log');

        return [
            'checks' => $checks,
            'Log Dosyası' => is_file($log) ? $this->formatBytes(filesize($log)) : 'Yok',
            'Storage Boyutu' => $this->formatBytes(Cache::remember('system_info.storage_size', 600, fn () => $this->directorySize(storage_path('app')))),
        ];
    }

    public function warnings(): array
    {
        $warnings = [];

        if (config('app.debug')) {
            $warnings[] = 'Debug modu açık. Hata sayfalarında yapılandırma ve ortam bilgileri görünebilir; sunucuda APP_DEBUG=false yapılmalı.';
        }
        if (! app()->environment('production')) {
            $warnings[] = 'Uygulama "'.app()->environment().'" ortamında çalışıyor. Sunucuda APP_ENV=production önerilir.';
        }
        if (! app()->configurationIsCached()) {
            $warnings[] = 'Config önbelleği yok. Sunucuda "php artisan optimize" çalıştırılması performansı artırır.';
        }

        return $warnings;
    }

    public function formatBytes(int|float $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return number_format($bytes, $i === 0 ? 0 : 1, ',', '.').' '.$units[$i];
    }

    private function diskUsage(string $path): string
    {
        $total = @disk_total_space($path);
        $free = @disk_free_space($path);

        if (! $total || $free === false) {
            return '-';
        }

        $used = $total - $free;

        return $this->formatBytes($used).' / '.$this->formatBytes($total).' (%'.round($used / $total * 100).', '.$this->formatBytes($free).' boş)';
    }

    private function systemMemory(): ?array
    {
        if (! is_readable('/proc/meminfo')) {
            return null;
        }

        preg_match_all('/^(MemTotal|MemAvailable):\s+(\d+)/m', (string) file_get_contents('/proc/meminfo'), $m);
        $info = array_combine($m[1], $m[2]);

        if (! isset($info['MemTotal'], $info['MemAvailable'])) {
            return null;
        }

        return [
            'total' => $info['MemTotal'] * 1024,
            'used' => ($info['MemTotal'] - $info['MemAvailable']) * 1024,
        ];
    }

    private function uptime(): ?string
    {
        if (! is_readable('/proc/uptime')) {
            return null;
        }

        $seconds = (int) explode(' ', (string) file_get_contents('/proc/uptime'))[0];

        return sprintf('%d gün %d saat %d dk', intdiv($seconds, 86400), intdiv($seconds % 86400, 3600), intdiv($seconds % 3600, 60));
    }

    private function pendingMigrations(): int|string
    {
        try {
            $migrator = app('migrator');
            $files = array_keys($migrator->getMigrationFiles($migrator->paths() + [database_path('migrations')]));
            $ran = $migrator->getRepository()->getRan();

            return count(array_diff($files, $ran));
        } catch (Throwable) {
            return '-';
        }
    }

    private function tableCount(?string $table): int|string
    {
        try {
            return $table && Schema::hasTable($table) ? DB::table($table)->count() : '-';
        } catch (Throwable) {
            return '-';
        }
    }

    private function directorySize(string $path): int
    {
        if (! is_dir($path)) {
            return 0;
        }

        $size = 0;
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            $size += $file->getSize();
        }

        return $size;
    }
}
