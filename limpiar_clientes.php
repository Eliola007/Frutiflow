<?php

use Illuminate\Support\Facades\DB;

// Limpiar tabla de clientes
DB::table('clientes')->delete();

// Recrear cliente MOSTRADOR
DB::table('clientes')->insert([
    'nombre' => 'CLIENTE MOSTRADOR',
    'email' => 'mostrador@frutiflow.local',
    'telefono' => '000-000-0000',
    'direccion' => 'Mostrador',
    'limite_credito' => 0.00,
    'dias_credito' => 0,
    'saldo_pendiente' => 0.00,
    'estado_credito' => 'activo',
    'activo' => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "Tabla de clientes limpiada exitosamente.\n";
echo "Cliente MOSTRADOR recreado.\n";
