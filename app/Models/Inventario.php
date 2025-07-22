<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Inventario extends Model
{
    protected $fillable = [
        'producto_id',
        'compra_id',
        'lote',
        'fecha_ingreso',
        'fecha_vencimiento',
        'cantidad_inicial',
        'cantidad_actual',
        'precio_costo',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_vencimiento' => 'date',
        'cantidad_inicial' => 'decimal:2',
        'cantidad_actual' => 'decimal:2',
        'precio_costo' => 'decimal:2'
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    // Scope para obtener inventarios disponibles ordenados por PEPS
    public function scopePeps(Builder $query, int $productoId): Builder
    {
        return $query->where('producto_id', $productoId)
            ->where('estado', 'disponible')
            ->where('cantidad_actual', '>', 0)
            ->orderBy('fecha_ingreso', 'asc')
            ->orderBy('fecha_vencimiento', 'asc');
    }

    // Scope para productos próximos a vencer
    public function scopeProximosAVencer(Builder $query, int $dias = 7): Builder
    {
        return $query->where('fecha_vencimiento', '<=', Carbon::now()->addDays($dias))
            ->where('fecha_vencimiento', '>', Carbon::now())
            ->where('estado', 'disponible')
            ->where('cantidad_actual', '>', 0);
    }

    // Scope para productos vencidos
    public function scopeVencidos(Builder $query): Builder
    {
        return $query->where('fecha_vencimiento', '<', Carbon::now())
            ->where('estado', '!=', 'vencido');
    }

    // Método para marcar como vencido
    public function marcarComoVencido(): void
    {
        if ($this->fecha_vencimiento && $this->fecha_vencimiento->isPast()) {
            $this->update(['estado' => 'vencido']);
            
            // Reducir stock del producto
            $producto = $this->producto;
            $producto->stock_actual -= $this->cantidad_actual;
            $producto->save();
        }
    }

    // Calcular valor del inventario
    public function getValorTotalAttribute(): float
    {
        return $this->cantidad_actual * $this->precio_costo;
    }

    // Verificar si está próximo a vencer
    public function getProximoAVencerAttribute(): bool
    {
        if (!$this->fecha_vencimiento) return false;
        
        return $this->fecha_vencimiento->diffInDays(Carbon::now()) <= 7 
            && $this->fecha_vencimiento->isFuture();
    }

    // Verificar si está vencido
    public function getVencidoAttribute(): bool
    {
        if (!$this->fecha_vencimiento) return false;
        
        return $this->fecha_vencimiento->isPast();
    }
}
