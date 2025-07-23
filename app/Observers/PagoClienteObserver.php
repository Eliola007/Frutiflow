<?php

namespace App\Observers;

use App\Models\PagoCliente;

class PagoClienteObserver
{
    /**
     * Handle the PagoCliente "created" event.
     */
    public function created(PagoCliente $pagoCliente): void
    {
        // Restar el monto del pago al saldo pendiente del cliente
        $cliente = $pagoCliente->cliente;
        $cliente->realizarPago($pagoCliente->monto);
        $cliente->verificarEstadoCredito();
    }

    /**
     * Handle the PagoCliente "updated" event.
     */
    public function updated(PagoCliente $pagoCliente): void
    {
        // Si cambió el monto, ajustar el saldo del cliente
        if ($pagoCliente->wasChanged('monto') || $pagoCliente->wasChanged('cliente_id')) {
            // Revertir el pago anterior
            if ($pagoCliente->getOriginal('cliente_id')) {
                $clienteAnterior = \App\Models\Cliente::find($pagoCliente->getOriginal('cliente_id'));
                if ($clienteAnterior) {
                    $clienteAnterior->agregarDeuda($pagoCliente->getOriginal('monto'));
                    $clienteAnterior->verificarEstadoCredito();
                }
            }
            
            // Aplicar el nuevo pago
            $cliente = $pagoCliente->cliente;
            $cliente->realizarPago($pagoCliente->monto);
            $cliente->verificarEstadoCredito();
        }
    }

    /**
     * Handle the PagoCliente "deleted" event.
     */
    public function deleted(PagoCliente $pagoCliente): void
    {
        // Revertir el pago (agregar de vuelta al saldo pendiente)
        $cliente = $pagoCliente->cliente;
        $cliente->agregarDeuda($pagoCliente->monto);
        $cliente->verificarEstadoCredito();
    }

    /**
     * Handle the PagoCliente "restored" event.
     */
    public function restored(PagoCliente $pagoCliente): void
    {
        // Aplicar nuevamente el pago
        $cliente = $pagoCliente->cliente;
        $cliente->realizarPago($pagoCliente->monto);
        $cliente->verificarEstadoCredito();
    }
}
