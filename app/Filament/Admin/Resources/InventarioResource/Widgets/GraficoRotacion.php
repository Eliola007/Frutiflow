<?php

namespace App\Filament\Admin\Resources\InventarioResource\Widgets;

use App\Models\Inventario;
use Filament\Widgets\ChartWidget;

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
