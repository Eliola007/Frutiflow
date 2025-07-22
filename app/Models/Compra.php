<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compra extends Model
{
    protected $fillable = [
        'numero_factura',
        'proveedor_id',
        'user_id',
        'fecha_compra',
        'fecha_vencimiento',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'descuento',
        'impuestos',
        'total',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'fecha_vencimiento' => 'date',
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class);
    }

    // Método para crear entrada en inventario al recibir la compra
    public function recibirCompra(): void
    {
        if ($this->estado === 'recibida') {
            return;
        }

        // Crear registro en inventario
        Inventario::create([
            'producto_id' => $this->producto_id,
            'compra_id' => $this->id,
            'lote' => $this->numero_factura . '-' . now()->format('Ymd'),
            'fecha_ingreso' => $this->fecha_compra,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'cantidad_inicial' => $this->cantidad,
            'cantidad_actual' => $this->cantidad,
            'precio_costo' => $this->precio_unitario,
            'estado' => 'disponible'
        ]);

        // Actualizar stock del producto
        $producto = $this->producto;
        $producto->stock_actual += $this->cantidad;
        $producto->save();

        // Cambiar estado a recibida
        $this->update(['estado' => 'recibida']);
    }
}
