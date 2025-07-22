<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    protected $fillable = [
        'numero_comprobante',
        'categoria',
        'descripcion',
        'monto',
        'fecha_gasto',
        'user_id',
        'proveedor_gasto',
        'observaciones',
        'archivo_soporte'
    ];

    protected $casts = [
        'fecha_gasto' => 'date',
        'monto' => 'decimal:2'
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCategoriaLabelAttribute(): string
    {
        $categorias = [
            'transporte' => 'Transporte',
            'almacenamiento' => 'Almacenamiento',
            'empaque' => 'Empaque',
            'administrativo' => 'Administrativo',
            'servicios' => 'Servicios',
            'otros' => 'Otros'
        ];

        return $categorias[$this->categoria] ?? $this->categoria;
    }
}
