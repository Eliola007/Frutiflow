<?php

namespace App\Filament\Admin\Resources\ProductoResource\Widgets;

use App\Models\Producto;
use App\Helpers\CurrencyHelper;
use Filament\Widgets\ChartWidget;

class InventarioValorChart extends ChartWidget
{
    protected static ?string $heading = 'Valor de Inventario por Grupo';
    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        // Obtener valor del inventario por grupo
        $grupos = Producto::activos()
            ->whereNotNull('costo_promedio')
            ->whereNotNull('stock_actual')
            ->where('stock_actual', '>', 0)
            ->where('costo_promedio', '>', 0)
            ->selectRaw('grupo, SUM(stock_actual * costo_promedio) as valor_total')
            ->groupBy('grupo')
            ->orderBy('valor_total', 'desc')
            ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [
            'rgba(34, 197, 94, 0.8)',    // Verde
            'rgba(59, 130, 246, 0.8)',   // Azul
            'rgba(249, 115, 22, 0.8)',   // Naranja
            'rgba(168, 85, 247, 0.8)',   // Púrpura
            'rgba(236, 72, 153, 0.8)',   // Rosa
            'rgba(14, 165, 233, 0.8)',   // Cyan
            'rgba(250, 204, 21, 0.8)',   // Amarillo
            'rgba(239, 68, 68, 0.8)',    // Rojo
            'rgba(156, 163, 175, 0.8)',  // Gris
        ];
        
        foreach ($grupos as $index => $grupo) {
            $labels[] = $grupo->grupo . ' (' . CurrencyHelper::format($grupo->valor_total) . ')';
            $data[] = round($grupo->valor_total, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Valor en MXN',
                    'data' => $data,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($data)),
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Valor Total de Inventario por Grupo de Productos',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return context.label + ": $" + context.parsed.toLocaleString("es-MX", {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }'
                    ]
                ]
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
            'cutout' => '50%', // Para hacer el efecto doughnut
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
