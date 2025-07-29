<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos (solo si no existen)
        $permissions = [
            'gestionar usuarios',
            'gestionar roles',
            'gestionar productos',
            'gestionar inventario',
            'gestionar ventas',
            'gestionar compras',
            'gestionar gastos',
            'gestionar cortes',
            'ver reportes',
            'ver cortes de caja',
            'crear cortes de caja',
            'administrar cortes de caja'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $cajero = Role::firstOrCreate(['name' => 'cajero']);
        $cajero->syncPermissions([
            'gestionar ventas',
            'gestionar gastos',
            'gestionar cortes',
            'ver reportes',
            'ver cortes de caja',
            'crear cortes de caja'
        ]);

        // Crear usuario administrador
        $user = User::firstOrCreate(
            ['email' => 'admin@frutiflow.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123')
            ]
        );
        
        $user->assignRole('admin');
    }
}
