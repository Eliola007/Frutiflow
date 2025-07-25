<?php

namespace App\Observers;

use App\Models\Compra;
use Filament\Notifications\Notification;

class CompraObserver
{
    /**
     * Handle the Compra "created" event.
     */
    public function created(Compra $compra): void
    {
        // Verificar diferencia de precio con promedio para cada item
        foreach ($compra->items as $item) {
            $diferencia = $compra->diferenciaPrecioPromedio($item->producto_id, $item->precio_unitario);
            
            if ($diferencia['tiene_diferencia']) {
                $mensaje = sprintf(
                    'El precio de compra del producto %s ($%s) difiere un %.1f%% del promedio histórico ($%s)',
                    $item->producto->nombre,
                    number_format($item->precio_unitario, 2),
                    $diferencia['porcentaje'],
                    number_format($diferencia['promedio'], 2)
                );
                
                Notification::make()
                    ->warning()
                    ->title('Diferencia de Precio Significativa')
                    ->body($mensaje)
                    ->persistent()
                    ->send();
            }
        }
    }

    /**
     * Handle the Compra "updated" event.
     */
    public function updated(Compra $compra): void
    {
        // Si cambió a estado recibida, crear pago de enganche si aplica
        if ($compra->wasChanged('estado') && $compra->estado === 'recibida') {
            if ($compra->tipo_pago === 'credito_enganche' && $compra->monto_enganche > 0) {
                $compra->crearPagoEnganche();
            }
        }
    }

    /**
     * Handle the Compra "deleted" event.
     */
    public function deleted(Compra $compra): void
    {
        // Si la compra estaba recibida, revertir el inventario
        if ($compra->estado === 'recibida') {
            // Eliminar registros de inventario relacionados
            $compra->inventarios()->delete();
            
            // Revertir stock de cada producto
            foreach ($compra->items as $item) {
                $producto = $item->producto;
                if ($producto) {
                    $producto->stock_actual -= $item->cantidad;
                    $producto->save();
                }
            }
        }
    }

    /**
     * Handle the Compra "restored" event.
     */
    public function restored(Compra $compra): void
    {
        //
    }

    /**
     * Handle the Compra "force deleted" event.
     */
    public function forceDeleted(Compra $compra): void
    {
        //
    }
}
