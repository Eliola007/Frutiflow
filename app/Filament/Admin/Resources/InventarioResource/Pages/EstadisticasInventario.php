<?php

namespace App\Filament\Admin\Resources\InventarioResource\Pages;

use App\Filament\Admin\Resources\InventarioResource;
use App\Filament\Admin\Resources\InventarioResource\Widgets\GraficoVencimientos;
use App\Filament\Admin\Resources\InventarioResource\Widgets\GraficoRotacion;
use App\Models\Inventario;
use App\Models\Producto;
use App\Helpers\CurrencyHelper;
use Filament\Resources\Pages\Page;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EstadisticasInventario extends Page
{
    protected static string $resource = InventarioResource::class;

    protected static string $view = 'filament.admin.resources.inventario-resource.pages.estadisticas-inventario';

    protected static ?string $title = 'Estadísticas de Inventario';

    protected static ?string $navigationLabel = 'Estadísticas';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected function getHeaderWidgets(): array
    {
        return [
            InventarioEstadisticasWidget::class,
            GraficoVencimientos::class,
            GraficoRotacion::class,
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Análisis completo del estado del inventario y tendencias PEPS';
    }
}

class InventarioEstadisticasWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalLotes = Inventario::count();
        $lotesActivos = Inventario::where('cantidad_actual', '>', 0)
            ->where('estado', '!=', 'vencido')
            ->count();
        
        $valorTotal = Inventario::where('cantidad_actual', '>', 0)
            ->where('estado', '!=', 'vencido')
            ->join('productos', 'inventarios.producto_id', '=', 'productos.id')
            ->sum(DB::raw('inventarios.cantidad_actual * productos.precio_compra_referencia'));
        
        $productosEnStock = Producto::whereHas('inventarios', function ($query) {
            $query->where('cantidad_actual', '>', 0)
                  ->where('estado', '!=', 'vencido');
        })->count();
        
        $productosVencidos = Inventario::where('estado', 'vencido')
            ->distinct('producto_id')
            ->count();
        
        $proximosVencer = Inventario::where('cantidad_actual', '>', 0)
            ->where('estado', 'disponible')
            ->where('fecha_vencimiento', '<=', Carbon::now()->addDays(7))
            ->count();

        return [
            Stat::make('Total de Lotes', $totalLotes)
                ->description('Registros en inventario')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('primary'),
                
            Stat::make('Lotes Activos', $lotesActivos)
                ->description('Con stock disponible')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Valor Total', CurrencyHelper::format($valorTotal ?? 0))
                ->description('Inventario valorizado')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),
                
            Stat::make('Productos en Stock', $productosEnStock)
                ->description('Productos disponibles')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),
                
            Stat::make('Productos Vencidos', $productosVencidos)
                ->description('Requieren atención')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
                
            Stat::make('Próximos a Vencer', $proximosVencer)
                ->description('En 7 días o menos')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
