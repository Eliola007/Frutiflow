<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->nombre} ({$this->documento})";
    }
}
