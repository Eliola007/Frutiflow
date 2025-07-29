<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class LimpiarClientesSeeder extends Seeder
{
    public function run(): void
    {
        // Eliminar todos los clientes excepto MOSTRADOR
        Cliente::where('email', '!=', 'mostrador@frutiflow.local')->delete();
        
        echo "Clientes de prueba eliminados exitosamente.\n";
    }
}
