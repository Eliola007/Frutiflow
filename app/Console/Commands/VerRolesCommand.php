<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role as CustomRole;
use Spatie\Permission\Models\Role as SpatieRole;

class VerRolesCommand extends Command
{
    protected $signature = 'roles:ver';
    protected $description = 'Ver todos los roles disponibles';

    public function handle()
    {
        $this->info('=== ROLES PERSONALIZADOS ===');
        $customRoles = CustomRole::all(['id', 'nombre', 'name', 'guard_name']);
        $this->table(['ID', 'Nombre', 'Name (Spatie)', 'Guard'], $customRoles->map(function($role) {
            return [$role->id, $role->nombre, $role->name ?? 'NULL', $role->guard_name ?? 'NULL'];
        }));

        $this->info('=== ROLES SPATIE ===');
        $spatieRoles = SpatieRole::all(['id', 'name', 'guard_name']);
        $this->table(['ID', 'Name', 'Guard'], $spatieRoles->toArray());
    }
}
