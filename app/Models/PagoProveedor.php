<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Helpers\CurrencyHelper;

class PagoProveedor extends Model
{
    protected $fillable = [
        'proveedor_id',
        'monto',
        'tipo_pago',
        'metodo_pago',
        'fecha_pago',
        'referencia',
        'observaciones',
        'user_id'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    /**
     * Relación con Proveedor
     */
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Relación con Usuario que registró el pago
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor para monto formateado
     */
    public function getMontoFormattedAttribute(): string
    {
        return CurrencyHelper::format($this->monto);
    }

    /**
     * Accessor para tipo de pago formateado
     */
    public function getTipoPagoFormattedAttribute(): string
    {
        return match($this->tipo_pago) {
            'pago' => 'Pago',
            'anticipo' => 'Anticipo',
            'abono' => 'Abono',
            default => $this->tipo_pago
        };
    }

    /**
     * Accessor para método de pago formateado
     */
    public function getMetodoPagoFormattedAttribute(): string
    {
        return match($this->metodo_pago) {
            'efectivo' => 'Efectivo',
            'transferencia' => 'Transferencia',
            'cheque' => 'Cheque',
            'tarjeta' => 'Tarjeta',
            default => $this->metodo_pago
        };
    }

    /**
     * Scope para pagos de un mes específico
     */
    public function scopeDelMes($query, $month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        return $query->whereMonth('fecha_pago', $month)
                    ->whereYear('fecha_pago', $year);
    }

    /**
     * Scope para pagos por tipo
     */
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo_pago', $tipo);
    }

    /**
     * Scope para pagos por método
     */
    public function scopePorMetodo($query, string $metodo)
    {
        return $query->where('metodo_pago', $metodo);
    }
}
