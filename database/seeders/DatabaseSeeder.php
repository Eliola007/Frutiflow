<?php

namespace Database\Seeders;

use App\Models\User;
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
            UserSeeder::class, // Crear usuarios primero
        ]);

        // Ejecutar seeders que NO requieren usuario
        $this->call([
            ProductoSeeder::class,
            ClienteSeeder::class,
            ProveedorSeeder::class,
            ConceptoGastoSeeder::class,
        ]);

        // Ejecutar seeders que requieren datos existentes (usuario y entidades)
        $this->call([
            PagoClienteSeeder::class,
            PagoProveedorSeeder::class,
        ]);
    }
}
