<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TicketController::class, 'index'])->name('home');
Route::post('/talep-olustur', [TicketController::class, 'store'])->name('tickets.store');
Route::get('/talep-sorgula', [TicketController::class, 'show'])->name('tickets.show');

Route::get('/bilgi-bankasi', [\App\Http\Controllers\ArticleController::class, 'index'])->name('kb.index');
Route::get('/bilgi-bankasi/{slug}', [\App\Http\Controllers\ArticleController::class, 'show'])->name('kb.show');

Route::get('/tickets/attachments/{attachment}', [\App\Http\Controllers\TicketAttachmentController::class, 'download'])
    ->name('tickets.attachments.download')
    ->middleware('auth');

Route::get('/admin/tickets/{ticket}/print', [\App\Http\Controllers\Admin\TicketPrintController::class, 'show'])
    ->name('admin.tickets.print')
    ->middleware('auth');