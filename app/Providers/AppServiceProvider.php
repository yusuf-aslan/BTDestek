<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('local') && str_contains(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \App\Models\Ticket::observe(\App\Observers\TicketObserver::class);

        // Override Mail Config from Database
        try {
            $settings = \Illuminate\Support\Facades\Cache::rememberForever('general_settings', function () {
                return \App\Models\GeneralSetting::first();
            });

            if ($settings && $settings->mail_host) {
                config([
                    'mail.default' => $settings->mail_mailer ?? 'smtp',
                    'mail.mailers.smtp.host' => $settings->mail_host,
                    'mail.mailers.smtp.port' => $settings->mail_port,
                    'mail.mailers.smtp.username' => $settings->mail_username,
                    'mail.mailers.smtp.password' => $settings->mail_password,
                    'mail.mailers.smtp.encryption' => $settings->mail_encryption,
                    'mail.from.address' => $settings->mail_from_address,
                    'mail.from.name' => $settings->mail_from_name,
                ]);
            }
        } catch (\Exception $e) {
            // Database might not be ready during migration
        }

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $settings = \Illuminate\Support\Facades\Cache::rememberForever('general_settings', function () {
                return \App\Models\GeneralSetting::first() ?? new \App\Models\GeneralSetting([
                    'site_title' => 'Hastane BT Destek',
                    'important_reminders' => [
                        ['text' => "Kritik tıbbi cihaz arızaları için sistem üzerinden talep oluşturduktan sonra dahili 1122'yi arayınız."],
                        ['text' => "Parola sıfırlama işlemleri sadece kimlik ibrazı ile şahsen yapılmaktadır."],
                        ['text' => "Talebinize dair tüm güncellemeler otomatik olarak Bilgi İşlem ekranlarına düşmektedir."],
                    ]
                ]);
            });

            $public_menus = \Illuminate\Support\Facades\Cache::rememberForever('public_menus', function () {
                return \App\Models\Menu::whereNull('parent_id')
                    ->where('is_active', true)
                    ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
                    ->orderBy('sort_order')
                    ->get();
            });

            $view->with('settings', $settings);
            $view->with('public_menus', $public_menus);
        });
    }
}
