<?php

namespace Database\Seeders;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class, // Crear roles, permisos y usuario admin
        ]);

        // Ejecutar seeders que NO requieren usuario
        $this->call([
            ProductoSeeder::class,
            // ClienteSeeder::class, // Comentado para evitar clientes de prueba
            ProveedorSeeder::class,
            ConceptoGastoSeeder::class,
        ]);

        // Ejecutar seeders que requieren datos existentes (usuario y entidades)
        $this->call([
            // PagoClienteSeeder::class, // Comentado porque no hay clientes de prueba
            // PagoProveedorSeeder::class, // Comentado para evitar datos de prueba
            ClienteMostradorSeeder::class,
        ]);
    }
}
