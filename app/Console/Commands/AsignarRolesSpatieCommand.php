<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role as CustomRole;
use Spatie\Permission\Models\Role as SpatieRole;

class AsignarRolesSpatieCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:asignar-spatie';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asignar roles de Spatie a usuarios existentes basado en sus roles actuales';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Asignando roles de Spatie a usuarios existentes...');

        $usuarios = User::with('role')->get();

        foreach ($usuarios as $usuario) {
            if ($usuario->role) {
                // Mapear el rol personalizado al rol de Spatie
                $spatieRole = SpatieRole::where('name', $usuario->role->nombre)->first();
                
                if ($spatieRole) {
                    // Remover roles anteriores de Spatie
                    $usuario->syncRoles([]);
                    
                    // Asignar el nuevo rol de Spatie
                    $usuario->assignRole($spatieRole);
                    
                    $this->info("Usuario {$usuario->name} - Rol asignado: {$spatieRole->name}");
                } else {
                    $this->warn("No se encontró rol de Spatie para: {$usuario->role->nombre} (Usuario: {$usuario->name})");
                }
            } else {
                $this->warn("Usuario {$usuario->name} no tiene rol asignado");
            }
        }

        $this->info('✅ Asignación de roles completada!');
        
        // Mostrar resumen
        $this->table(
            ['Usuario', 'Rol Personalizado', 'Rol Spatie'],
            User::with('role')->get()->map(function($user) {
                return [
                    $user->name,
                    $user->role ? $user->role->nombre : 'Sin rol',
                    $user->roles->pluck('name')->join(', ') ?: 'Sin rol Spatie'
                ];
            })
        );
    }
}
