<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Helpers\CurrencyHelper;
use Carbon\Carbon;

class Cliente extends Model
{
    protected $fillable = [
        'nombre',
        'documento',
        'tipo_documento',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'activo',
        'limite_credito',
        'dias_credito',
        'saldo_pendiente',
        'estado_credito',
        'descuento_especial',
        'ultima_compra',
        'total_compras'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'limite_credito' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'descuento_especial' => 'decimal:2',
        'total_compras' => 'decimal:2',
        'ultima_compra' => 'datetime'
    ];

    protected $appends = [
        'credito_disponible',
        'credito_utilizado_porcentaje',
        'estado_credito_label',
        'dias_sin_comprar'
    ];

    // Relaciones
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(PagoCliente::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->nombre} ({$this->documento})";
    }

    public function getCreditoDisponibleAttribute(): float
    {
        return $this->limite_credito - $this->saldo_pendiente;
    }

    public function getCreditoUtilizadoPorcentajeAttribute(): float
    {
        if ($this->limite_credito <= 0) return 0;
        return ($this->saldo_pendiente / $this->limite_credito) * 100;
    }

    public function getEstadoCreditoLabelAttribute(): string
    {
        return match($this->estado_credito) {
            'activo' => 'Activo',
            'suspendido' => 'Suspendido',
            'bloqueado' => 'Bloqueado',
            default => $this->estado_credito
        };
    }

    public function getDiasSinComprarAttribute(): ?int
    {
        if (!$this->ultima_compra) return null;
        return $this->ultima_compra->diffInDays(now());
    }

    public function getLimiteCreditoFormattedAttribute(): string
    {
        return CurrencyHelper::format($this->limite_credito);
    }

    public function getSaldoPendienteFormattedAttribute(): string
    {
        return CurrencyHelper::format($this->saldo_pendiente);
    }

    public function getCreditoDisponibleFormattedAttribute(): string
    {
        return CurrencyHelper::format($this->credito_disponible);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeConCredito($query)
    {
        return $query->where('limite_credito', '>', 0);
    }

    public function scopeConDeuda($query)
    {
        return $query->where('saldo_pendiente', '>', 0);
    }

    public function scopeCreditoSuspendido($query)
    {
        return $query->where('estado_credito', 'suspendido');
    }

    public function scopeCreditoBloqueado($query)
    {
        return $query->where('estado_credito', 'bloqueado');
    }

    // Métodos de negocio
    public function puedeComprarACredito(float $monto): bool
    {
        if ($this->estado_credito !== 'activo') return false;
        return ($this->saldo_pendiente + $monto) <= $this->limite_credito;
    }

    public function agregarDeuda(float $monto): void
    {
        $this->saldo_pendiente += $monto;
        $this->ultima_compra = now();
        $this->total_compras += $monto;
        $this->save();
    }

    public function realizarPago(float $monto): void
    {
        $this->saldo_pendiente = max(0, $this->saldo_pendiente - $monto);
        $this->save();
    }

    public function verificarEstadoCredito(): void
    {
        $porcentajeUtilizado = $this->credito_utilizado_porcentaje;
        
        if ($porcentajeUtilizado >= 100) {
            $this->estado_credito = 'bloqueado';
        } elseif ($porcentajeUtilizado >= 90) {
            $this->estado_credito = 'suspendido';
        } else {
            $this->estado_credito = 'activo';
        }
        
        $this->save();
    }
}
