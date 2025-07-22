<?php

namespace App\Filament\Admin\Resources\ProductoResource\Widgets;

use App\Models\Producto;
use App\Helpers\CurrencyHelper;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductosOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Estadísticas de productos
        $totalProductos = Producto::count();
        $productosActivos = Producto::activos()->count();
        $productosInactivos = Producto::inactivos()->count();
        
        // Estadísticas de stock
        $stockTotal = Producto::activos()->sum('stock_actual');
        $stockDisponible = Producto::activos()->sum('stock_disponible');
        $sinStock = Producto::activos()->where('stock_actual', '<=', 0)->count();
        
        // Valor del inventario
        $valorInventario = Producto::activos()
            ->whereNotNull('costo_promedio')
            ->whereNotNull('stock_actual')
            ->get()
            ->sum(function ($producto) {
                return $producto->stock_actual * $producto->costo_promedio;
            });
        
        // Margen promedio
        $margenPromedio = Producto::activos()
            ->whereNotNull('precio_compra_referencia')
            ->whereNotNull('precio_venta_sugerido')
            ->where('precio_compra_referencia', '>', 0)
            ->get()
            ->avg(function ($producto) {
                return (($producto->precio_venta_sugerido - $producto->precio_compra_referencia) / $producto->precio_compra_referencia) * 100;
            });

        return [
            Stat::make('Total Productos', $totalProductos)
                ->description($productosActivos . ' activos, ' . $productosInactivos . ' inactivos')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),
                
            Stat::make('Stock Total', number_format($stockTotal, 2))
                ->description('Stock disponible: ' . number_format($stockDisponible, 2))
                ->descriptionIcon('heroicon-m-archive-box')
                ->color($stockTotal > 0 ? 'success' : 'danger'),
                
            Stat::make('Sin Stock', $sinStock)
                ->description('Productos sin inventario')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($sinStock > 0 ? 'warning' : 'success'),
                
            Stat::make('Valor Inventario', CurrencyHelper::format($valorInventario))
                ->description('Valor total del inventario')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
                
            Stat::make('Margen Promedio', number_format($margenPromedio ?? 0, 1) . '%')
                ->description('Margen de ganancia promedio')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($margenPromedio >= 30 ? 'success' : ($margenPromedio >= 15 ? 'warning' : 'danger')),
        ];
    }
}
