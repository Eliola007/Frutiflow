<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\InventarioReporteController;

Route::get('/', function () {
    return redirect('/admin');
});

// Rutas para tickets de venta
Route::middleware(['auth'])->group(function () {
    Route::get('/ticket/{venta}', [TicketController::class, 'ticket'])->name('ticket.venta');
    Route::get('/ticket/{venta}/compacto', [TicketController::class, 'ticketCompacto'])->name('ticket.venta.compacto');
    Route::get('/ticket/{venta}/preview', [TicketController::class, 'preview'])->name('ticket.venta.preview');
    
    // Rutas para reportes de inventario
    Route::prefix('inventario')->name('inventario.')->group(function () {
        Route::get('/reporte-peps/{producto}', [InventarioReporteController::class, 'reportePeps'])->name('reporte-peps');
        Route::get('/reporte-general', [InventarioReporteController::class, 'reporteGeneral'])->name('reporte-general');
        Route::get('/reporte-vencimientos', [InventarioReporteController::class, 'reporteVencimientos'])->name('reporte-vencimientos');
        Route::get('/reporte-movimientos', [InventarioReporteController::class, 'reporteMovimientos'])->name('reporte-movimientos');
        Route::get('/exportar-csv', [InventarioReporteController::class, 'exportarCsv'])->name('exportar-csv');
    });
});
