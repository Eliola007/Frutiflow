<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Juan Pérez García',
                'documento' => '12345678901',
                'tipo_documento' => 'cedula',
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
                'documento' => '09876543210',
                'tipo_documento' => 'cedula',
                'telefono' => '+52 55 9876 5432',
                'direccion' => 'Calle Morelos 456, Col. Centro, Guadalajara, Jalisco',
                'limite_credito' => 30000.00,
                'dias_credito' => 15,
                'saldo_pendiente' => 28000.00,
                'estado_credito' => 'suspendido',
                'descuento_especial' => null,
                'total_compras' => 85000.00,
                'ultima_compra' => now()->subDays(10),
                'activo' => true,
            ],
            [
                'nombre' => 'Carlos Rodríguez Hernández',
                'documento' => '55512345678',
                'tipo_documento' => 'cedula',
                'telefono' => '+52 81 5555 7777',
                'direccion' => 'Av. Universidad 789, Col. San Pedro, Monterrey, NL',
                'limite_credito' => 75000.00,
                'dias_credito' => 30,
                'saldo_pendiente' => 0.00,
                'estado_credito' => 'activo',
                'descuento_especial' => 10.00,
                'total_compras' => 245000.00,
                'ultima_compra' => now()->subDays(3),
                'activo' => true,
            ],
            [
                'nombre' => 'Ana Martínez Sánchez',
                'documento' => '77798765432',
                'tipo_documento' => 'cedula',
                'telefono' => '+52 33 8888 9999',
                'direccion' => 'Blvd. Tlaquepaque 321, Col. Centro, Tlaquepaque, Jalisco',
                'limite_credito' => 20000.00,
                'dias_credito' => 20,
                'saldo_pendiente' => 19500.00,
                'estado_credito' => 'bloqueado',
                'descuento_especial' => null,
                'total_compras' => 45000.00,
                'ultima_compra' => now()->subDays(25),
                'activo' => true,
            ],
            [
                'nombre' => 'Roberto Silva Mendoza',
                'documento' => '11122334455',
                'tipo_documento' => 'cedula',
                'telefono' => '+52 55 4444 3333',
                'direccion' => 'Calle Juárez 654, Col. Roma Norte, CDMX',
                'limite_credito' => 100000.00,
                'dias_credito' => 45,
                'saldo_pendiente' => 35000.00,
                'estado_credito' => 'activo',
                'descuento_especial' => 15.00,
                'total_compras' => 380000.00,
                'ultima_compra' => now()->subDays(2),
                'activo' => true,
            ],
            [
                'nombre' => 'Luisa Torres Castillo',
                'documento' => '99988877766',
                'tipo_documento' => 'cedula',
                'telefono' => '+52 998 7777 6666',
                'direccion' => 'Av. Tulum 987, SM 22, Cancún, Quintana Roo',
                'limite_credito' => 40000.00,
                'dias_credito' => 30,
                'saldo_pendiente' => 12000.00,
                'estado_credito' => 'activo',
                'descuento_especial' => 8.00,
                'total_compras' => 95000.00,
                'ultima_compra' => now()->subDays(7),
                'activo' => true,
            ],
            [
                'nombre' => 'Javier Morales Vega',
                'documento' => '33344455566',
                'tipo_documento' => 'cedula',
                'telefono' => '+52 222 1111 2222',
                'direccion' => 'Calle 5 de Mayo 147, Col. Centro, Puebla, Puebla',
                'limite_credito' => 15000.00,
                'dias_credito' => 15,
                'saldo_pendiente' => 0.00,
                'estado_credito' => 'activo',
                'descuento_especial' => null,
                'total_compras' => 25000.00,
                'ultima_compra' => now()->subDays(12),
                'activo' => true,
            ],
            [
                'nombre' => 'Patricia Jiménez Cruz',
                'documento' => '44455566677',
                'tipo_documento' => 'cedula',
                'telefono' => '+52 55 6666 8888',
                'direccion' => 'Insurgentes Sur 258, Col. Del Valle, CDMX',
                'limite_credito' => 60000.00,
                'dias_credito' => 30,
                'saldo_pendiente' => 45000.00,
                'estado_credito' => 'suspendido',
                'descuento_especial' => 12.00,
                'total_compras' => 180000.00,
                'ultima_compra' => now()->subDays(8),
                'activo' => true,
            ],
            [
                'nombre' => 'Empresa Frutos del Valle SA de CV',
                'documento' => 'FDV123456789',
                'tipo_documento' => 'nit',
                'telefono' => '+52 55 5555 0000',
                'direccion' => 'Periférico Sur 1000, Col. Jardines del Pedregal, CDMX',
                'limite_credito' => 200000.00,
                'dias_credito' => 45,
                'saldo_pendiente' => 85000.00,
                'estado_credito' => 'activo',
                'descuento_especial' => 20.00,
                'total_compras' => 1200000.00,
                'ultima_compra' => now(),
                'activo' => true,
            ],
            [
                'nombre' => 'Cliente Sin Crédito',
                'documento' => '66677788899',
                'tipo_documento' => 'cedula',
                'telefono' => '+52 55 6667 7788',
                'direccion' => 'Av. Juárez 369, Col. Cuauhtémoc, CDMX',
                'limite_credito' => 0.00, // Cliente de contado
                'dias_credito' => 0,
                'saldo_pendiente' => 0.00,
                'estado_credito' => 'activo',
                'descuento_especial' => null,
                'total_compras' => 25000.00,
                'ultima_compra' => now()->subDays(90),
                'activo' => true,
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
