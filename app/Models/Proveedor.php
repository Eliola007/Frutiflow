<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $fillable = [
        'nombre',
        'documento',
        'tipo_documento',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'contacto_principal',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->nombre} ({$this->documento})";
    }
}
