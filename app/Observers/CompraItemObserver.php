<?php

namespace App\Observers;

use App\Models\CompraItem;

class CompraItemObserver
{
    /**
     * Handle the CompraItem "created" event.
     */
    public function created(CompraItem $compraItem): void
    {
        $this->actualizarPrecioReferencia($compraItem);
    }

    /**
     * Handle the CompraItem "updated" event.
     */
    public function updated(CompraItem $compraItem): void
    {
        $this->actualizarPrecioReferencia($compraItem);
    }

    /**
     * Handle the CompraItem "deleted" event.
     */
    public function deleted(CompraItem $compraItem): void
    {
        $this->actualizarPrecioReferencia($compraItem);
    }

    /**
     * Handle the CompraItem "restored" event.
     */
    public function restored(CompraItem $compraItem): void
    {
        $this->actualizarPrecioReferencia($compraItem);
    }

    /**
     * Handle the CompraItem "force deleted" event.
     */
    public function forceDeleted(CompraItem $compraItem): void
    {
        $this->actualizarPrecioReferencia($compraItem);
    }

    /**
     * Actualizar el precio de compra de referencia del producto
     */
    private function actualizarPrecioReferencia(CompraItem $compraItem): void
    {
        if ($compraItem->producto) {
            $compraItem->producto->actualizarPrecioReferenciaAutomatico();
        }
    }
}
