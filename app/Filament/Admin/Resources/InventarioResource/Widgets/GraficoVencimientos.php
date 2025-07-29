<?php

namespace App\Filament\Admin\Resources\InventarioResource\Widgets;

use App\Models\Inventario;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

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
