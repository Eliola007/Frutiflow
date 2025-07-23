<?php

namespace App\Filament\Admin\Resources\ProveedorResource\Widgets;

use App\Models\Proveedor;
use App\Models\PagoProveedor;
use App\Helpers\CurrencyHelper;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class EstadisticasProveedoresWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        // Deuda total por pagar a proveedores
        $deudaTotal = Proveedor::sum('saldo_pendiente');
        
        // Proveedores con deuda
        $proveedoresConDeuda = Proveedor::where('saldo_pendiente', '>', 0)->count();
        
        // Proveedores bloqueados
        $proveedoresBloqueados = Proveedor::where('estado_credito', 'bloqueado')->count();
        
        // Límites de crédito próximos a agotar (>= 80%)
        $limitesProximosAgotar = Proveedor::whereRaw('(saldo_pendiente / NULLIF(limite_credito, 0)) >= 0.8')
            ->where('limite_credito', '>', 0)
            ->count();
            
        // Pagos del mes actual a proveedores
        $pagosDelMes = PagoProveedor::whereMonth('fecha_pago', Carbon::now()->month)
            ->whereYear('fecha_pago', Carbon::now()->year)
            ->sum('monto');
            
        // Promedio de días de crédito otorgado por proveedores
        $promedioDiasCredito = Proveedor::where('dias_credito', '>', 0)->avg('dias_credito');

        return [
            Stat::make('Deuda Total por Pagar', CurrencyHelper::format($deudaTotal))
                ->description('Total adeudado a proveedores')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color($deudaTotal > 200000 ? 'danger' : ($deudaTotal > 100000 ? 'warning' : 'success')),
                
            Stat::make('Proveedores con Deuda', $proveedoresConDeuda)
                ->description('Proveedores con saldo pendiente')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color($proveedoresConDeuda > 15 ? 'warning' : 'primary'),
                
            Stat::make('Proveedores Bloqueados', $proveedoresBloqueados)
                ->description('Por exceder límite de crédito')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($proveedoresBloqueados > 0 ? 'danger' : 'success'),
                
            Stat::make('Límites Próximos a Agotar', $limitesProximosAgotar)
                ->description('Crédito usado >= 80%')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($limitesProximosAgotar > 0 ? 'warning' : 'success'),
                
            Stat::make('Pagos del Mes', CurrencyHelper::format($pagosDelMes))
                ->description('Total pagado este mes')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
                
            Stat::make('Promedio Días Crédito', number_format($promedioDiasCredito, 1) . ' días')
                ->description('Días promedio de crédito')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('gray'),
        ];
    }
}
