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
