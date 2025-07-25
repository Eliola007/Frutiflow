<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Producto;
use App\Models\User;
use OwenIt\Auditing\Models\Audit;

class PermisosTestController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        return view('permisos.dashboard', [
            'user' => $user,
            'roles' => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'productosAsignados' => $user->hasRole('Socio Comercial') ? $user->productosAsignados : collect(),
        ]);
    }

    public function testProductos()
    {
        $user = Auth::user();
        
        // Esto disparará auditoría
        $productos = Producto::all();
        
        return view('permisos.productos', [
            'productos' => $productos,
            'user' => $user,
        ]);
    }

    public function testVentas()
    {
        return view('permisos.ventas');
    }

    public function auditoria()
    {
        $audits = Audit::with('user')
                      ->orderBy('created_at', 'desc')
                      ->take(50)
                      ->get();
                      
        return view('permisos.auditoria', [
            'audits' => $audits
        ]);
    }

    public function crearProducto(Request $request)
    {
        $producto = Producto::create([
            'codigo' => 'TEST-' . now()->format('YmdHis'),
            'nombre' => 'Producto de Prueba ' . now()->format('H:i:s'),
            'descripcion' => 'Producto creado para probar auditoría',
            'precio_compra' => 10.00,
            'precio_venta' => 15.00,
            'stock_minimo' => 5,
            'stock_actual' => 100,
            'unidad_medida' => 'Unidad',
            'activo' => true,
        ]);

        return redirect()->back()->with('success', "Producto creado: {$producto->nombre}");
    }

    public function asignarProductos(User $user)
    {
        $productos = Producto::all();
        $productosAsignados = $user->productosAsignados->pluck('id')->toArray();
        
        return view('permisos.asignar-productos', [
            'user' => $user,
            'productos' => $productos,
            'productosAsignados' => $productosAsignados,
        ]);
    }

    public function guardarProductos(Request $request, User $user)
    {
        $productosSeleccionados = $request->input('productos', []);
        
        // Desasignar todos los productos actuales
        $user->productosAsignados()->detach();
        
        // Asignar los nuevos productos seleccionados
        foreach ($productosSeleccionados as $productoId) {
            $user->productosAsignados()->attach($productoId, [
                'asignado_en' => now(),
                'asignado_hasta' => now()->addYear(),
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        return redirect()->back()->with('success', 'Productos asignados correctamente');
    }
}
