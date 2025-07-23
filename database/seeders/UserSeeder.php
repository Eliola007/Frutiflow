<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $usuarios = [
            [
                'name' => 'Administrador Principal',
                'email' => 'admin@frutiflow.com',
                'password' => Hash::make('admin123'),
                'role_id' => 1, // Administrador
                'activo' => true,
            ],
            [
                'name' => 'Usuario Demo',
                'email' => 'demo@frutiflow.com',
                'password' => Hash::make('demo123'),
                'role_id' => 1, // Administrador
                'activo' => true,
            ],
            [
                'name' => 'Vendedor Principal',
                'email' => 'vendedor@frutiflow.com',
                'password' => Hash::make('vendedor123'),
                'role_id' => 2, // Vendedor
                'activo' => true,
            ],
            [
                'name' => 'Gerente Inventario',
                'email' => 'inventario@frutiflow.com',
                'password' => Hash::make('inventario123'),
                'role_id' => 3, // Empleado
                'activo' => true,
            ],
            [
                'name' => 'Cajero',
                'email' => 'cajero@frutiflow.com',
                'password' => Hash::make('cajero123'),
                'role_id' => 3, // Empleado
                'activo' => true,
            ],
        ];

        foreach ($usuarios as $userData) {
            // Solo crear si no existe
            if (!User::where('email', $userData['email'])->exists()) {
                $user = User::create($userData);
                $this->command->info('Usuario creado: ' . $user->email);
            } else {
                $this->command->info('Usuario ya existe: ' . $userData['email']);
            }
        }
        
        $this->command->info('Total usuarios en sistema: ' . User::count());
    }
}
