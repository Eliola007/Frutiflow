<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Definir permisos por módulo
        $permisos = [
            // Módulo Ventas
            'ventas.ver' => 'Ver ventas',
            'ventas.crear' => 'Crear ventas',
            'ventas.editar' => 'Editar ventas',
            'ventas.eliminar' => 'Eliminar ventas',
            'ventas.cancelar' => 'Cancelar ventas',

            // Módulo Compras
            'compras.ver' => 'Ver compras',
            'compras.crear' => 'Crear compras',
            'compras.editar' => 'Editar compras',
            'compras.eliminar' => 'Eliminar compras',
            'compras.cancelar' => 'Cancelar compras',

            // Módulo Inventario
            'inventario.ver' => 'Ver inventario',
            'inventario.ajustar' => 'Ajustar inventario',
            'inventario.transferir' => 'Transferir inventario',
            'inventario.contar' => 'Contar inventario',

            // Módulo Productos
            'productos.ver' => 'Ver productos',
            'productos.crear' => 'Crear productos',
            'productos.editar' => 'Editar productos',
            'productos.eliminar' => 'Eliminar productos',
            'productos.configurar' => 'Configurar productos',

            // Módulo Clientes
            'clientes.ver' => 'Ver clientes',
            'clientes.crear' => 'Crear clientes',
            'clientes.editar' => 'Editar clientes',
            'clientes.eliminar' => 'Eliminar clientes',
            'clientes.credito' => 'Gestionar crédito de clientes',

            // Módulo Proveedores
            'proveedores.ver' => 'Ver proveedores',
            'proveedores.crear' => 'Crear proveedores',
            'proveedores.editar' => 'Editar proveedores',
            'proveedores.eliminar' => 'Eliminar proveedores',
            'proveedores.credito' => 'Gestionar crédito de proveedores',

            // Módulo Reportes
            'reportes.ventas' => 'Ver reportes de ventas',
            'reportes.compras' => 'Ver reportes de compras',
            'reportes.inventario' => 'Ver reportes de inventario',
            'reportes.financiero' => 'Ver reportes financieros',
            'reportes.productos' => 'Ver reportes de productos',
            'reportes.clientes' => 'Ver reportes de clientes',
            'reportes.proveedores' => 'Ver reportes de proveedores',

            // Módulo Configuración
            'configuracion.ver' => 'Ver configuración',
            'configuracion.editar' => 'Editar configuración',
            'configuracion.sistema' => 'Configurar sistema',

            // Módulo Usuarios
            'usuarios.ver' => 'Ver usuarios',
            'usuarios.crear' => 'Crear usuarios',
            'usuarios.editar' => 'Editar usuarios',
            'usuarios.eliminar' => 'Eliminar usuarios',
            'usuarios.roles' => 'Gestionar roles de usuarios',

            // Módulo Gastos
            'gastos.ver' => 'Ver gastos',
            'gastos.crear' => 'Crear gastos',
            'gastos.editar' => 'Editar gastos',
            'gastos.eliminar' => 'Eliminar gastos',

            // Módulo Pagos
            'pagos.ver' => 'Ver pagos',
            'pagos.crear' => 'Crear pagos',
            'pagos.editar' => 'Editar pagos',
            'pagos.eliminar' => 'Eliminar pagos',
        ];

        // Crear permisos
        foreach ($permisos as $permiso => $descripcion) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web'
            ]);
        }

        // Crear roles y asignar permisos
        $this->crearRoles();
    }

    private function crearRoles()
    {
        // 1. Administrador - Acceso completo
        $administrador = \App\Models\Role::where('nombre', 'Administrador')->first();
        if ($administrador) {
            // Crear el rol de Spatie usando el mismo nombre
            $adminRole = Role::firstOrCreate([
                'name' => 'Administrador',
                'guard_name' => 'web'
            ]);
            
            // Asignar todos los permisos
            $adminRole->givePermissionTo(Permission::all());
        }

        // 2. Cajero - Ventas, compras básicas, inventario limitado
        $cajero = \App\Models\Role::where('nombre', 'Cajero')->first();
        if ($cajero) {
            $cajeroRole = Role::firstOrCreate([
                'name' => 'Cajero',
                'guard_name' => 'web'
            ]);
            
            $permisosCajero = [
                // Ventas completas
                'ventas.ver', 'ventas.crear', 'ventas.editar', 'ventas.cancelar',
                
                // Compras básicas
                'compras.ver', 'compras.crear',
                
                // Inventario limitado
                'inventario.ver', 'inventario.contar',
                
                // Productos solo ver
                'productos.ver',
                
                // Clientes básico
                'clientes.ver', 'clientes.crear', 'clientes.editar',
                
                // Proveedores básico
                'proveedores.ver',
                
                // Reportes básicos
                'reportes.ventas', 'reportes.inventario',
                
                // Pagos
                'pagos.ver', 'pagos.crear',
                
                // Gastos básicos
                'gastos.ver', 'gastos.crear'
            ];
            
            $cajeroRole->givePermissionTo($permisosCajero);
        }

        // 3. Socio Comercial - Solo reportes específicos de productos asignados
        $socio = \App\Models\Role::where('nombre', 'Socio Comercial')->first();
        if ($socio) {
            $socioRole = Role::firstOrCreate([
                'name' => 'Socio Comercial',
                'guard_name' => 'web'
            ]);
            
            $permisosSocio = [
                // Solo reportes de productos
                'reportes.productos',
                'reportes.ventas', // limitado a sus productos
                'reportes.inventario', // limitado a sus productos
                
                // Ver productos (limitado a los asignados)
                'productos.ver',
            ];
            
            $socioRole->givePermissionTo($permisosSocio);
        }
    }
}
