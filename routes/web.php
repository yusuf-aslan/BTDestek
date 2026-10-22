<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::post('/tickets/{ticket}/edit-lock/release', [\App\Http\Controllers\TicketEditLockController::class, 'release'])
    ->middleware('auth')->name('tickets.edit-lock.release');

Route::get('/', [TicketController::class, 'index'])->name('home');
Route::post('/talep-olustur', [TicketController::class, 'store'])->name('tickets.store');
Route::get('/talep-sorgula', [TicketController::class, 'show'])->name('tickets.show');

Route::get('/talep/{ticket}/yazdir', [\App\Http\Controllers\PublicTicketPrintController::class, 'show'])->name('public.tickets.print');

Route::get('/bilgi-bankasi', [\App\Http\Controllers\ArticleController::class, 'index'])->name('kb.index');
Route::get('/bilgi-bankasi/{slug}', [\App\Http\Controllers\ArticleController::class, 'show'])->name('kb.show');

Route::get('/egitim-videolari', [\App\Http\Controllers\TrainingVideoController::class, 'index'])->name('training.index');
Route::get('/egitim-videolari/{video}', [\App\Http\Controllers\TrainingVideoController::class, 'show'])->name('training.show');
Route::get('/egitim-videolari/{video}/video', [\App\Http\Controllers\TrainingVideoController::class, 'video'])->name('training.video');
Route::get('/egitim-videolari/{video}/altyazi', [\App\Http\Controllers\TrainingVideoController::class, 'subtitle'])->name('training.subtitle');

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
