<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            [
                'codigo' => 'MANZ001',
                'nombre' => 'Manzana Red Delicious',
                'calidad' => '1A',
                'tamaño' => 'Large',
                'grupo' => 'Manzanas',
                'configuracion_avanzada' => false,
                'unidad_base' => 'caja',
                'factor_conversion' => 20.000,
                'precio_compra_referencia' => 3500.00,
                'precio_venta_sugerido' => 5500.00,
                'activo' => true,
            ],
            [
                'codigo' => 'MANZ002',
                'nombre' => 'Manzana Verde Granny Smith',
                'calidad' => '1A',
                'tamaño' => 'Medium',
                'grupo' => 'Manzanas',
                'configuracion_avanzada' => false,
                'unidad_base' => 'caja',
                'factor_conversion' => 18.000,
                'precio_compra_referencia' => 3200.00,
                'precio_venta_sugerido' => 5000.00,
                'activo' => true,
            ],
            [
                'codigo' => 'BAN001',
                'nombre' => 'Banano Cavendish',
                'calidad' => '1A',
                'tamaño' => 'Medium',
                'grupo' => 'Bananos',
                'configuracion_avanzada' => false,
                'unidad_base' => 'caja',
                'factor_conversion' => 22.000,
                'precio_compra_referencia' => 2200.00,
                'precio_venta_sugerido' => 3500.00,
                'activo' => true,
            ],
            [
                'codigo' => 'NAR001',
                'nombre' => 'Naranja Valencia',
                'calidad' => '1A',
                'tamaño' => 'Large',
                'grupo' => 'Cítricos',
                'configuracion_avanzada' => false,
                'unidad_base' => 'caja',
                'factor_conversion' => 25.000,
                'precio_compra_referencia' => 1800.00,
                'precio_venta_sugerido' => 3000.00,
                'activo' => true,
            ],
            [
                'codigo' => 'UVA001',
                'nombre' => 'Uva Red Globe',
                'calidad' => '1A',
                'tamaño' => 'Medium',
                'grupo' => 'Uvas',
                'configuracion_avanzada' => false,
                'unidad_base' => 'caja',
                'factor_conversion' => 8.000,
                'precio_compra_referencia' => 8500.00,
                'precio_venta_sugerido' => 12000.00,
                'activo' => true,
            ],
            [
                'codigo' => 'MANG001',
                'nombre' => 'Mango Tommy Atkins',
                'calidad' => '1A',
                'tamaño' => 'Large',
                'grupo' => 'Tropicales',
                'configuracion_avanzada' => true,
                'unidad_base' => 'und',
                'factor_conversion' => 1.000,
                'precio_compra_referencia' => 1200.00,
                'precio_venta_sugerido' => 2000.00,
                'activo' => true,
            ],
            [
                'codigo' => 'FRES001',
                'nombre' => 'Fresa Festival',
                'calidad' => '1A',
                'tamaño' => 'Medium',
                'grupo' => 'Berries',
                'configuracion_avanzada' => false,
                'unidad_base' => 'caja',
                'factor_conversion' => 2.000,
                'precio_compra_referencia' => 12000.00,
                'precio_venta_sugerido' => 18000.00,
                'activo' => true,
            ],
            [
                'codigo' => 'PERA001',
                'nombre' => 'Pera Williams',
                'calidad' => '2A',
                'tamaño' => 'Medium',
                'grupo' => 'Peras',
                'configuracion_avanzada' => false,
                'unidad_base' => 'caja',
                'factor_conversion' => 18.000,
                'precio_compra_referencia' => 4200.00,
                'precio_venta_sugerido' => 6500.00,
                'activo' => true,
            ],
            [
                'codigo' => 'LIM001',
                'nombre' => 'Limón Tahití',
                'calidad' => '2A',
                'tamaño' => 'Small',
                'grupo' => 'Cítricos',
                'configuracion_avanzada' => false,
                'unidad_base' => 'caja',
                'factor_conversion' => 15.000,
                'precio_compra_referencia' => 2500.00,
                'precio_venta_sugerido' => 4000.00,
                'activo' => false,
            ],
            [
                'codigo' => 'MAN001',
                'nombre' => 'Mandarina Común',
                'calidad' => '1A',
                'tamaño' => 'Medium',
                'grupo' => 'Cítricos',
                'configuracion_avanzada' => true,
                'unidad_base' => 'bulto',
                'factor_conversion' => 200.000, // 1 bulto = 200 unidades
                'precio_compra_referencia' => 15000.00,
                'precio_venta_sugerido' => 22000.00,
                'activo' => true,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}
