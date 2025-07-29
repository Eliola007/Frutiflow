<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Crear el rol de administrador si no existe
        $adminRole = Role::where('name', 'admin')->first();
        
        if (!$adminRole) {
            $adminRole = Role::create([
                'name' => 'admin',
                'guard_name' => 'web'
            ]);
        }

        // Crear el usuario administrador
        User::create([
            'name' => 'Admin',
            'email' => 'admin@frutiflow.com',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
            'activo' => true
        ]);
    }
}
