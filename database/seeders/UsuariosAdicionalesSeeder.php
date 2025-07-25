<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Producto;
use Illuminate\Support\Facades\Hash;

class UsuariosAdicionalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles
        $cajeroRole = Role::where('nombre', 'Cajero')->first();
        $socioRole = Role::where('nombre', 'Socio Comercial')->first();

        // Crear usuario Cajero
        $cajero = User::firstOrCreate([
            'email' => 'cajero@frutiflow.com'
        ], [
            'name' => 'María Rodríguez',
            'password' => Hash::make('password123'),
            'role_id' => $cajeroRole->id,
            'activo' => true,
        ]);

        // Crear usuario Socio Comercial
        $socio = User::firstOrCreate([
            'email' => 'socio@frutiflow.com'
        ], [
            'name' => 'Carlos Méndez',
            'password' => Hash::make('password123'),
            'role_id' => $socioRole->id,
            'activo' => true,
        ]);

        // Asignar productos específicos al Socio Comercial
        // Vamos a asignar algunos productos como ejemplo
        $productos = Producto::take(3)->get(); // Tomar los primeros 3 productos
        
        if ($productos->count() > 0) {
            foreach ($productos as $producto) {
                $socio->productosAsignados()->syncWithoutDetaching([
                    $producto->id => [
                        'asignado_en' => now(),
                        'asignado_hasta' => now()->addYear(), // Válido por 1 año
                        'activo' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]);
            }

            $this->command->info("✅ Asignados {$productos->count()} productos al Socio Comercial: {$socio->name}");
            $this->command->info("   Productos: " . $productos->pluck('nombre')->join(', '));
        }

        $this->command->info("✅ Usuarios adicionales creados:");
        $this->command->info("   - {$cajero->name} (Cajero) - {$cajero->email}");
        $this->command->info("   - {$socio->name} (Socio Comercial) - {$socio->email}");
    }
}
