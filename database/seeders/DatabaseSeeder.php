<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ProductoSeeder::class,
        ]);

        // Crear usuario administrador con rol
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@frutiflow.com',
            'password' => bcrypt('password123'),
            'role_id' => 1, // Rol de Administrador
            'activo' => true,
        ]);
    }
}
