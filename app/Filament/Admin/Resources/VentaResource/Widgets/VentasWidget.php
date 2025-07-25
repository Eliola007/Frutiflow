<?php

namespace App\Filament\Admin\Resources\VentaResource\Widgets;

use App\Models\Venta;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class VentasWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Calcular ventas del día, mes y año
        $ventasHoy = Venta::ventasDelPeriodo('dia');
        $ventasMes = Venta::ventasDelPeriodo('mes');
        $ventasAño = Venta::ventasDelPeriodo('año');

        // Calcular comparativas (mes anterior)
        $mesAnterior = Venta::where('estado', '!=', 'cancelada')
            ->whereMonth('fecha_venta', now()->subMonth()->month)
            ->whereYear('fecha_venta', now()->subMonth()->year)
            ->sum('total');

        $añoAnterior = Venta::where('estado', '!=', 'cancelada')
            ->whereYear('fecha_venta', now()->subYear()->year)
            ->sum('total');

        // Calcular tendencias
        $tendenciaMes = $mesAnterior > 0 ? (($ventasMes - $mesAnterior) / $mesAnterior) * 100 : 0;
        $tendenciaAño = $añoAnterior > 0 ? (($ventasAño - $añoAnterior) / $añoAnterior) * 100 : 0;

        // Ventas por tipo del mes
        $ventasContado = Venta::where('tipo_venta', 'contado')
            ->where('estado', '!=', 'cancelada')
            ->delMes()
            ->sum('total');

        $ventasCredito = Venta::where('tipo_venta', 'credito')
            ->where('estado', '!=', 'cancelada')
            ->delMes()
            ->sum('total');

        // Cantidad de ventas del día
        $cantidadVentasHoy = Venta::where('estado', '!=', 'cancelada')
            ->whereDate('fecha_venta', now()->toDateString())
            ->count();

        return [
            Stat::make('Ventas Hoy', '$' . Number::format($ventasHoy, 0))
                ->description($cantidadVentasHoy . ' ventas realizadas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Ventas del Mes', '$' . Number::format($ventasMes, 0))
                ->description($tendenciaMes >= 0 ? 
                    '+' . number_format($tendenciaMes, 1) . '% vs mes anterior' : 
                    number_format($tendenciaMes, 1) . '% vs mes anterior'
                )
                ->descriptionIcon($tendenciaMes >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tendenciaMes >= 0 ? 'success' : 'danger'),

            Stat::make('Ventas del Año', '$' . Number::format($ventasAño, 0))
                ->description($tendenciaAño >= 0 ? 
                    '+' . number_format($tendenciaAño, 1) . '% vs año anterior' : 
                    number_format($tendenciaAño, 1) . '% vs año anterior'
                )
                ->descriptionIcon($tendenciaAño >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tendenciaAño >= 0 ? 'info' : 'warning'),

            Stat::make('Contado vs Crédito', '$' . Number::format($ventasContado, 0))
                ->description('Crédito: $' . Number::format($ventasCredito, 0))
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('primary'),
        ];
    }
}
