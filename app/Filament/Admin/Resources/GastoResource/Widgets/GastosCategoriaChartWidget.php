<?php

namespace App\Filament\Admin\Resources\GastoResource\Widgets;

use App\Models\Gasto;
use Filament\Widgets\DoughnutChartWidget;

class GastosCategoriaChartWidget extends DoughnutChartWidget
{
    protected static ?string $heading = 'Gastos por Categoría';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $gastosPorCategoria = Gasto::selectRaw('categoria, SUM(monto) as total')
            ->whereMonth('fecha_gasto', now()->month)
            ->whereYear('fecha_gasto', now()->year)
            ->groupBy('categoria')
            ->orderBy('total', 'desc')
            ->get();

        $categoriaLabels = [
            'operativo' => 'Operativo',
            'logistica' => 'Logística',
            'personal' => 'Personal',
            'servicios' => 'Servicios',
            'mantenimiento' => 'Mantenimiento',
            'sanitario' => 'Sanitario',
            'administrativo' => 'Administrativo',
            'comisiones' => 'Comisiones',
            'otros' => 'Otros'
        ];

        // Si no hay datos, retornar estructura vacía
        if ($gastosPorCategoria->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => 'Sin datos del mes',
                        'data' => [0],
                        'backgroundColor' => ['rgb(156, 163, 175)'],
                        'borderWidth' => 0,
                    ],
                ],
                'labels' => ['Sin gastos registrados'],
            ];
        }

        $labels = [];
        $data = [];
        $backgroundColors = [
            'rgb(59, 130, 246)',   // Blue
            'rgb(16, 185, 129)',   // Green
            'rgb(245, 158, 11)',   // Yellow
            'rgb(239, 68, 68)',    // Red
            'rgb(139, 92, 246)',   // Purple
            'rgb(236, 72, 153)',   // Pink
            'rgb(34, 197, 94)',    // Emerald
            'rgb(251, 146, 60)',   // Orange
            'rgb(107, 114, 128)',  // Gray
        ];

        foreach ($gastosPorCategoria as $index => $gasto) {
            $labels[] = $categoriaLabels[$gasto->categoria] ?? $gasto->categoria;
            $data[] = floatval($gasto->total);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Gastos del Mes',
                    'data' => $data,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($data)),
                    'borderWidth' => 0,
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
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return context.label + ": $" + context.parsed.toLocaleString();
                        }'
                    ]
                ]
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
