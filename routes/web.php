<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TicketController::class, 'index'])->name('home');
Route::post('/talep-olustur', [TicketController::class, 'store'])->name('tickets.store');
Route::get('/talep-sorgula', [TicketController::class, 'show'])->name('tickets.show');

Route::get('/talep/{ticket}/yazdir', [\App\Http\Controllers\PublicTicketPrintController::class, 'show'])->name('public.tickets.print');

Route::get('/bilgi-bankasi', [\App\Http\Controllers\ArticleController::class, 'index'])->name('kb.index');
Route::get('/bilgi-bankasi/{slug}', [\App\Http\Controllers\ArticleController::class, 'show'])->name('kb.show');

Route::get('/tickets/attachments/{attachment}', [\App\Http\Controllers\TicketAttachmentController::class, 'download'])
    ->name('tickets.attachments.download')
    ->middleware('auth');

Route::get('/admin/tickets/{ticket}/print', [\App\Http\Controllers\Admin\TicketPrintController::class, 'show'])
    ->name('admin.tickets.print')
    ->middleware('auth');

Route::get('/admin/envanter/{asset}/yazdir', [\App\Http\Controllers\Admin\AssetPrintController::class, 'show'])
    ->name('admin.assets.print-qr')
    ->middleware('auth');

Route::get('/admin/varlik-sorgula/yazdir', [\App\Http\Controllers\Admin\AssetQueryPrintController::class, 'show'])
    ->name('asset-query.print')
    ->middleware('auth');

Route::get('/clear-cache', function() {
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('filament:optimize-clear');
    return "Tüm önbellekler (OPcache, View, Cache, Filament) başarıyla temizlendi! <a href='/admin'>Admin Paneline Git</a>";
});