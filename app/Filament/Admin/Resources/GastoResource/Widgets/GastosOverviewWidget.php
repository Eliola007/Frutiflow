<?php

namespace App\Filament\Admin\Resources\GastoResource\Widgets;

use App\Models\Gasto;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class GastosOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Calcular gastos del día, mes y año
        $gastosHoy = Gasto::gastosDelPeriodo('dia');
        $gastosMes = Gasto::gastosDelPeriodo('mes');
        $gastosAño = Gasto::gastosDelPeriodo('año');

        // Calcular comparativas (mes anterior)
        $mesAnterior = Gasto::whereMonth('fecha_gasto', now()->subMonth()->month)
            ->whereYear('fecha_gasto', now()->subMonth()->year)
            ->sum('monto');

        $añoAnterior = Gasto::whereYear('fecha_gasto', now()->subYear()->year)
            ->sum('monto');

        // Calcular tendencias
        $tendenciaMes = $mesAnterior > 0 ? (($gastosMes - $mesAnterior) / $mesAnterior) * 100 : 0;
        $tendenciaAño = $añoAnterior > 0 ? (($gastosAño - $añoAnterior) / $añoAnterior) * 100 : 0;

        // Gastos recurrentes del mes
        $gastosRecurrentes = Gasto::recurrentes()
            ->delMes()
            ->sum('monto');

        return [
            Stat::make('Gastos Hoy', '$' . Number::format($gastosHoy, 0))
                ->description('Gastos del día actual')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Gastos del Mes', '$' . Number::format($gastosMes, 0))
                ->description($tendenciaMes >= 0 ? 
                    '+' . number_format($tendenciaMes, 1) . '% vs mes anterior' : 
                    number_format($tendenciaMes, 1) . '% vs mes anterior'
                )
                ->descriptionIcon($tendenciaMes >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tendenciaMes >= 0 ? 'warning' : 'success'),

            Stat::make('Gastos del Año', '$' . Number::format($gastosAño, 0))
                ->description($tendenciaAño >= 0 ? 
                    '+' . number_format($tendenciaAño, 1) . '% vs año anterior' : 
                    number_format($tendenciaAño, 1) . '% vs año anterior'
                )
                ->descriptionIcon($tendenciaAño >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tendenciaAño >= 0 ? 'danger' : 'success'),

            Stat::make('Gastos Recurrentes', '$' . Number::format($gastosRecurrentes, 0))
                ->description('Gastos fijos del mes')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),
        ];
    }
}
