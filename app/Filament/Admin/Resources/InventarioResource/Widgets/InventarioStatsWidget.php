<?php

namespace App\Filament\Admin\Resources\InventarioResource\Widgets;

use App\Models\Inventario;
use App\Models\Producto;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class InventarioStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalLotes = Inventario::where('estado', 'disponible')
            ->where('cantidad_actual', '>', 0)
            ->count();

        $valorTotal = Inventario::where('estado', 'disponible')
            ->where('cantidad_actual', '>', 0)
            ->get()
            ->sum(function ($item) {
                return $item->cantidad_actual * $item->precio_costo;
            });

        $proximosVencer = Inventario::proximosAVencer(7)
            ->count();

        $vencidos = Inventario::vencidos()
            ->count();

        $productos = Producto::whereHas('inventarios', function ($query) {
            $query->where('estado', 'disponible')
                ->where('cantidad_actual', '>', 0);
        })->count();

        $sinStock = Producto::where('stock_actual', '<=', 0)->count();

        return [
            Stat::make('Lotes Disponibles', $totalLotes)
                ->description('Registros con stock')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),

            Stat::make('Valor Total', '$' . number_format($valorTotal, 0))
                ->description('Valor del inventario')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),

            Stat::make('Próximos a Vencer', $proximosVencer)
                ->description('En los próximos 7 días')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($proximosVencer > 0 ? 'warning' : 'success'),

            Stat::make('Productos Vencidos', $vencidos)
                ->description('Requieren atención')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($vencidos > 0 ? 'danger' : 'success'),

            Stat::make('Productos en Stock', $productos)
                ->description('Con inventario disponible')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),

            Stat::make('Sin Stock', $sinStock)
                ->description('Productos agotados')
                ->descriptionIcon('heroicon-m-minus-circle')
                ->color($sinStock > 0 ? 'danger' : 'success'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
