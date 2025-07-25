<?php

namespace Database\Seeders;

use App\Models\Gasto;
use App\Models\ConceptoGasto;
use App\Models\User;
use Illuminate\Database\Seeder;

class GastoSeeder extends Seeder
{
    public function run(): void
    {
        $usuario = User::first();
        if (!$usuario) {
            $this->command->warn('No hay usuarios en la base de datos. Ejecuta UserSeeder primero.');
            return;
        }

        $conceptos = ConceptoGasto::all();
        if ($conceptos->isEmpty()) {
            $this->command->warn('No hay conceptos de gasto. Ejecuta ConceptoGastoSeeder primero.');
            return;
        }

        // Gastos del mes actual
        $gastosMes = [
            [
                'numero_comprobante' => 'G001-2025',
                'concepto_gasto_id' => $conceptos->where('nombre', 'FLETE LOCAL')->first()?->id,
                'categoria' => 'logistica',
                'descripcion' => 'Flete para transporte de mercancía',
                'monto' => 150000,
                'fecha_gasto' => now()->subDays(5),
                'user_id' => $usuario->id,
            ],
            [
                'numero_comprobante' => 'G002-2025',
                'concepto_gasto_id' => $conceptos->where('nombre', 'LUZ')->first()?->id,
                'categoria' => 'servicios',
                'descripcion' => 'Factura de energía eléctrica',
                'monto' => 280000,
                'fecha_gasto' => now()->subDays(3),
                'user_id' => $usuario->id,
                'es_recurrente' => true,
                'periodo_recurrencia' => 'mensual',
            ],
            [
                'numero_comprobante' => 'G003-2025',
                'concepto_gasto_id' => $conceptos->where('nombre', 'SALARIOS')->first()?->id,
                'categoria' => 'personal',
                'descripcion' => 'Pago de nómina',
                'monto' => 2500000,
                'fecha_gasto' => now()->subDays(2),
                'user_id' => $usuario->id,
                'es_recurrente' => true,
                'periodo_recurrencia' => 'mensual',
            ],
            [
                'numero_comprobante' => 'G004-2025',
                'concepto_gasto_id' => $conceptos->where('nombre', 'GASOLINA')->first()?->id,
                'categoria' => 'logistica',
                'descripcion' => 'Combustible para vehículos',
                'monto' => 180000,
                'fecha_gasto' => now()->subDay(),
                'user_id' => $usuario->id,
            ],
            [
                'numero_comprobante' => 'G005-2025',
                'concepto_gasto_id' => $conceptos->where('nombre', 'ARRIENDO')->first()?->id,
                'categoria' => 'administrativo',
                'descripcion' => 'Arriendo de bodega',
                'monto' => 1200000,
                'fecha_gasto' => now(),
                'user_id' => $usuario->id,
                'es_recurrente' => true,
                'periodo_recurrencia' => 'mensual',
            ],
        ];

        // Gastos del día anterior
        $gastosAyer = [
            [
                'numero_comprobante' => 'G006-2025',
                'concepto_gasto_id' => $conceptos->where('nombre', 'PROPINA TOMATE')->first()?->id,
                'categoria' => 'personal',
                'descripcion' => 'Propina para descarga de tomate',
                'monto' => 25000,
                'fecha_gasto' => now()->subDay(),
                'user_id' => $usuario->id,
            ],
            [
                'numero_comprobante' => 'G007-2025',
                'concepto_gasto_id' => $conceptos->where('nombre', 'CAJAS')->first()?->id,
                'categoria' => 'operativo',
                'descripcion' => 'Compra de cajas para empaque',
                'monto' => 80000,
                'fecha_gasto' => now()->subDay(),
                'user_id' => $usuario->id,
            ],
        ];

        // Gastos de hoy
        $gastosHoy = [
            [
                'numero_comprobante' => 'G008-2025',
                'concepto_gasto_id' => $conceptos->where('nombre', 'VIÁTICOS')->first()?->id,
                'categoria' => 'otros',
                'descripcion' => 'Viáticos para viaje',
                'monto' => 120000,
                'fecha_gasto' => now(),
                'user_id' => $usuario->id,
            ],
        ];

        // Crear todos los gastos
        foreach (array_merge($gastosMes, $gastosAyer, $gastosHoy) as $gastoData) {
            $gasto = new Gasto($gastoData);
            
            // Generar número interno si no existe
            if (!$gasto->numero_interno) {
                $gasto->numero_interno = $gasto->generarNumeroInterno();
            }
            
            $gasto->save();
        }

        $this->command->info('Se crearon ' . count(array_merge($gastosMes, $gastosAyer, $gastosHoy)) . ' gastos de prueba.');
    }
}
