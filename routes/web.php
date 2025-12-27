<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TicketController::class, 'index'])->name('home');
Route::post('/talep-olustur', [TicketController::class, 'store'])->name('tickets.store');
Route::get('/talep-sorgula', [TicketController::class, 'show'])->name('tickets.show');