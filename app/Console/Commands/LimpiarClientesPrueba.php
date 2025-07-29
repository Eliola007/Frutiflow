<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cliente;

class LimpiarClientesPrueba extends Command
{
    protected $signature = 'clientes:limpiar';
    protected $description = 'Elimina todos los clientes de prueba excepto MOSTRADOR';

    public function handle()
    {
        $this->info('Eliminando clientes de prueba...');
        
        // Eliminar todos los clientes excepto MOSTRADOR
        $clientesEliminados = Cliente::where('email', '!=', 'mostrador@frutiflow.local')->delete();
        
        $this->info("Se eliminaron {$clientesEliminados} clientes de prueba.");
        $this->info('El cliente MOSTRADOR se preservó correctamente.');
        
        return 0;
    }
}
