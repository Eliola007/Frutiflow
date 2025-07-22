<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venta extends Model
{
    protected $fillable = [
        'numero_venta',
        'cliente_id',
        'user_id',
        'fecha_venta',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'descuento',
        'impuestos',
        'total',
        'metodo_pago',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_venta' => 'date',
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    // Método para procesar venta con lógica PEPS
    public function procesarVenta(): bool
    {
        $cantidadPendiente = $this->cantidad;
        
        // Obtener inventarios disponibles ordenados por PEPS (fecha más antigua primero)
        $inventarios = Inventario::where('producto_id', $this->producto_id)
            ->where('estado', 'disponible')
            ->where('cantidad_actual', '>', 0)
            ->orderBy('fecha_ingreso', 'asc')
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        if ($inventarios->sum('cantidad_actual') < $cantidadPendiente) {
            return false; // No hay suficiente stock
        }

        foreach ($inventarios as $inventario) {
            if ($cantidadPendiente <= 0) break;

            $cantidadAUsar = min($cantidadPendiente, $inventario->cantidad_actual);
            
            // Reducir cantidad en inventario
            $inventario->cantidad_actual -= $cantidadAUsar;
            
            if ($inventario->cantidad_actual <= 0) {
                $inventario->estado = 'agotado';
            }
            
            $inventario->save();
            $cantidadPendiente -= $cantidadAUsar;
        }

        // Actualizar stock del producto
        $producto = $this->producto;
        $producto->stock_actual -= $this->cantidad;
        $producto->save();

        return true;
    }
}
