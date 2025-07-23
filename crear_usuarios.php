<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\User;
use App\Models\Role;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Mostrar usuarios existentes
echo "=== USUARIOS EXISTENTES ===\n";
$usuarios = User::all();
foreach ($usuarios as $user) {
    echo "ID: {$user->id} | Email: {$user->email} | Nombre: {$user->name} | Activo: " . ($user->activo ? 'Sí' : 'No') . "\n";
}

// Crear usuarios adicionales si no existen
$usuariosACrear = [
    [
        'name' => 'Administrador Principal',
        'email' => 'admin@frutiflow.com',
        'password' => bcrypt('admin123'),
        'role_id' => 1,
        'activo' => true,
    ],
    [
        'name' => 'Usuario Demo',
        'email' => 'demo@frutiflow.com',
        'password' => bcrypt('demo123'),
        'role_id' => 1,
        'activo' => true,
    ],
    [
        'name' => 'Vendedor',
        'email' => 'vendedor@frutiflow.com',
        'password' => bcrypt('vendedor123'),
        'role_id' => 2,
        'activo' => true,
    ]
];

echo "\n=== CREANDO USUARIOS FALTANTES ===\n";
foreach ($usuariosACrear as $userData) {
    try {
        $existeUsuario = User::where('email', $userData['email'])->first();
        if (!$existeUsuario) {
            $user = User::create($userData);
            echo "✅ Usuario creado: {$user->email} (ID: {$user->id})\n";
        } else {
            echo "⚠️  Usuario ya existe: {$userData['email']}\n";
        }
    } catch (Exception $e) {
        echo "❌ Error creando {$userData['email']}: " . $e->getMessage() . "\n";
    }
}

echo "\n=== CREDENCIALES DE ACCESO ===\n";
echo "Email: admin@frutiflow.com | Password: admin123\n";
echo "Email: demo@frutiflow.com | Password: demo123\n";
echo "Email: vendedor@frutiflow.com | Password: vendedor123\n";
echo "\n=== TOTAL USUARIOS: " . User::count() . " ===\n";
