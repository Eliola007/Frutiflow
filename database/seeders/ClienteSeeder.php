<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
         
           
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
