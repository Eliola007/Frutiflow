<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\InventarioReporteController;

Route::get('/', function () {
    return redirect('/admin');
});

// Ruta de logout personalizada que funciona con Filament
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Rutas para tickets de venta
Route::middleware(['auth:web'])->group(function () {
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

    // Rutas para testing de permisos
    Route::prefix('permisos-test')->name('permisos.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\PermisosTestController::class, 'dashboard'])->name('dashboard');
        Route::get('/productos', [App\Http\Controllers\PermisosTestController::class, 'testProductos'])
             ->middleware('permission:productos.ver')->name('productos');
        Route::get('/ventas', [App\Http\Controllers\PermisosTestController::class, 'testVentas'])
             ->middleware('permission:ventas.ver')->name('ventas');
        Route::get('/auditoria', [App\Http\Controllers\PermisosTestController::class, 'auditoria'])
             ->middleware('permission:configuracion.ver')->name('auditoria');
        Route::post('/crear-producto', [App\Http\Controllers\PermisosTestController::class, 'crearProducto'])
             ->middleware('permission:productos.crear')->name('crear-producto');
        
        Route::get('/asignar-productos/{user}', [App\Http\Controllers\PermisosTestController::class, 'asignarProductos'])
             ->middleware('permission:usuarios.editar')->name('asignar-productos');
        Route::post('/guardar-productos/{user}', [App\Http\Controllers\PermisosTestController::class, 'guardarProductos'])
             ->middleware('permission:usuarios.editar')->name('guardar-productos');
    });
});
