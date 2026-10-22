<?php

namespace App\Filament\Pages;

use App\Models\GeneralSetting;
use App\Services\SystemInfoService;
use App\Services\VersionCheckService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class SystemInfo extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $title = 'Sistem Bilgileri';

    protected static ?string $navigationLabel = 'Sistem Bilgileri';

    protected static string|\UnitEnum|null $navigationGroup = 'Ayarlar';

    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.pages.system-info';

    // Sürüm kontrolü GitHub'a istek attığı için sayfa açıldıktan sonra (wire:init) yüklenir
    public bool $versionLoaded = false;

    public ?array $github = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    public function mount(): void
    {
        $setting = GeneralSetting::query()->first();

        // Anahtar hiçbir zaman forma geri doldurulmaz
        $this->form->fill([
            'github_repo' => $setting?->github_repo ?: VersionCheckService::DEFAULT_REPO,
            'github_branch' => $setting?->github_branch ?: VersionCheckService::DEFAULT_BRANCH,
            'github_token' => null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('github')
            ->schema([
                Section::make('GitHub bağlantısı')
                    ->icon('heroicon-o-link')
                    ->description(new HtmlString('Özel depo için yalnızca <strong>Contents: Read-only</strong> yetkili ve sadece bu depoyla sınırlandırılmış bir fine-grained token kullanın. Anahtar şifreli saklanır ve tekrar görüntülenmez.'))
                    ->schema([
                        Grid::make(['default' => 1, 'md' => 3])
                            ->schema([
                                TextInput::make('github_repo')
                                    ->label('Depo')
                                    ->placeholder('kullanici/depo')
                                    ->required()
                                    ->regex('/^[A-Za-z0-9_.-]+\/[A-Za-z0-9_.-]+$/')
                                    ->validationMessages(['regex' => 'Depo "kullanici/depo" biçiminde olmalı.'])
                                    ->columnSpan(['md' => 2]),
                                TextInput::make('github_branch')
                                    ->label('İzlenen dal')
                                    ->required()
                                    ->regex('/^[A-Za-z0-9._\/-]+$/')
                                    ->validationMessages(['regex' => 'Dal adı geçersiz.']),
                            ]),
                        TextInput::make('github_token')
                            ->label(fn (): HtmlString => new HtmlString('Salt okunur erişim anahtarı · '.($this->hasToken()
                                ? '<span style="color:#16a34a;">tanımlı</span>'
                                : '<span style="color:#94a3b8;">tanımlı değil</span>')))
                            ->password()
                            ->autocomplete('new-password')
                            ->placeholder(fn (): string => $this->hasToken() ? 'Değiştirmek istemiyorsanız boş bırakın' : 'github_pat_...')
                            ->maxLength(500),
                        Actions::make([
                            Action::make('save_github')
                                ->label('Bağlantıyı Kaydet')
                                ->icon('heroicon-o-check')
                                ->action('saveGithub'),
                            Action::make('check_now')
                                ->label('Şimdi Kontrol Et')
                                ->icon('heroicon-o-arrow-path')
                                ->color('gray')
                                ->action('checkNow'),
                            Action::make('delete_token')
                                ->label('Anahtarı Sil')
                                ->icon('heroicon-o-trash')
                                ->color('danger')
                                ->outlined()
                                ->visible(fn (): bool => $this->hasToken())
                                ->requiresConfirmation()
                                ->modalHeading('Erişim anahtarı silinsin mi?')
                                ->modalDescription('Depo özel ise anahtar olmadan sürüm kontrolü yapılamaz.')
                                ->modalSubmitActionLabel('Sil')
                                ->action('deleteToken'),
                        ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('check_version')
                ->label('Sürümü Kontrol Et')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action('checkNow'),
        ];
    }

    public function loadVersion(): void
    {
        $this->versionLoaded = true;
    }

    public function saveGithub(): void
    {
        $data = $this->form->getState();

        $values = [
            'github_repo' => trim($data['github_repo']),
            'github_branch' => trim($data['github_branch']),
        ];

        if (filled($data['github_token'] ?? null)) {
            $values['github_token'] = trim($data['github_token']);
        }

        GeneralSetting::firstOrCreate([])->update($values);

        $this->form->fill([...$values, 'github_token' => null]);

        Notification::make()->success()->title('GitHub bağlantısı kaydedildi.')->send();

        $this->checkNow();
    }

    public function deleteToken(): void
    {
        GeneralSetting::query()->first()?->update(['github_token' => null]);
        app(VersionCheckService::class)->forget();

        Notification::make()->success()->title('Erişim anahtarı silindi.')->send();
    }

    public function checkNow(): void
    {
        $result = app(VersionCheckService::class)->check(fresh: true);
        $this->versionLoaded = true;

        $notification = match ($result['status']) {
            'identical' => Notification::make()->success()->title('Sistem güncel'),
            'behind' => Notification::make()->warning()->title("Güncelleme var: {$result['behind_by']} yeni commit"),
            'ahead' => Notification::make()->info()->title("Sunucu GitHub'dan {$result['ahead_by']} commit önde"),
            'diverged' => Notification::make()->warning()->title('Sunucu ve GitHub ayrışmış'),
            default => Notification::make()->danger()->title('Sürüm durumu belirlenemedi')->body($result['error']),
        };

        $notification->send();
    }

    protected function hasToken(): bool
    {
        return filled(GeneralSetting::query()->value('github_token'));
    }

    protected function getViewData(): array
    {
        return [
            'info' => app(SystemInfoService::class)->all(),
            'version' => $this->versionLoaded ? app(VersionCheckService::class)->check() : null,
        ];
    }
}
