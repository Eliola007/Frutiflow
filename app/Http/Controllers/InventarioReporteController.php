<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class InventarioReporteController extends Controller
{
    /**
     * Reporte PEPS por producto
     */
    public function reportePeps(Producto $producto): View
    {
        // Obtener inventarios PEPS del producto
        $inventarios = Inventario::peps($producto->id)->get();
        
        // Calcular totales
        $totalCantidad = $inventarios->sum('cantidad_actual');
        $valorTotal = $inventarios->sum('valor_total');
        $costoPromedio = $totalCantidad > 0 ? $valorTotal / $totalCantidad : 0;
        
        return view('reportes.inventario-peps', compact(
            'producto', 
            'inventarios', 
            'totalCantidad', 
            'valorTotal', 
            'costoPromedio'
        ));
    }

    /**
     * Reporte general de inventario
     */
    public function reporteGeneral(): View
    {
        $stats = [
            'total_productos' => Producto::count(),
            'productos_con_stock' => Producto::where('stock_actual', '>', 0)->count(),
            'productos_sin_stock' => Producto::where('stock_actual', '<=', 0)->count(),
            'total_lotes' => Inventario::where('estado', 'disponible')->count(),
            'valor_total' => Inventario::where('estado', 'disponible')
                ->get()
                ->sum('valor_total'),
            'proximos_vencer' => Inventario::proximosAVencer(7)->count(),
            'vencidos' => Inventario::vencidos()->count(),
        ];

        // Top productos por valor
        $topProductos = Inventario::select('producto_id')
            ->selectRaw('SUM(cantidad_actual * precio_costo) as valor_total')
            ->selectRaw('SUM(cantidad_actual) as cantidad_total')
            ->where('estado', 'disponible')
            ->where('cantidad_actual', '>', 0)
            ->with('producto')
            ->groupBy('producto_id')
            ->orderByDesc('valor_total')
            ->limit(10)
            ->get();

        // Productos próximos a vencer
        $proximosVencer = Inventario::proximosAVencer(7)
            ->with('producto')
            ->orderBy('fecha_vencimiento')
            ->get();

        return view('reportes.inventario-general', compact(
            'stats',
            'topProductos', 
            'proximosVencer'
        ));
    }

    /**
     * Reporte de vencimientos
     */
    public function reporteVencimientos(): View
    {
        $vencidos = Inventario::vencidos()
            ->with('producto')
            ->orderBy('fecha_vencimiento', 'desc')
            ->get();

        $proximosVencer = Inventario::proximosAVencer(30)
            ->with('producto')
            ->orderBy('fecha_vencimiento')
            ->get();

        $valorVencidos = $vencidos->sum('valor_total');
        $valorProximos = $proximosVencer->sum('valor_total');

        return view('reportes.inventario-vencimientos', compact(
            'vencidos',
            'proximosVencer',
            'valorVencidos',
            'valorProximos'
        ));
    }

    /**
     * Reporte de movimientos de inventario
     */
    public function reporteMovimientos(Request $request): View
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth());
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth());

        // Entradas (compras)
        $entradas = Inventario::whereBetween('fecha_ingreso', [$fechaInicio, $fechaFin])
            ->with(['producto', 'compra'])
            ->orderBy('fecha_ingreso', 'desc')
            ->get();

        // Salidas (ventas) - aquí necesitarías obtener los datos de VentaItem
        // Por ahora solo mostramos las entradas

        $totalEntradas = $entradas->sum('cantidad_inicial');
        $valorEntradas = $entradas->sum(function ($item) {
            return $item->cantidad_inicial * $item->precio_costo;
        });

        return view('reportes.inventario-movimientos', compact(
            'entradas',
            'totalEntradas',
            'valorEntradas',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Exportar inventario a CSV
     */
    public function exportarCsv()
    {
        $inventarios = Inventario::with(['producto', 'compra'])
            ->where('estado', 'disponible')
            ->where('cantidad_actual', '>', 0)
            ->orderBy('producto_id')
            ->orderBy('fecha_ingreso')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventario-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($inventarios) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM para Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'Producto',
                'Lote',
                'Fecha Ingreso',
                'Fecha Vencimiento',
                'Cantidad Inicial',
                'Cantidad Actual',
                'Precio Costo',
                'Valor Total',
                'Estado',
                'Compra',
                'Observaciones'
            ]);

            // Datos
            foreach ($inventarios as $item) {
                fputcsv($file, [
                    $item->producto->nombre ?? '',
                    $item->lote,
                    $item->fecha_ingreso->format('d/m/Y'),
                    $item->fecha_vencimiento ? $item->fecha_vencimiento->format('d/m/Y') : '',
                    $item->cantidad_inicial,
                    $item->cantidad_actual,
                    $item->precio_costo,
                    $item->valor_total,
                    $item->estado,
                    $item->compra->numero_factura ?? 'Manual',
                    $item->observaciones ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
