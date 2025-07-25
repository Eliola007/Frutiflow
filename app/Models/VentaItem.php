<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaItem extends Model
{
    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal',
        'observaciones'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    protected $attributes = [
        'cantidad' => 1,
        'precio_unitario' => 0,
        'descuento' => 0,
        'subtotal' => 0
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    // Mutator para calcular subtotal automáticamente
    public function setSubtotalAttribute($value)
    {
        $cantidad = $this->attributes['cantidad'] ?? 1;
        $precio = $this->attributes['precio_unitario'] ?? 0;
        $descuento = $this->attributes['descuento'] ?? 0;
        
        $this->attributes['subtotal'] = ($cantidad * $precio) - ($descuento * $cantidad);
    }

    // Accessor para obtener el total del item
    public function getTotalAttribute(): float
    {
        return ($this->cantidad * $this->precio_unitario) - ($this->descuento * $this->cantidad);
    }

    // Método para calcular y asignar el subtotal
    public function calcularSubtotal(): void
    {
        $this->subtotal = ($this->cantidad * $this->precio_unitario) - ($this->descuento * $this->cantidad);
    }

    // Boot method para calcular subtotal automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ventaItem) {
            if (!$ventaItem->subtotal) {
                $cantidad = $ventaItem->cantidad ?? 1;
                $precio = $ventaItem->precio_unitario ?? 0;
                $descuento = $ventaItem->descuento ?? 0;
                $ventaItem->subtotal = ($cantidad * $precio) - ($descuento * $cantidad);
            }
        });

        static::updating(function ($ventaItem) {
            $cantidad = $ventaItem->cantidad ?? 1;
            $precio = $ventaItem->precio_unitario ?? 0;
            $descuento = $ventaItem->descuento ?? 0;
            $ventaItem->subtotal = ($cantidad * $precio) - ($descuento * $cantidad);
        });
    }

    // Scope para filtrar por producto
    public function scopePorProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    // Método para verificar disponibilidad de stock
    public function verificarStock(): bool
    {
        $stockDisponible = Inventario::where('producto_id', $this->producto_id)
            ->where('estado', 'disponible')
            ->sum('cantidad_actual');

        return $stockDisponible >= $this->cantidad;
    }

    // Método para obtener el costo promedio del producto
    public function getCostoPromedio(): float
    {
        return Inventario::where('producto_id', $this->producto_id)
            ->where('cantidad_actual', '>', 0)
            ->avg('precio_costo') ?? 0;
    }

    // Método para calcular la ganancia del item
    public function getGanancia(): float
    {
        $costoPromedio = $this->getCostoPromedio();
        $totalVenta = $this->total;
        $costoTotal = $costoPromedio * $this->cantidad;
        
        return $totalVenta - $costoTotal;
    }

    // Método para calcular el margen de ganancia porcentual
    public function getMargenGanancia(): float
    {
        $costoPromedio = $this->getCostoPromedio();
        if ($costoPromedio <= 0) return 0;
        
        $ganancia = $this->getGanancia();
        $costoTotal = $costoPromedio * $this->cantidad;
        
        return $costoTotal > 0 ? ($ganancia / $costoTotal) * 100 : 0;
    }
}
