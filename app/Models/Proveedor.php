<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Helpers\CurrencyHelper;
use Carbon\Carbon;

class Proveedor extends Model
{
    protected $fillable = [
        'nombre',
        'rfc',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'contacto_principal',
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

    /**
     * Relación con Compras
     */
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    /**
     * Relación con Pagos de Proveedor
     */
    public function pagos(): HasMany
    {
        return $this->hasMany(PagoProveedor::class);
    }

    /**
     * Accessor para nombre completo
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->nombre} ({$this->documento})";
    }

    /**
     * Accessor para límite de crédito formateado
     */
    public function getLimiteCreditoFormattedAttribute(): string
    {
        return CurrencyHelper::format($this->limite_credito);
    }

    /**
     * Accessor para saldo pendiente formateado
     */
    public function getSaldoPendienteFormattedAttribute(): string
    {
        return CurrencyHelper::format($this->saldo_pendiente);
    }

    /**
     * Accessor para total de compras formateado
     */
    public function getTotalComprasFormattedAttribute(): string
    {
        return CurrencyHelper::format($this->total_compras);
    }

    /**
     * Calcular porcentaje de crédito usado
     */
    public function getPorcentajeCreditoUsadoAttribute(): float
    {
        if ($this->limite_credito <= 0) {
            return 0;
        }
        
        return ($this->saldo_pendiente / $this->limite_credito) * 100;
    }

    /**
     * Verificar si puede realizar compra por un monto
     */
    public function puedeComprarMonto(float $monto): bool
    {
        if ($this->estado_credito === 'bloqueado') {
            return false;
        }
        
        if ($this->limite_credito <= 0) {
            return true; // Sin límite de crédito
        }
        
        return ($this->saldo_pendiente + $monto) <= $this->limite_credito;
    }

    /**
     * Actualizar estado de crédito automáticamente
     */
    public function actualizarEstadoCredito(): void
    {
        if ($this->limite_credito <= 0) {
            return; // Sin límite, no se actualiza estado
        }
        
        $porcentaje = $this->porcentaje_credito_usado;
        
        if ($porcentaje >= 100) {
            $this->estado_credito = 'bloqueado';
        } elseif ($porcentaje >= 90) {
            $this->estado_credito = 'suspendido';
        } else {
            $this->estado_credito = 'activo';
        }
        
        $this->save();
    }

    /**
     * Scope para proveedores con deuda
     */
    public function scopeConDeuda($query)
    {
        return $query->where('saldo_pendiente', '>', 0);
    }

    /**
     * Scope para proveedores por estado de crédito
     */
    public function scopePorEstadoCredito($query, string $estado)
    {
        return $query->where('estado_credito', $estado);
    }

    /**
     * Scope para proveedores con límite próximo a agotar
     */
    public function scopeLimiteProximoAgotar($query, float $porcentaje = 80)
    {
        return $query->whereRaw('(saldo_pendiente / NULLIF(limite_credito, 0)) >= ?', [$porcentaje / 100])
                    ->where('limite_credito', '>', 0);
    }

    /**
     * Validar formato del RFC
     */
    public static function validarRFC(string $rfc): bool
    {
        // El RFC debe tener exactamente 13 caracteres alfanuméricos
        return preg_match('/^[A-Z0-9]{13}$/', strtoupper($rfc));
    }

    /**
     * Mutator para el RFC - convertir a mayúsculas
     */
    public function setRfcAttribute($value)
    {
        $this->attributes['rfc'] = strtoupper($value);
    }
}
