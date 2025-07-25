<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PagoCliente;
use App\Observers\PagoClienteObserver;

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
        // Registrar observers
        \App\Models\PagoCliente::observe(\App\Observers\PagoClienteObserver::class);
        \App\Models\PagoProveedor::observe(\App\Observers\PagoProveedorObserver::class);
        \App\Models\Compra::observe(\App\Observers\CompraObserver::class);
        \App\Models\CompraItem::observe(\App\Observers\CompraItemObserver::class);
        \App\Models\VentaItem::observe(\App\Observers\VentaItemObserver::class);
    }
}
