<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Venta extends Model implements Auditable
{
    use AuditableTrait;
    protected $fillable = [
        'numero_venta',
        'numero_interno',
        'cliente_id',
        'user_id',
        'fecha_venta',
        'subtotal',
        'descuento_general',
        'impuestos',
        'total',
        'metodo_pago',
        'tipo_venta',
        'monto_recibido',
        'cambio',
        'estado',
        'observaciones',
        'notas'
    ];

    protected $casts = [
        'fecha_venta' => 'date',
        'subtotal' => 'decimal:2',
        'descuento_general' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
        'monto_recibido' => 'decimal:2',
        'cambio' => 'decimal:2'
    ];

    protected $attributes = [
        'subtotal' => 0,
        'descuento_general' => 0,
        'impuestos' => 0,
        'total' => 0,
        'monto_recibido' => 0,
        'cambio' => 0,
        'estado' => 'procesada',
        'metodo_pago' => 'efectivo',
        'tipo_venta' => 'contado'
    ];

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(VentaItem::class);
    }

    // Scopes
    public function scopeDelMes($query, $mes = null, $año = null)
    {
        $mes = $mes ?? now()->month;
        $año = $año ?? now()->year;
        return $query->whereMonth('fecha_venta', $mes)->whereYear('fecha_venta', $año);
    }

    public function scopeDelAño($query, $año = null)
    {
        $año = $año ?? now()->year;
        return $query->whereYear('fecha_venta', $año);
    }

    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?? now()->toDateString();
        return $query->whereDate('fecha_venta', $fecha);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePorMetodoPago($query, $metodo)
    {
        return $query->where('metodo_pago', $metodo);
    }

    public function scopePorTipoVenta($query, $tipo)
    {
        return $query->where('tipo_venta', $tipo);
    }

    // Accessors
    public function getEstadoLabelAttribute(): string
    {
        $estados = [
            'pendiente' => 'Pendiente',
            'procesada' => 'Procesada',
            'entregada' => 'Entregada',
            'cancelada' => 'Cancelada',
            'devuelta' => 'Devuelta'
        ];

        return $estados[$this->estado] ?? ucfirst($this->estado);
    }

    public function getMetodoPagoLabelAttribute(): string
    {
        $metodos = [
            'efectivo' => 'Efectivo',
            'tarjeta' => 'Tarjeta',
            'transferencia' => 'Transferencia',
            'credito' => 'Crédito',
            'mixto' => 'Mixto'
        ];

        return $metodos[$this->metodo_pago] ?? ucfirst($this->metodo_pago);
    }

    public function getTipoVentaLabelAttribute(): string
    {
        $tipos = [
            'contado' => 'Contado',
            'credito' => 'Crédito',
            'mayoreo' => 'Mayoreo',
            'menudeo' => 'Menudeo'
        ];

        return $tipos[$this->tipo_venta] ?? ucfirst($this->tipo_venta);
    }

    // Métodos auxiliares
    public function generarNumeroInterno(): string
    {
        $año = now()->year;
        $ultimoNumero = static::whereYear('created_at', $año)
            ->whereNotNull('numero_interno')
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimoNumero && preg_match('/VEN-(\d+)-(\d{4})/', $ultimoNumero->numero_interno, $matches)) {
            $numero = intval($matches[1]) + 1;
        } else {
            $numero = 1;
        }

        return sprintf('VEN-%04d-%s', $numero, $año);
    }

    public function calcularTotales(): void
    {
        $this->load('items');
        
        $subtotal = $this->items->sum(function ($item) {
            return $item->cantidad * $item->precio_unitario - $item->descuento;
        });

        $this->subtotal = $subtotal;
        $this->total = $subtotal - $this->descuento_general + $this->impuestos;
        
        if ($this->monto_recibido > 0) {
            $this->cambio = max(0, $this->monto_recibido - $this->total);
        }
    }

    // Método para procesar venta con lógica PEPS
    public function procesarVenta(): bool
    {
        if ($this->estado === 'procesada') {
            return true; // Ya está procesada
        }

        // Procesar cada item de la venta
        foreach ($this->items as $item) {
            if (!$this->procesarItemVenta($item)) {
                return false; // No hay suficiente stock para algún item
            }
        }

        // Cambiar estado a procesada
        $this->update(['estado' => 'procesada']);

        // Si es crédito, crear registro de cuenta por cobrar
        if ($this->tipo_venta === 'credito') {
            $this->crearCuentaPorCobrar();
        }

        return true;
    }

    private function procesarItemVenta($item): bool
    {
        $cantidadPendiente = $item->cantidad;
        
        // Obtener inventarios disponibles ordenados por PEPS (fecha más antigua primero)
        $inventarios = Inventario::where('producto_id', $item->producto_id)
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
        $producto = $item->producto;
        $producto->stock_actual -= $item->cantidad;
        $producto->save();

        return true;
    }

    private function crearCuentaPorCobrar(): void
    {
        if (!$this->cliente) {
            return;
        }

        \App\Models\PagoCliente::create([
            'cliente_id' => $this->cliente_id,
            'monto' => -$this->total, // Negativo porque es deuda
            'fecha_pago' => null, // Sin fecha porque está pendiente
            'concepto' => 'Venta #' . $this->numero_venta,
            'tipo' => 'credito',
            'user_id' => $this->user_id
        ]);
    }

    // Métodos estáticos para reportes
    public static function ventasDelPeriodo($periodo = 'mes')
    {
        $query = static::where('estado', '!=', 'cancelada');
        
        switch ($periodo) {
            case 'dia':
                return $query->whereDate('fecha_venta', now()->toDateString())->sum('total');
            case 'mes':
                return $query->whereMonth('fecha_venta', now()->month)
                    ->whereYear('fecha_venta', now()->year)->sum('total');
            case 'año':
                return $query->whereYear('fecha_venta', now()->year)->sum('total');
            default:
                return 0;
        }
    }

    public static function ventasPorMetodoPago($fechaInicio = null, $fechaFin = null)
    {
        $query = static::where('estado', '!=', 'cancelada');
        
        if ($fechaInicio) $query->whereDate('fecha_venta', '>=', $fechaInicio);
        if ($fechaFin) $query->whereDate('fecha_venta', '<=', $fechaFin);
        
        return $query->selectRaw('metodo_pago, SUM(total) as total, COUNT(*) as cantidad')
            ->groupBy('metodo_pago')
            ->get();
    }

    public static function ventasPorCliente($fechaInicio = null, $fechaFin = null)
    {
        $query = static::with('cliente')->where('estado', '!=', 'cancelada');
        
        if ($fechaInicio) $query->whereDate('fecha_venta', '>=', $fechaInicio);
        if ($fechaFin) $query->whereDate('fecha_venta', '<=', $fechaFin);
        
        return $query->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
            ->selectRaw('clientes.nombre, SUM(ventas.total) as total, COUNT(ventas.id) as cantidad')
            ->groupBy('clientes.id', 'clientes.nombre')
            ->orderBy('total', 'desc')
            ->get();
    }

    public static function getEstadosDisponibles(): array
    {
        return [
            'pendiente' => 'Pendiente',
            'procesada' => 'Procesada',
            'entregada' => 'Entregada',
            'cancelada' => 'Cancelada',
            'devuelta' => 'Devuelta'
        ];
    }

    public static function getMetodosPagoDisponibles(): array
    {
        return [
            'efectivo' => 'Efectivo',
            'tarjeta' => 'Tarjeta',
            'transferencia' => 'Transferencia',
            'credito' => 'Crédito',
            'mixto' => 'Mixto'
        ];
    }

    public static function getTiposVentaDisponibles(): array
    {
        return [
            'contado' => 'Contado',
            'credito' => 'Crédito',
            'mayoreo' => 'Mayoreo',
            'menudeo' => 'Menudeo'
        ];
    }
}
