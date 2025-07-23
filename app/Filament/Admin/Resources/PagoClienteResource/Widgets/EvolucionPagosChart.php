<?php

namespace App\Filament\Admin\Resources\PagoClienteResource\Widgets;

use App\Models\PagoCliente;
use App\Helpers\CurrencyHelper;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class EvolucionPagosChart extends ChartWidget
{
    protected static ?string $heading = 'Evolución de Pagos (Últimos 6 Meses)';

    protected static ?string $pollingInterval = '30s';
    
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $now = Carbon::now();
        $months = [];
        $pagosData = [];
        $metasData = [];

        // Generar datos para los últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthName = $month->format('M Y');
            $months[] = $monthName;

            // Pagos reales del mes
            $pagosMes = PagoCliente::whereYear('fecha_pago', $month->year)
                ->whereMonth('fecha_pago', $month->month)
                ->sum('monto');
            $pagosData[] = round($pagosMes, 2);

            // Meta estimada (puedes ajustar esta lógica según tu negocio)
            $meta = $month->month % 2 == 0 ? 50000 : 40000; // Meta variable por mes
            $metasData[] = $meta;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pagos Recibidos',
                    'data' => $pagosData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Meta de Cobros',
                    'data' => $metasData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                    'borderDash' => [5, 5],
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {
                            return context.dataset.label + ': ' + new Intl.NumberFormat('es-MX', {
                                style: 'currency',
                                currency: 'MXN'
                            }).format(context.parsed.y);
                        }"
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) {
                            return new Intl.NumberFormat('es-MX', {
                                style: 'currency',
                                currency: 'MXN',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(value);
                        }"
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
