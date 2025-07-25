<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

Route::get('/', function () {
    return redirect('/admin');
});

// Rutas para tickets de venta
Route::middleware(['auth'])->group(function () {
    Route::get('/ticket/{venta}', [TicketController::class, 'ticket'])->name('ticket.venta');
    Route::get('/ticket/{venta}/compacto', [TicketController::class, 'ticketCompacto'])->name('ticket.venta.compacto');
    Route::get('/ticket/{venta}/preview', [TicketController::class, 'preview'])->name('ticket.venta.preview');
});
