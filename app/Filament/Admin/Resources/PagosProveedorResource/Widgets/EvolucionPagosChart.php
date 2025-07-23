<?php

namespace App\Filament\Admin\Resources\PagosProveedorResource\Widgets;

use App\Models\PagoProveedor;
use App\Models\Proveedor;
use App\Helpers\CurrencyHelper;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class EvolucionPagosChart extends ChartWidget
{
    protected static ?string $heading = 'Evolución de Pagos a Proveedores - Metas Automáticas (Últimos 6 Meses)';

    protected static ?string $pollingInterval = '30s';
    
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $now = Carbon::now();
        $months = [];
        $pagosData = [];
        $metasData = [];

        // Calcular promedio de pagos de los últimos 3 meses para establecer meta base
        $promedioTrimestral = $this->calcularPromedioTrimestral();
        
        // Obtener saldos pendientes promedio para ajustar metas
        $saldosPendientesPromedio = $this->calcularSaldosPendientesPromedio();

        // Generar datos para los últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthName = $month->format('M Y');
            $months[] = $monthName;

            // Pagos reales del mes
            $pagosMes = PagoProveedor::whereYear('fecha_pago', $month->year)
                ->whereMonth('fecha_pago', $month->month)
                ->sum('monto');
            $pagosData[] = round($pagosMes, 2);

            // Meta inteligente basada en datos reales
            $metaMes = $this->calcularMetaInteligente($month, $promedioTrimestral, $saldosPendientesPromedio);
            $metasData[] = $metaMes;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pagos Realizados',
                    'data' => $pagosData,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#f59e0b',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                ],
                [
                    'label' => 'Meta Automática',
                    'data' => $metasData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'transparent',
                    'borderDash' => [5, 5],
                    'fill' => false,
                    'tension' => 0,
                    'pointRadius' => 0,
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
            'responsive' => true,
            'maintainAspectRatio' => false,
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
                        }",
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'callbacks' => [
                        'label' => "function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += new Intl.NumberFormat('es-MX', {
                                style: 'currency',
                                currency: 'MXN'
                            }).format(context.parsed.y);
                            return label;
                        }",
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
        ];
    }

    /**
     * Calcular promedio de pagos de los últimos 3 meses
     */
    private function calcularPromedioTrimestral(): float
    {
        $now = Carbon::now();
        $hace3Meses = $now->copy()->subMonths(3);
        
        $totalPagos = PagoProveedor::where('fecha_pago', '>=', $hace3Meses)
            ->sum('monto');
            
        return $totalPagos / 3; // Promedio mensual
    }

    /**
     * Calcular promedio de saldos pendientes de proveedores activos
     */
    private function calcularSaldosPendientesPromedio(): float
    {
        return Proveedor::where('activo', true)
            ->avg('saldo_pendiente') ?? 0;
    }

    /**
     * Calcular meta inteligente basada en datos reales
     */
    private function calcularMetaInteligente(Carbon $month, float $promedioTrimestral, float $saldosPendientesPromedio): float
    {
        // Meta base: promedio de los últimos 3 meses
        $metaBase = $promedioTrimestral;
        
        // Ajuste por saldos pendientes (si hay muchos saldos pendientes, aumentar meta)
        $factorSaldosPendientes = min(1.3, 1 + ($saldosPendientesPromedio / 100000) * 0.1);
        
        // Ajuste estacional (meses de alta actividad comercial)
        $factorEstacional = $this->getFactorEstacional($month->month);
        
        // Ajuste por tendencia (aumentar gradualmente las metas)
        $factorTendencia = 1.05; // 5% de mejora gradual
        
        // Calcular meta final
        $metaFinal = $metaBase * $factorSaldosPendientes * $factorEstacional * $factorTendencia;
        
        // Asegurar que la meta no sea menor a 30,000 ni mayor a 150,000
        return max(30000, min(150000, round($metaFinal, 0)));
    }

    /**
     * Obtener factor estacional por mes
     */
    private function getFactorEstacional(int $mes): float
    {
        // Factores basados en actividad comercial típica
        $factoresEstacionales = [
            1 => 0.9,  // Enero - baja después de fiestas
            2 => 0.95, // Febrero - baja
            3 => 1.0,  // Marzo - normal
            4 => 1.05, // Abril - incremento
            5 => 1.1,  // Mayo - alta temporada
            6 => 1.15, // Junio - alta temporada
            7 => 1.1,  // Julio - alta temporada
            8 => 1.0,  // Agosto - normal
            9 => 1.05, // Septiembre - incremento
            10 => 1.1, // Octubre - alta temporada
            11 => 1.15, // Noviembre - alta temporada pre-navideña
            12 => 1.2,  // Diciembre - pico navideño
        ];
        
        return $factoresEstacionales[$mes] ?? 1.0;
    }
}
