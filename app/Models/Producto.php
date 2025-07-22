<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\CurrencyHelper;

class Producto extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'calidad',
        'tamaño',
        'grupo',
        'configuracion_avanzada',
        'unidad_base',
        'factor_conversion',
        'precio_compra_referencia',
        'precio_venta_sugerido',
        'activo'
    ];

    protected $casts = [
        'configuracion_avanzada' => 'boolean',
        'factor_conversion' => 'decimal:3',
        'precio_compra_referencia' => 'decimal:2',
        'precio_venta_sugerido' => 'decimal:2',
        'stock_actual' => 'decimal:3',
        'stock_disponible' => 'decimal:3',
        'costo_promedio' => 'decimal:4',
        'activo' => 'boolean'
    ];

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeInactivos($query)
    {
        return $query->where('activo', false);
    }

    protected $appends = [
        'stock_disponible',
        'valor_inventario',
        'stock_por_lotes',
        'rotacion_inventario',
        'precio_compra_formatted',
        'precio_venta_formatted'
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

    // Accessors y Mutators
    public function getFullNameAttribute(): string
    {
        return "{$this->codigo} - {$this->nombre} {$this->calidad} {$this->tamaño}";
    }

    public function getGrupoDisplayAttribute(): string
    {
        return "{$this->grupo} - {$this->nombre}";
    }

    public function getPrecioCompraFormattedAttribute(): string
    {
        return $this->precio_compra_referencia 
            ? CurrencyHelper::format($this->precio_compra_referencia)
            : 'No definido';
    }

    public function getPrecioVentaFormattedAttribute(): string
    {
        return $this->precio_venta_sugerido 
            ? CurrencyHelper::format($this->precio_venta_sugerido)
            : 'No definido';
    }

    public function getStockDisponibleAttribute(): float
    {
        return $this->inventarios()
            ->where('estado', 'disponible')
            ->where('cantidad_actual', '>', 0)
            ->sum('cantidad_actual');
    }

    public function getValorInventarioAttribute(): float
    {
        return $this->stock_disponible * $this->costo_promedio;
    }

    public function getStockPorLotesAttribute(): array
    {
        return $this->inventarios()
            ->where('estado', 'disponible')
            ->where('cantidad_actual', '>', 0)
            ->orderBy('fecha_ingreso', 'asc')
            ->get()
            ->map(function ($inventario) {
                return [
                    'lote' => $inventario->lote,
                    'cantidad' => $inventario->cantidad_actual,
                    'costo' => $inventario->precio_costo,
                    'fecha_ingreso' => $inventario->fecha_ingreso,
                    'fecha_vencimiento' => $inventario->fecha_vencimiento,
                    'dias_restantes' => $inventario->fecha_vencimiento 
                        ? $inventario->fecha_vencimiento->diffInDays(now()) 
                        : null
                ];
            })->toArray();
    }

    public function getRotacionInventarioAttribute(): array
    {
        $periodo = 30; // últimos 30 días
        $fechaInicio = now()->subDays($periodo);
        
        $ventasRecientes = $this->ventas()
            ->where('fecha_venta', '>=', $fechaInicio)
            ->where('estado', 'completada')
            ->sum('cantidad');

        $promedioVentasDiarias = $ventasRecientes / $periodo;
        $diasStock = $promedioVentasDiarias > 0 
            ? $this->stock_disponible / $promedioVentasDiarias 
            : 0;

        return [
            'ventas_periodo' => $ventasRecientes,
            'promedio_diario' => round($promedioVentasDiarias, 2),
            'dias_stock' => round($diasStock, 0),
            'rotacion' => $diasStock > 0 ? round($periodo / $diasStock, 2) : 0
        ];
    }

    // Métodos PEPS
    public function actualizarStockYCostoPEPS(): void
    {
        $inventariosDisponibles = $this->inventarios()
            ->where('estado', 'disponible')
            ->where('cantidad_actual', '>', 0)
            ->get();

        // Calcular stock total
        $stockTotal = $inventariosDisponibles->sum('cantidad_actual');

        // Calcular costo promedio ponderado PEPS
        $valorTotal = $inventariosDisponibles->sum(function ($inventario) {
            return $inventario->cantidad_actual * $inventario->precio_costo;
        });

        $costoPromedio = $stockTotal > 0 ? $valorTotal / $stockTotal : 0;

        // Actualizar producto
        $this->update([
            'stock_total' => $stockTotal,
            'costo_promedio' => $costoPromedio
        ]);
    }

    public function agregarStock(float $cantidad, float $precioUnitario, int $compraId, ?string $fechaVencimiento = null): void
    {
        // Crear nuevo lote en inventario
        Inventario::create([
            'producto_id' => $this->id,
            'compra_id' => $compraId,
            'lote' => 'COMP-' . $compraId . '-' . now()->format('Ymd-His'),
            'fecha_ingreso' => now(),
            'fecha_vencimiento' => $fechaVencimiento,
            'cantidad_inicial' => $cantidad,
            'cantidad_actual' => $cantidad,
            'precio_costo' => $precioUnitario,
            'estado' => 'disponible'
        ]);

        // Actualizar stock y costo promedio
        $this->actualizarStockYCostoPEPS();
    }

    public function reducirStockPEPS(float $cantidad): array
    {
        $cantidadPendiente = $cantidad;
        $lotesUtilizados = [];
        
        // Obtener lotes disponibles ordenados por PEPS
        $lotes = $this->inventarios()
            ->where('estado', 'disponible')
            ->where('cantidad_actual', '>', 0)
            ->orderBy('fecha_ingreso', 'asc')
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        if ($lotes->sum('cantidad_actual') < $cantidad) {
            throw new \Exception('Stock insuficiente');
        }

        foreach ($lotes as $lote) {
            if ($cantidadPendiente <= 0) break;

            $cantidadAUsar = min($cantidadPendiente, $lote->cantidad_actual);
            
            // Registrar lote utilizado
            $lotesUtilizados[] = [
                'lote' => $lote->lote,
                'cantidad' => $cantidadAUsar,
                'costo' => $lote->precio_costo
            ];

            // Reducir cantidad en lote
            $lote->cantidad_actual -= $cantidadAUsar;
            
            if ($lote->cantidad_actual <= 0) {
                $lote->estado = 'agotado';
            }
            
            $lote->save();
            $cantidadPendiente -= $cantidadAUsar;
        }

        // Actualizar stock y costo promedio
        $this->actualizarStockYCostoPEPS();

        return $lotesUtilizados;
    }

    // Scopes
    public function scopePorGrupo($query, string $grupo)
    {
        return $query->where('grupo', $grupo);
    }

    public function scopePorCalidad($query, string $calidad)
    {
        return $query->where('calidad', $calidad);
    }

    public function scopeConStock($query)
    {
        return $query->where('stock_total', '>', 0);
    }

    public function scopeSinStock($query)
    {
        return $query->where('stock_total', '<=', 0);
    }

    public function scopeBajoMinimo($query)
    {
        return $query->whereColumn('stock_total', '<=', 'stock_minimo');
    }

    public function scopeSinCompras($query)
    {
        return $query->whereDoesntHave('compras');
    }

    // Conversiones de unidades
    public function convertirACajas(float $cantidad, string $unidadOrigen): float
    {
        switch ($unidadOrigen) {
            case 'tonelada':
                return $cantidad * $this->factor_tonelada;
            case 'kilo':
                return $cantidad / $this->factor_caja_kilos;
            case 'caja':
                return $cantidad;
            default:
                return $cantidad;
        }
    }

    public function convertirAKilos(float $cantidad, string $unidadOrigen): float
    {
        switch ($unidadOrigen) {
            case 'caja':
                return $cantidad * $this->factor_caja_kilos;
            case 'tonelada':
                return $cantidad * $this->factor_tonelada * $this->factor_caja_kilos;
            case 'kilo':
                return $cantidad;
            default:
                return $cantidad;
        }
    }

    // Validaciones
    public function puedeEliminarse(): bool
    {
        return $this->stock_total <= 0 && $this->compras()->count() === 0;
    }

    public function puedeEditarse(): bool
    {
        $user = Auth::user();
        return $user && $user->role && $user->role->nombre === 'Administrador';
    }
}
