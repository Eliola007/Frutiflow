<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PagoCliente;
use App\Models\Cliente;
use App\Models\User;

class PagoClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el primer usuario (admin) para asociar los pagos
        $admin = User::first();
        
        // Obtener algunos clientes para crear pagos
        $clientes = Cliente::limit(5)->get();
        
        foreach ($clientes as $cliente) {
            // Crear entre 1-3 pagos por cliente
            $numeroPagos = rand(1, 3);
            
            for ($i = 0; $i < $numeroPagos; $i++) {
                $monto = rand(1000, 15000);
                
                PagoCliente::create([
                    'cliente_id' => $cliente->id,
                    'monto' => $monto,
                    'tipo_pago' => ['abono', 'pago_completo', 'ajuste'][rand(0, 2)],
                    'metodo_pago' => ['efectivo', 'transferencia', 'cheque', 'tarjeta'][rand(0, 3)],
                    'referencia' => rand(100000, 999999),
                    'observaciones' => 'Pago de ejemplo generado automáticamente',
                    'fecha_pago' => now()->subDays(rand(1, 30)),
                    'user_id' => $admin->id,
                ]);
            }
        }
        
        // Crear algunos pagos específicos con datos más realistas
        $pagosEspecificos = [
            [
                'cliente_id' => 1, // Juan Pérez García
                'monto' => 5000.00,
                'tipo_pago' => 'abono',
                'metodo_pago' => 'transferencia',
                'referencia' => 'TRANS001234',
                'observaciones' => 'Abono parcial a cuenta',
                'fecha_pago' => now()->subDays(3),
                'user_id' => $admin->id,
            ],
            [
                'cliente_id' => 2, // María González López
                'monto' => 15000.00,
                'tipo_pago' => 'pago_completo',
                'metodo_pago' => 'efectivo',
                'referencia' => null,
                'observaciones' => 'Pago completo de facturas pendientes',
                'fecha_pago' => now()->subDays(1),
                'user_id' => $admin->id,
            ],
            [
                'cliente_id' => 3, // Carlos Rodríguez Hernández
                'monto' => 25000.00,
                'tipo_pago' => 'pago_completo',
                'metodo_pago' => 'cheque',
                'referencia' => 'CHE789456',
                'observaciones' => 'Pago con cheque bancario',
                'fecha_pago' => now()->subDays(5),
                'user_id' => $admin->id,
            ],
            [
                'cliente_id' => 5, // Roberto Silva Mendoza
                'monto' => 10000.00,
                'tipo_pago' => 'abono',
                'metodo_pago' => 'tarjeta',
                'referencia' => 'CARD456789',
                'observaciones' => 'Pago con tarjeta de crédito',
                'fecha_pago' => now()->subDays(2),
                'user_id' => $admin->id,
            ],
        ];
        
        foreach ($pagosEspecificos as $pago) {
            PagoCliente::create($pago);
        }
    }
}
