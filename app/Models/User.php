<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class User extends Authenticatable implements Auditable
{
    use HasFactory, Notifiable, HasRoles, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function gastos()
    {
        return $this->hasMany(Gasto::class);
    }

    /**
     * Productos asignados al usuario (para Socios Comerciales)
     */
    public function productosAsignados()
    {
        return $this->belongsToMany(Producto::class, 'user_productos')
                    ->withPivot(['asignado_en', 'asignado_hasta', 'activo'])
                    ->withTimestamps()
                    ->wherePivot('activo', true);
    }

    /**
     * Todos los productos asignados (incluidos inactivos)
     */
    public function todosLosProductosAsignados()
    {
        return $this->belongsToMany(Producto::class, 'user_productos')
                    ->withPivot(['asignado_en', 'asignado_hasta', 'activo'])
                    ->withTimestamps();
    }
}
