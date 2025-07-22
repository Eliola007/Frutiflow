<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso completo al sistema',
                'permisos' => [
                    'usuarios.create', 'usuarios.read', 'usuarios.update', 'usuarios.delete',
                    'roles.create', 'roles.read', 'roles.update', 'roles.delete',
                    'productos.create', 'productos.read', 'productos.update', 'productos.delete',
                    'clientes.create', 'clientes.read', 'clientes.update', 'clientes.delete',
                    'proveedores.create', 'proveedores.read', 'proveedores.update', 'proveedores.delete',
                    'compras.create', 'compras.read', 'compras.update', 'compras.delete',
                    'ventas.create', 'ventas.read', 'ventas.update', 'ventas.delete',
                    'gastos.create', 'gastos.read', 'gastos.update', 'gastos.delete',
                    'inventario.read', 'reportes.read'
                ],
                'activo' => true
            ],
            [
                'nombre' => 'Vendedor',
                'descripcion' => 'Gestión de ventas y clientes',
                'permisos' => [
                    'productos.read',
                    'clientes.create', 'clientes.read', 'clientes.update',
                    'ventas.create', 'ventas.read', 'ventas.update',
                    'inventario.read'
                ],
                'activo' => true
            ],
            [
                'nombre' => 'Comprador',
                'descripcion' => 'Gestión de compras y proveedores',
                'permisos' => [
                    'productos.read',
                    'proveedores.create', 'proveedores.read', 'proveedores.update',
                    'compras.create', 'compras.read', 'compras.update',
                    'inventario.read'
                ],
                'activo' => true
            ],
            [
                'nombre' => 'Almacenista',
                'descripcion' => 'Gestión de inventario',
                'permisos' => [
                    'productos.read',
                    'compras.read', 'compras.update',
                    'inventario.read', 'inventario.update'
                ],
                'activo' => true
            ]
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }
    }
}
