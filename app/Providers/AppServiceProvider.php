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

            $view->with('settings', $settings);
        });
    }
}
