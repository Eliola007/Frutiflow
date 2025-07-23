<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Crear usuarios adicionales con contraseñas simples
$usuariosSimples = [
    [
        'name' => 'Super Admin',
        'email' => 'super@admin.com',
        'password' => Hash::make('123456'),
        'role_id' => 1,
        'activo' => true,
    ],
    [
        'name' => 'Test User',
        'email' => 'test@test.com',
        'password' => Hash::make('123456'),
        'role_id' => 1,
        'activo' => true,
    ],
];

echo "=== CREANDO USUARIOS ALTERNATIVOS ===\n";
foreach ($usuariosSimples as $userData) {
    if (!User::where('email', $userData['email'])->exists()) {
        $user = User::create($userData);
        echo "✅ Usuario creado: {$user->email} | Password: 123456\n";
    } else {
        echo "⚠️  Usuario ya existe: {$userData['email']}\n";
    }
}

echo "\n=== TODAS LAS CREDENCIALES DISPONIBLES ===\n";
echo "1. Email: admin@frutiflow.com | Password: admin123\n";
echo "2. Email: super@admin.com | Password: 123456\n";
echo "3. Email: test@test.com | Password: 123456\n";
echo "4. Email: demo@frutiflow.com | Password: demo123\n";
echo "\nURL de acceso: http://127.0.0.1:8000/admin\n";
