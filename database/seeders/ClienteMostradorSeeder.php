<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClienteMostradorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear cliente mostrador si no existe
        Cliente::firstOrCreate(
            ['email' => 'mostrador@frutiflow.com'],
            [
                'nombre' => 'MOSTRADOR',
                'telefono' => '000-000-0000',
                'email' => 'mostrador@frutiflow.com',
                'direccion' => 'Venta directa en mostrador',
                'limite_credito' => 0.00,
                'dias_credito' => 0,
                'saldo_pendiente' => 0.00,
                'estado_credito' => 'activo',
                'descuento_especial' => 0.00,
                'total_compras' => 0.00,
                'activo' => true
            ]
        );

        $this->command->info('Cliente MOSTRADOR creado exitosamente.');
    }
}
