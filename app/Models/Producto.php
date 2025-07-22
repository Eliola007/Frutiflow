<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria',
        'unidad_medida',
        'precio_venta',
        'precio_minimo',
        'stock_minimo',
        'stock_actual',
        'dias_vencimiento',
        'requiere_refrigeracion',
        'activo'
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
        'precio_minimo' => 'decimal:2',
        'requiere_refrigeracion' => 'boolean',
        'activo' => 'boolean'
    ];

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class);
    }

    public function getStockDisponibleAttribute(): float
    {
        return $this->inventarios()
            ->where('estado', 'disponible')
            ->where('cantidad_actual', '>', 0)
            ->sum('cantidad_actual');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->codigo} - {$this->nombre}";
    }
}
