<?php

namespace App\Observers;

use App\Models\VentaItem;

class VentaItemObserver
{
    /**
     * Handle the VentaItem "created" event.
     */
    public function created(VentaItem $ventaItem): void
    {
        $this->actualizarPrecioVenta($ventaItem);
    }

    /**
     * Handle the VentaItem "updated" event.
     */
    public function updated(VentaItem $ventaItem): void
    {
        $this->actualizarPrecioVenta($ventaItem);
    }

    /**
     * Handle the VentaItem "deleted" event.
     */
    public function deleted(VentaItem $ventaItem): void
    {
        $this->actualizarPrecioVenta($ventaItem);
    }

    /**
     * Handle the VentaItem "restored" event.
     */
    public function restored(VentaItem $ventaItem): void
    {
        $this->actualizarPrecioVenta($ventaItem);
    }

    /**
     * Handle the VentaItem "force deleted" event.
     */
    public function forceDeleted(VentaItem $ventaItem): void
    {
        $this->actualizarPrecioVenta($ventaItem);
    }

    /**
     * Actualizar el precio de venta sugerido del producto
     */
    private function actualizarPrecioVenta(VentaItem $ventaItem): void
    {
        if ($ventaItem->producto) {
            $ventaItem->producto->actualizarPrecioVentaAutomatico();
        }
    }
}
