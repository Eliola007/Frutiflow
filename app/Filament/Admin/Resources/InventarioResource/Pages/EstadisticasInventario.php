<?php

namespace App\Filament\Admin\Resources\InventarioResource\Pages;

use App\Filament\Admin\Resources\InventarioResource;
use App\Models\Inventario;
use App\Models\Producto;
use Filament\Resources\Pages\Page;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\ChartWidget;
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
            EstadisticasGenerales::class,
            GraficoVencimientos::class,
            GraficoRotacion::class,
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Análisis completo del estado del inventario y tendencias PEPS';
    }
}

class EstadisticasGenerales extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalLotes = Inventario::count();
        $lotesActivos = Inventario::where('cantidad_actual', '>', 0)
            ->where('estado', '!=', 'vencido')
            ->count();
        
        $valorTotal = Inventario::where('cantidad_actual', '>', 0)
            ->where('estado', '!=', 'vencido')
            ->sum(DB::raw('cantidad_actual * precio_costo'));

        $proximosVencimiento = Inventario::where('cantidad_actual', '>', 0)
            ->where('fecha_vencimiento', '>', now())
            ->where('fecha_vencimiento', '<=', now()->addDays(30))
            ->count();

        $vencidos = Inventario::where('estado', 'vencido')
            ->orWhere('fecha_vencimiento', '<=', now())
            ->count();

        $productosConStock = Inventario::select('producto_id')
            ->where('cantidad_actual', '>', 0)
            ->where('estado', '!=', 'vencido')
            ->distinct()
            ->count();

        $totalProductos = Producto::count();
        $sinStock = $totalProductos - $productosConStock;

        return [
            \Filament\Widgets\StatsOverviewWidget\Stat::make('Total de Lotes', $totalLotes)
                ->description($lotesActivos . ' lotes activos')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            \Filament\Widgets\StatsOverviewWidget\Stat::make('Valor del Inventario', 'Q ' . number_format($valorTotal, 2))
                ->description('Valor total del stock disponible')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            \Filament\Widgets\StatsOverviewWidget\Stat::make('Próximos a Vencer', $proximosVencimiento)
                ->description('En los próximos 30 días')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            \Filament\Widgets\StatsOverviewWidget\Stat::make('Productos Vencidos', $vencidos)
                ->description('Requieren atención inmediata')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            \Filament\Widgets\StatsOverviewWidget\Stat::make('Productos con Stock', $productosConStock)
                ->description($sinStock . ' productos sin stock')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),

            \Filament\Widgets\StatsOverviewWidget\Stat::make('Rotación Promedio', $this->getRotacionPromedio() . ' días')
                ->description('Tiempo promedio de permanencia')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('gray'),
        ];
    }

    private function getRotacionPromedio(): int
    {
        $lotesVendidos = Inventario::where('cantidad_actual', 0)
            ->where('estado', 'agotado')
            ->whereNotNull('fecha_ingreso')
            ->get();

        if ($lotesVendidos->isEmpty()) {
            return 0;
        }

        $totalDias = 0;
        foreach ($lotesVendidos as $lote) {
            $diasRotacion = $lote->fecha_ingreso->diffInDays(now());
            $totalDias += $diasRotacion;
        }

        return intval($totalDias / $lotesVendidos->count());
    }
}

class GraficoVencimientos extends ChartWidget
{
    protected static ?string $heading = 'Productos por Vencer (Próximos 90 días)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $hoy = Carbon::now();
        $datos = [];
        $labels = [];

        // Generar datos para los próximos 90 días agrupados por semana
        for ($i = 0; $i < 13; $i++) {
            $inicioSemana = $hoy->copy()->addWeeks($i);
            $finSemana = $inicioSemana->copy()->addWeek();
            
            $count = Inventario::where('cantidad_actual', '>', 0)
                ->where('fecha_vencimiento', '>=', $inicioSemana)
                ->where('fecha_vencimiento', '<', $finSemana)
                ->count();

            $datos[] = $count;
            $labels[] = 'Sem ' . ($i + 1);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Lotes por vencer',
                    'data' => $datos,
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.1)',
                        'rgba(245, 158, 11, 0.1)',
                        'rgba(59, 130, 246, 0.1)',
                    ],
                    'borderColor' => [
                        'rgb(239, 68, 68)',
                        'rgb(245, 158, 11)',
                        'rgb(59, 130, 246)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

class GraficoRotacion extends ChartWidget
{
    protected static ?string $heading = 'Análisis de Rotación por Producto (Top 10)';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $productosRotacion = Inventario::select('producto_id')
            ->selectRaw('AVG(DATEDIFF(COALESCE(updated_at, NOW()), fecha_ingreso)) as promedio_dias')
            ->selectRaw('COUNT(*) as total_lotes')
            ->with('producto')
            ->where('cantidad_actual', 0)
            ->where('estado', 'agotado')
            ->groupBy('producto_id')
            ->having('total_lotes', '>=', 2)
            ->orderBy('promedio_dias', 'asc')
            ->limit(10)
            ->get();

        $labels = [];
        $datos = [];

        foreach ($productosRotacion as $item) {
            $labels[] = $item->producto->nombre ?? 'Producto ' . $item->producto_id;
            $datos[] = round($item->promedio_dias, 1);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Días promedio de rotación',
                    'data' => $datos,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Días'
                    ]
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
        ];
    }
}
