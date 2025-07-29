<?php

namespace App\Policies;

use App\Models\CorteCaja;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CorteCajaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['ver cortes de caja', 'administrar cortes de caja']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CorteCaja $corteCaja): bool
    {
        return $user->hasAnyPermission(['ver cortes de caja', 'administrar cortes de caja']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyPermission(['crear cortes de caja', 'administrar cortes de caja']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CorteCaja $corteCaja): bool
    {
        // Solo el administrador puede editar si el corte está marcado como editable
        // O el cajero puede editar sus propios cortes del día actual
        if ($user->hasPermissionTo('administrar cortes de caja')) {
            return $corteCaja->editable || true; // Admin siempre puede editar
        }
        
        if ($user->hasPermissionTo('crear cortes de caja')) {
            return $corteCaja->usuario_id === $user->id && 
                   $corteCaja->fecha->isToday() && 
                   $corteCaja->editable;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CorteCaja $corteCaja): bool
    {
        // Solo administradores pueden eliminar cortes
        return $user->hasPermissionTo('administrar cortes de caja');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CorteCaja $corteCaja): bool
    {
        return $user->hasPermissionTo('administrar cortes de caja');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CorteCaja $corteCaja): bool
    {
        return $user->hasPermissionTo('administrar cortes de caja');
    }
}
