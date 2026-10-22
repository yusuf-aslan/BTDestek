<?php

namespace App\Filament\Pages;

use App\Services\SystemInfoService;
use App\Services\VersionCheckService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SystemInfo extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $title = 'Sistem Bilgileri';

    protected static ?string $navigationLabel = 'Sistem Bilgileri';

    protected static string|\UnitEnum|null $navigationGroup = 'Ayarlar';

    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.pages.system-info';

    // Sürüm kontrolü GitHub'a istek attığı için sayfa açıldıktan sonra (wire:init) yüklenir
    public bool $versionLoaded = false;

    public static function canAccess(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('check_version')
                ->label('Sürümü Kontrol Et')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function (VersionCheckService $versions): void {
                    $result = $versions->check(fresh: true);
                    $this->versionLoaded = true;

                    $notification = match ($result['status']) {
                        'identical' => Notification::make()->success()->title('Sistem güncel'),
                        'behind' => Notification::make()->warning()->title("Güncelleme var: {$result['behind_by']} yeni commit"),
                        'ahead' => Notification::make()->info()->title("Sunucu GitHub'dan {$result['ahead_by']} commit önde"),
                        'diverged' => Notification::make()->warning()->title('Sunucu ve GitHub ayrışmış'),
                        default => Notification::make()->danger()->title('Sürüm durumu belirlenemedi')->body($result['error']),
                    };

                    $notification->send();
                }),
        ];
    }

    public function loadVersion(): void
    {
        $this->versionLoaded = true;
    }

    protected function getViewData(): array
    {
        return [
            'info' => app(SystemInfoService::class)->all(),
            'version' => $this->versionLoaded ? app(VersionCheckService::class)->check() : null,
        ];
    }
}
