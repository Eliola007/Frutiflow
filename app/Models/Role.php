<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'permisos',
        'activo'
    ];

    protected $casts = [
        'permisos' => 'array',
        'activo' => 'boolean'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
