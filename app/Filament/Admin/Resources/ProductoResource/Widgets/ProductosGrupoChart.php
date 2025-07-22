<?php

namespace App\Filament\Admin\Resources\ProductoResource\Widgets;

use App\Models\Producto;
use Filament\Widgets\ChartWidget;

class ProductosGrupoChart extends ChartWidget
{
    protected static ?string $heading = 'Distribución por Grupos';
    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        // Obtener productos agrupados por grupo
        $grupos = Producto::activos()
            ->selectRaw('grupo, COUNT(*) as total')
            ->groupBy('grupo')
            ->orderBy('total', 'desc')
            ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [
            'rgba(255, 99, 132, 0.8)',   // Rojo
            'rgba(54, 162, 235, 0.8)',   // Azul
            'rgba(255, 205, 86, 0.8)',   // Amarillo
            'rgba(75, 192, 192, 0.8)',   // Verde agua
            'rgba(153, 102, 255, 0.8)',  // Púrpura
            'rgba(255, 159, 64, 0.8)',   // Naranja
            'rgba(199, 199, 199, 0.8)',  // Gris
            'rgba(83, 102, 255, 0.8)',   // Azul índigo
            'rgba(255, 99, 255, 0.8)',   // Magenta
        ];
        
        foreach ($grupos as $index => $grupo) {
            $labels[] = $grupo->grupo;
            $data[] = $grupo->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad de Productos',
                    'data' => $data,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($data)),
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
                    'display' => true,
                    'position' => 'bottom',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Productos Activos por Grupo',
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
