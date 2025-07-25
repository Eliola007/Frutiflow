<?php

namespace Database\Seeders;

use App\Models\ConceptoGasto;
use Illuminate\Database\Seeder;

class ConceptoGastoSeeder extends Seeder
{
    public function run(): void
    {
        $conceptos = [
            // Grupo: tomate
            ['nombre' => 'PROPINA TOMATE', 'categoria' => 'personal', 'grupo' => 'tomate', 'es_recurrente' => false],
            ['nombre' => 'SANITARIA TOMATE', 'categoria' => 'sanitario', 'grupo' => 'tomate', 'es_recurrente' => false],
            ['nombre' => 'FITOSANITARIA TOMATE', 'categoria' => 'sanitario', 'grupo' => 'tomate', 'es_recurrente' => false],
            ['nombre' => 'FLETE TOMATE', 'categoria' => 'logistica', 'grupo' => 'tomate', 'es_recurrente' => false],
            ['nombre' => 'DESCARGA TOMATE', 'categoria' => 'logistica', 'grupo' => 'tomate', 'es_recurrente' => false],
            ['nombre' => 'EMPAQUE TOMATE', 'categoria' => 'operativo', 'grupo' => 'tomate', 'es_recurrente' => false],
            ['nombre' => 'OTROS TOMATE', 'categoria' => 'otros', 'grupo' => 'tomate', 'es_recurrente' => false],

            // Grupo: flete
            ['nombre' => 'FLETE LOCAL', 'categoria' => 'logistica', 'grupo' => 'flete', 'es_recurrente' => false],
            ['nombre' => 'FLETE FORÁNEO', 'categoria' => 'logistica', 'grupo' => 'flete', 'es_recurrente' => false],
            ['nombre' => 'PEAJE', 'categoria' => 'logistica', 'grupo' => 'flete', 'es_recurrente' => false],
            ['nombre' => 'GASOLINA', 'categoria' => 'logistica', 'grupo' => 'flete', 'es_recurrente' => true],
            ['nombre' => 'MANTENIMIENTO VEHÍCULO', 'categoria' => 'mantenimiento', 'grupo' => 'flete', 'es_recurrente' => true],

            // Grupo: servicios
            ['nombre' => 'AGUA', 'categoria' => 'servicios', 'grupo' => 'servicios', 'es_recurrente' => true],
            ['nombre' => 'LUZ', 'categoria' => 'servicios', 'grupo' => 'servicios', 'es_recurrente' => true],
            ['nombre' => 'GAS', 'categoria' => 'servicios', 'grupo' => 'servicios', 'es_recurrente' => true],
            ['nombre' => 'INTERNET', 'categoria' => 'servicios', 'grupo' => 'servicios', 'es_recurrente' => true],
            ['nombre' => 'TELÉFONO', 'categoria' => 'servicios', 'grupo' => 'servicios', 'es_recurrente' => true],

            // Grupo: administrativo
            ['nombre' => 'ARRIENDO', 'categoria' => 'administrativo', 'grupo' => 'administrativo', 'es_recurrente' => true],
            ['nombre' => 'SALARIOS', 'categoria' => 'personal', 'grupo' => 'administrativo', 'es_recurrente' => true],
            ['nombre' => 'PRESTACIONES SOCIALES', 'categoria' => 'personal', 'grupo' => 'administrativo', 'es_recurrente' => true],
            ['nombre' => 'IMPUESTOS', 'categoria' => 'administrativo', 'grupo' => 'administrativo', 'es_recurrente' => true],
            ['nombre' => 'SEGUROS', 'categoria' => 'administrativo', 'grupo' => 'administrativo', 'es_recurrente' => true],

            // Grupo: almacenamiento
            ['nombre' => 'BODEGA', 'categoria' => 'operativo', 'grupo' => 'almacenamiento', 'es_recurrente' => true],
            ['nombre' => 'REFRIGERACIÓN', 'categoria' => 'operativo', 'grupo' => 'almacenamiento', 'es_recurrente' => true],
            ['nombre' => 'FUMIGACIÓN', 'categoria' => 'sanitario', 'grupo' => 'almacenamiento', 'es_recurrente' => false],

            // Grupo: empaque
            ['nombre' => 'CAJAS', 'categoria' => 'operativo', 'grupo' => 'empaque', 'es_recurrente' => false],
            ['nombre' => 'BOLSAS', 'categoria' => 'operativo', 'grupo' => 'empaque', 'es_recurrente' => false],
            ['nombre' => 'ETIQUETAS', 'categoria' => 'operativo', 'grupo' => 'empaque', 'es_recurrente' => false],
            ['nombre' => 'ZUNCHOS', 'categoria' => 'operativo', 'grupo' => 'empaque', 'es_recurrente' => false],

            // Conceptos sin grupo
            ['nombre' => 'VIÁTICOS', 'categoria' => 'otros', 'grupo' => null, 'es_recurrente' => false],
            ['nombre' => 'HERRAMIENTAS', 'categoria' => 'mantenimiento', 'grupo' => null, 'es_recurrente' => false],
            ['nombre' => 'EQUIPOS', 'categoria' => 'operativo', 'grupo' => null, 'es_recurrente' => false],
            ['nombre' => 'PAPELERÍA', 'categoria' => 'administrativo', 'grupo' => null, 'es_recurrente' => false],
            ['nombre' => 'ASEO Y LIMPIEZA', 'categoria' => 'sanitario', 'grupo' => null, 'es_recurrente' => true],
            
            // Comisiones
            ['nombre' => 'COMISIÓN VENTAS', 'categoria' => 'comisiones', 'grupo' => null, 'es_recurrente' => false],
            ['nombre' => 'BONIFICACIONES', 'categoria' => 'personal', 'grupo' => null, 'es_recurrente' => false],
        ];

        foreach ($conceptos as $concepto) {
            ConceptoGasto::create($concepto);
        }
    }
}
