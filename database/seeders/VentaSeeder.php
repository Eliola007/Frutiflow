<?php

namespace Database\Seeders;

use App\Models\Venta;
use App\Models\VentaItem;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Seeder;

class VentaSeeder extends Seeder
{
    public function run(): void
    {
        $usuario = User::first();
        if (!$usuario) {
            $this->command->warn('No hay usuarios en la base de datos. Ejecuta UserSeeder primero.');
            return;
        }

        $clientes = Cliente::all();
        $productos = Producto::all();

        if ($clientes->isEmpty() || $productos->isEmpty()) {
            $this->command->warn('No hay suficientes clientes o productos. Ejecuta los seeders correspondientes primero.');
            return;
        }

        $clienteMostrador = $clientes->where('nombre', 'MOSTRADOR')->first();

        // Ventas del día actual
        $ventasHoy = [
            [
                'numero_venta' => 'V-' . now()->format('ymd') . '-001',
                'cliente_id' => $clienteMostrador->id,
                'fecha_venta' => now(),
                'subtotal' => 0, // Se calculará después
                'descuento_general' => 0,
                'impuestos' => 0,
                'total' => 0, // Se calculará después
                'tipo_venta' => 'contado',
                'metodo_pago' => 'efectivo',
                'monto_recibido' => 470,
                'monto_anticipo' => 0,
                'cambio' => 0,
                'estado' => 'entregada',
                'user_id' => $usuario->id,
                'items' => [
                    ['producto_id' => $productos->random()->id, 'cantidad' => 2, 'precio_unitario' => 150, 'descuento' => 0],
                    ['producto_id' => $productos->random()->id, 'cantidad' => 1, 'precio_unitario' => 280, 'descuento' => 10],
                ]
            ],
            [
                'numero_venta' => 'V-' . now()->format('ymd') . '-002',
                'cliente_id' => $clientes->where('nombre', '!=', 'MOSTRADOR')->random()->id,
                'fecha_venta' => now(),
                'subtotal' => 0,
                'descuento_general' => 0,
                'impuestos' => 0,
                'total' => 0,
                'tipo_venta' => 'credito',
                'metodo_pago' => 'credito',
                'monto_recibido' => 0,
                'monto_anticipo' => 100,
                'cambio' => 0,
                'estado' => 'procesada',
                'user_id' => $usuario->id,
                'items' => [
                    ['producto_id' => $productos->random()->id, 'cantidad' => 5, 'precio_unitario' => 120, 'descuento' => 20],
                ]
            ],
        ];

        // Ventas del mes pasado para comparativas
        $ventasMesPasado = [
            [
                'numero_venta' => 'V-' . now()->subMonth()->format('ymd') . '-001',
                'cliente_id' => $clienteMostrador->id,
                'fecha_venta' => now()->subMonth()->subDays(5),
                'subtotal' => 0,
                'descuento_general' => 0,
                'impuestos' => 0,
                'total' => 0,
                'tipo_venta' => 'contado',
                'metodo_pago' => 'tarjeta',
                'monto_recibido' => 600,
                'monto_anticipo' => 0,
                'cambio' => 0,
                'estado' => 'entregada',
                'user_id' => $usuario->id,
                'items' => [
                    ['producto_id' => $productos->random()->id, 'cantidad' => 3, 'precio_unitario' => 200, 'descuento' => 0],
                ]
            ],
            [
                'numero_venta' => 'V-' . now()->subMonth()->format('ymd') . '-002',
                'cliente_id' => $clientes->where('nombre', '!=', 'MOSTRADOR')->random()->id,
                'fecha_venta' => now()->subMonth()->subDays(3),
                'subtotal' => 0,
                'descuento_general' => 0,
                'impuestos' => 0,
                'total' => 0,
                'tipo_venta' => 'contado',
                'metodo_pago' => 'efectivo',
                'monto_recibido' => 700,
                'monto_anticipo' => 0,
                'cambio' => 0,
                'estado' => 'entregada',
                'user_id' => $usuario->id,
                'items' => [
                    ['producto_id' => $productos->random()->id, 'cantidad' => 2, 'precio_unitario' => 180, 'descuento' => 0],
                    ['producto_id' => $productos->random()->id, 'cantidad' => 1, 'precio_unitario' => 350, 'descuento' => 15],
                ]
            ],
        ];

        // Crear todas las ventas
        foreach (array_merge($ventasHoy, $ventasMesPasado) as $ventaData) {
            $items = $ventaData['items'];
            unset($ventaData['items']);

            // Crear la venta
            $venta = new Venta($ventaData);
            
            // Generar número interno si no existe
            if (!$venta->numero_interno) {
                $venta->numero_interno = $venta->generarNumeroInterno();
            }
            
            $venta->save();

            // Crear los items de la venta
            $subtotal = 0;
            foreach ($items as $itemData) {
                $itemData['venta_id'] = $venta->id;
                $itemData['subtotal'] = ($itemData['cantidad'] * $itemData['precio_unitario']) - $itemData['descuento'];
                $subtotal += $itemData['subtotal'];
                
                VentaItem::create($itemData);
            }

            // Actualizar totales de la venta
            $venta->subtotal = $subtotal;
            $venta->total = $subtotal - ($venta->descuento_general ?? 0) + ($venta->impuestos ?? 0);
            $venta->save();
        }

        $this->command->info('Se crearon ' . count(array_merge($ventasHoy, $ventasMesPasado)) . ' ventas de prueba.');
    }
}
