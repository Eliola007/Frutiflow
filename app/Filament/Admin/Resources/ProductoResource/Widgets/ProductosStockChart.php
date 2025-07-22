<?php

namespace App\Filament\Admin\Resources\ProductoResource\Widgets;

use App\Models\Producto;
use Filament\Widgets\ChartWidget;

class ProductosStockChart extends ChartWidget
{
    protected static ?string $heading = 'Stock por Producto';
    protected static ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Obtener productos activos con stock
        $productos = Producto::activos()
            ->where('stock_actual', '>', 0)
            ->orderBy('stock_actual', 'desc')
            ->limit(10) // Top 10 productos con más stock
            ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [];
        
        foreach ($productos as $producto) {
            // Usar código o nombre corto para las etiquetas
            $labels[] = $producto->codigo ?: substr($producto->nombre, 0, 15) . '...';
            $data[] = $producto->stock_actual;
            
            // Colores basados en nivel de stock
            if ($producto->stock_actual > 50) {
                $backgroundColors[] = 'rgba(34, 197, 94, 0.8)'; // Verde
            } elseif ($producto->stock_actual > 20) {
                $backgroundColors[] = 'rgba(249, 115, 22, 0.8)'; // Naranja
            } else {
                $backgroundColors[] = 'rgba(239, 68, 68, 0.8)'; // Rojo
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Stock Actual',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $backgroundColors,
                    'borderWidth' => 1,
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
                    'display' => false,
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Top 10 Productos con Mayor Stock',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Cantidad en Stock',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Productos',
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
