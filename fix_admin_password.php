<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Buscar el usuario admin
$admin = User::where('email', 'admin@frutiflow.com')->first();

if ($admin) {
    echo "=== USUARIO ADMINISTRADOR ENCONTRADO ===\n";
    echo "ID: {$admin->id}\n";
    echo "Nombre: {$admin->name}\n";
    echo "Email: {$admin->email}\n";
    echo "Activo: " . ($admin->activo ? 'Sí' : 'No') . "\n";
    echo "Role ID: {$admin->role_id}\n";
    
    // Actualizar la contraseña a admin123
    $admin->password = Hash::make('admin123');
    $admin->save();
    
    echo "\n✅ CONTRASEÑA ACTUALIZADA A: admin123\n";
    
    // Verificar que la contraseña funciona
    if (Hash::check('admin123', $admin->password)) {
        echo "✅ Verificación exitosa: La contraseña 'admin123' funciona\n";
    } else {
        echo "❌ Error: La contraseña no se actualizó correctamente\n";
    }
    
} else {
    echo "❌ Usuario admin@frutiflow.com no encontrado\n";
}

echo "\n=== CREDENCIALES DE ACCESO ===\n";
echo "URL: http://127.0.0.1:8000/admin\n";
echo "Email: admin@frutiflow.com\n";
echo "Password: admin123\n";
