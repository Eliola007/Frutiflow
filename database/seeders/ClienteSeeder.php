<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Juan Pérez García',
                'rfc' => 'PEGJ800101ABC',
                'telefono' => '+52 55 1234 5678',
                'direccion' => 'Av. Reforma 123, Col. Centro, CDMX',
                'limite_credito' => 50000.00,
                'dias_credito' => 30,
                'saldo_pendiente' => 15000.00,
                'estado_credito' => 'activo',
                'descuento_especial' => 5.00,
                'total_compras' => 125000.00,
                'ultima_compra' => now()->subDays(5),
                'activo' => true,
            ],
            [
                'nombre' => 'María González López',
                'rfc' => 'GOLM850215DEF',
                'telefono' => '+52 55 9876 5432',
                'direccion' => 'Calle Morelos 456, Col. Centro, Guadalajara',
                'limite_credito' => 30000.00,
                'dias_credito' => 15,
                'saldo_pendiente' => 28000.00,
                'estado_credito' => 'suspendido',
                'descuento_especial' => 0.00,
                'total_compras' => 85000.00,
                'ultima_compra' => now()->subDays(10),
                'activo' => true,
            ],
            [
                'nombre' => 'Cliente Mostrador',
                'rfc' => null,
                'telefono' => null,
                'direccion' => 'N/A',
                'limite_credito' => 0.00,
                'dias_credito' => 0,
                'saldo_pendiente' => 0.00,
                'estado_credito' => 'activo',
                'descuento_especial' => 0.00,
                'total_compras' => 0.00,
                'ultima_compra' => null,
                'activo' => true,
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
