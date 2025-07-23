<?php

namespace App\Observers;

use App\Models\PagoProveedor;

class PagoProveedorObserver
{
    /**
     * Handle the PagoProveedor "created" event.
     */
    public function created(PagoProveedor $pagoProveedor): void
    {
        $this->actualizarSaldoProveedor($pagoProveedor);
    }

    /**
     * Handle the PagoProveedor "updated" event.
     */
    public function updated(PagoProveedor $pagoProveedor): void
    {
        $this->actualizarSaldoProveedor($pagoProveedor);
    }

    /**
     * Handle the PagoProveedor "deleted" event.
     */
    public function deleted(PagoProveedor $pagoProveedor): void
    {
        $this->actualizarSaldoProveedor($pagoProveedor);
    }

    /**
     * Actualizar saldo del proveedor
     */
    private function actualizarSaldoProveedor(PagoProveedor $pagoProveedor): void
    {
        $proveedor = $pagoProveedor->proveedor;
        
        if (!$proveedor) {
            return;
        }

        // Calcular nuevo saldo basado en todos los pagos
        $totalPagos = $proveedor->pagos()->sum('monto');
        
        // Aquí podrías agregar lógica más compleja considerando las compras
        // Por ahora, simplemente reducimos el saldo pendiente con los pagos
        $saldoAnterior = $proveedor->saldo_pendiente + $pagoProveedor->monto;
        $nuevoSaldo = max(0, $saldoAnterior - $totalPagos);
        
        $proveedor->update([
            'saldo_pendiente' => $nuevoSaldo,
            'updated_at' => now()
        ]);
        
        // Actualizar estado de crédito automáticamente
        $proveedor->actualizarEstadoCredito();
    }
}
