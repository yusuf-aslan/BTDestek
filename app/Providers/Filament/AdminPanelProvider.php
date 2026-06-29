<?php

namespace App\Providers\Filament;

use App\Livewire\TicketWatcher;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Filament\Navigation\NavigationItem;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings = \Illuminate\Support\Facades\Cache::rememberForever('general_settings', function () {
            return \App\Models\GeneralSetting::first();
        });

        $panel = $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->databaseNotifications()
            ->maxContentWidth('screen-2xl');
        
        if ($settings && $settings->menu_layout === 'horizontal') {
            $panel->topNavigation();
        }

        return $panel
            ->brandName($settings->site_title ?? 'Hastane BT Destek')
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render(<<<'HTML'
                    @livewire(\App\Livewire\TicketWatcher::class)
                    
                    <script>
                        window.addEventListener("open-resolve-confirmation-modal", () => {
                            window.dispatchEvent(new CustomEvent("open-modal", { detail: { id: "confirm-resolve-modal" } }));
                        });
                    </script>
                    
                    <x-filament::modal
                        id="confirm-resolve-modal"
                        width="md"
                        icon="heroicon-o-exclamation-triangle"
                        icon-color="warning"
                        heading="Durum Güncelleme Uyarısı"
                    >
                        <div class="text-sm text-slate-600 dark:text-slate-400 text-left">
                            Çözüm notu girilmiş fakat durum "çözüldü" olarak işaretlenmemiş. Durum otomatik olarak "çözüldü" yapılarak kaydedilsin mi?
                        </div>
                        
                        <x-slot name="footer">
                            <div class="flex justify-end gap-3">
                                <x-filament::button
                                    color="gray"
                                    x-on:click="$dispatch('close-modal', { id: 'confirm-resolve-modal' })"
                                >
                                    Vazgeç
                                </x-filament::button>
                                
                                <x-filament::button
                                    color="primary"
                                    x-on:click="
                                        $dispatch('close-modal', { id: 'confirm-resolve-modal' });
                                        Livewire.dispatch('confirm-resolve-save');
                                    "
                                >
                                    Tamam, Çözüldü Olarak Kaydet
                                </x-filament::button>
                            </div>
                        </x-slot>
                    </x-filament::modal>
HTML
                ),
            )
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): string => Blade::render('@livewire(\App\Livewire\QuickActivityModal::class)'),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '
                    <style>
                        /* Dark modda primary (Amber/Orange) butonlarin yazi rengini okunurluk icin beyaz yap */
                        .dark .fi-btn.fi-color-primary,
                        .dark .fi-btn.fi-color-primary *,
                        .dark .fi-btn.fi-color-custom,
                        .dark .fi-btn.fi-color-custom *,
                        .dark button[class*="fi-color-primary"],
                        .dark button[class*="fi-color-primary"] *,
                        .dark a[class*="fi-color-primary"],
                        .dark a[class*="fi-color-primary"] *,
                        .dark .fi-color-amber,
                        .dark .fi-color-amber * {
                            color: #ffffff !important;
                        }
                    </style>
                ',
            )
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                //
            ])
            ->navigationGroups([
                'Talep Yönetimi',
                'Duyuru Yönetimi',
                'Envanter Yönetimi',
                'Ayarlar',
            ])
            ->navigationItems([
                NavigationItem::make('Anasayfa')
                    ->url('/')
                    ->icon('heroicon-o-globe-alt')
                    ->sort(99)
                    ->openUrlInNewTab(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}