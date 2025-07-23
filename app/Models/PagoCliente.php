<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Helpers\CurrencyHelper;

class PagoCliente extends Model
{
    protected $fillable = [
        'cliente_id',
        'venta_id',
        'monto',
        'tipo_pago',
        'metodo_pago',
        'referencia',
        'observaciones',
        'fecha_pago',
        'user_id'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'date'
    ];

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Accessors
    public function getMontoFormattedAttribute(): string
    {
        return CurrencyHelper::format($this->monto);
    }

    public function getTipoPagoLabelAttribute(): string
    {
        return match($this->tipo_pago) {
            'abono' => 'Abono Parcial',
            'pago_completo' => 'Pago Completo',
            'ajuste' => 'Ajuste',
            default => $this->tipo_pago
        };
    }

    public function getMetodoPagoLabelAttribute(): string
    {
        return match($this->metodo_pago) {
            'efectivo' => 'Efectivo',
            'transferencia' => 'Transferencia',
            'cheque' => 'Cheque',
            'tarjeta' => 'Tarjeta',
            default => $this->metodo_pago
        };
    }

    // Scopes
    public function scopePorCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopePorFecha($query, $fechaInicio, $fechaFin = null)
    {
        if ($fechaFin) {
            return $query->whereBetween('fecha_pago', [$fechaInicio, $fechaFin]);
        }
        return $query->whereDate('fecha_pago', $fechaInicio);
    }
}
