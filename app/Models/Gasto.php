<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    protected $fillable = [
        'numero_comprobante',
        'numero_interno',
        'categoria',
        'concepto_gasto_id',
        'proveedor_id',
        'producto_id',
        'descripcion',
        'monto',
        'fecha_gasto',
        'user_id',
        'proveedor_gasto',
        'observaciones',
        'archivo_soporte',
        'es_recurrente',
        'periodo_recurrencia'
    ];

    protected $casts = [
        'fecha_gasto' => 'date',
        'monto' => 'decimal:2',
        'es_recurrente' => 'boolean'
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function conceptoGasto(): BelongsTo
    {
        return $this->belongsTo(ConceptoGasto::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    // Scopes
    public function scopeDelMes($query, $mes = null, $año = null)
    {
        $mes = $mes ?? now()->month;
        $año = $año ?? now()->year;
        return $query->whereMonth('fecha_gasto', $mes)->whereYear('fecha_gasto', $año);
    }

    public function scopeDelAño($query, $año = null)
    {
        $año = $año ?? now()->year;
        return $query->whereYear('fecha_gasto', $año);
    }

    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?? now()->toDateString();
        return $query->whereDate('fecha_gasto', $fecha);
    }

    public function scopeRecurrentes($query)
    {
        return $query->where('es_recurrente', true);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopePorGrupo($query, $grupo)
    {
        return $query->whereHas('conceptoGasto', function ($q) use ($grupo) {
            $q->where('grupo', $grupo);
        });
    }

    // Accessors
    public function getCategoriaLabelAttribute(): string
    {
        $categorias = [
            'operativo' => 'Operativo',
            'logistica' => 'Logística',
            'personal' => 'Personal',
            'servicios' => 'Servicios',
            'mantenimiento' => 'Mantenimiento',
            'sanitario' => 'Sanitario',
            'administrativo' => 'Administrativo',
            'comisiones' => 'Comisiones',
            'otros' => 'Otros'
        ];

        // Si la categoría existe en el array, retorna el label, sino formatea el valor
        return $categorias[$this->categoria] ?? ucfirst(str_replace('_', ' ', $this->categoria));
    }

    public function getPeriodoRecurrenciaLabelAttribute(): ?string
    {
        if (!$this->periodo_recurrencia) return null;

        $periodos = [
            'semanal' => 'Semanal',
            'quincenal' => 'Quincenal',
            'mensual' => 'Mensual',
            'bimestral' => 'Bimestral',
            'trimestral' => 'Trimestral',
            'anual' => 'Anual'
        ];

        return $periodos[$this->periodo_recurrencia] ?? $this->periodo_recurrencia;
    }

    // Métodos auxiliares
    public function generarNumeroInterno(): string
    {
        $año = now()->year;
        $ultimoNumero = static::whereYear('created_at', $año)
            ->whereNotNull('numero_interno')
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimoNumero && preg_match('/GAS-(\d+)-(\d{4})/', $ultimoNumero->numero_interno, $matches)) {
            $numero = intval($matches[1]) + 1;
        } else {
            $numero = 1;
        }

        return sprintf('GAS-%04d-%s', $numero, $año);
    }

    // Métodos estáticos para reportes
    public static function gastosPorCategoria($fechaInicio = null, $fechaFin = null)
    {
        $query = static::query();
        
        if ($fechaInicio) $query->whereDate('fecha_gasto', '>=', $fechaInicio);
        if ($fechaFin) $query->whereDate('fecha_gasto', '<=', $fechaFin);
        
        return $query->selectRaw('categoria, SUM(monto) as total')
            ->groupBy('categoria')
            ->get();
    }

    public static function getCategoriasDisponibles(): array
    {
        $categoriasEstaticas = [
            'operativo' => 'Operativo',
            'logistica' => 'Logística',
            'personal' => 'Personal',
            'servicios' => 'Servicios',
            'mantenimiento' => 'Mantenimiento',
            'sanitario' => 'Sanitario',
            'administrativo' => 'Administrativo',
            'comisiones' => 'Comisiones',
            'otros' => 'Otros'
        ];

        // Obtener categorías personalizadas de la BD
        $categoriasPersonalizadas = static::select('categoria')
            ->whereNotIn('categoria', array_keys($categoriasEstaticas))
            ->whereNotNull('categoria')
            ->distinct()
            ->pluck('categoria')
            ->mapWithKeys(function ($categoria) {
                return [$categoria => ucfirst(str_replace('_', ' ', $categoria))];
            })
            ->toArray();

        return array_merge($categoriasEstaticas, $categoriasPersonalizadas);
    }

    public static function gastosPorGrupo($fechaInicio = null, $fechaFin = null)
    {
        $query = static::with('conceptoGasto');
        
        if ($fechaInicio) $query->whereDate('fecha_gasto', '>=', $fechaInicio);
        if ($fechaFin) $query->whereDate('fecha_gasto', '<=', $fechaFin);
        
        return $query->join('concepto_gastos', 'gastos.concepto_gasto_id', '=', 'concepto_gastos.id')
            ->selectRaw('concepto_gastos.grupo, SUM(gastos.monto) as total')
            ->whereNotNull('concepto_gastos.grupo')
            ->groupBy('concepto_gastos.grupo')
            ->get();
    }

    public static function gastosDelPeriodo($periodo = 'mes')
    {
        $query = static::query();
        
        switch ($periodo) {
            case 'dia':
                return $query->whereDate('fecha_gasto', now()->toDateString())->sum('monto');
            case 'mes':
                return $query->whereMonth('fecha_gasto', now()->month)
                    ->whereYear('fecha_gasto', now()->year)->sum('monto');
            case 'año':
                return $query->whereYear('fecha_gasto', now()->year)->sum('monto');
            default:
                return 0;
        }
    }
}
