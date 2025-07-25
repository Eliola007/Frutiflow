<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConceptoGasto extends Model
{
    protected $fillable = [
        'nombre',
        'grupo',
        'categoria',
        'es_recurrente',
        'activo',
        'descripcion'
    ];

    protected $casts = [
        'es_recurrente' => 'boolean',
        'activo' => 'boolean'
    ];

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class, 'concepto_gasto_id');
    }

    // Scope para conceptos activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Scope para conceptos recurrentes
    public function scopeRecurrentes($query)
    {
        return $query->where('es_recurrente', true);
    }

    // Scope por grupo
    public function scopePorGrupo($query, $grupo)
    {
        return $query->where('grupo', $grupo);
    }

    // Scope por categoría
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    // Accessor para obtener el nombre completo con grupo
    public function getNombreCompletoAttribute(): string
    {
        return $this->grupo ? "{$this->grupo} - {$this->nombre}" : $this->nombre;
    }

    // Método para obtener las categorías disponibles
    public static function getCategorias(): array
    {
        return [
            'operativo' => 'Operativo',
            'logistica' => 'Logística',
            'personal' => 'Personal',
            'servicios' => 'Servicios',
            'mantenimiento' => 'Mantenimiento',
            'sanitario' => 'Sanitario/Fitosanitario',
            'administrativo' => 'Administrativo',
            'comisiones' => 'Comisiones',
            'otros' => 'Otros'
        ];
    }

    // Método para obtener los grupos únicos
    public static function getGrupos(): array
    {
        return static::whereNotNull('grupo')
            ->distinct()
            ->pluck('grupo')
            ->sort()
            ->toArray();
    }
}
