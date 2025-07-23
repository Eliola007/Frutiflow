<?php

namespace App\Filament\Admin\Resources\ClienteResource\Widgets;

use App\Models\Cliente;
use App\Models\PagoCliente;
use App\Helpers\CurrencyHelper;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class EstadisticasCreditoWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        // Deuda total por cobrar
        $deudaTotal = Cliente::sum('saldo_pendiente');
        
        // Clientes con deuda
        $clientesConDeuda = Cliente::where('saldo_pendiente', '>', 0)->count();
        
        // Clientes bloqueados
        $clientesBloqueados = Cliente::where('estado_credito', 'bloqueado')->count();
        
        // Límites de crédito próximos a agotarse (>= 80%)
        $limitesProximosAgotar = Cliente::whereRaw('(saldo_pendiente / NULLIF(limite_credito, 0)) >= 0.8')
            ->where('limite_credito', '>', 0)
            ->count();
            
        // Pagos del mes actual
        $pagosDelMes = PagoCliente::whereMonth('fecha_pago', Carbon::now()->month)
            ->whereYear('fecha_pago', Carbon::now()->year)
            ->sum('monto');
            
        // Promedio de días de crédito otorgado
        $promedioDiasCredito = Cliente::where('dias_credito', '>', 0)->avg('dias_credito');

        return [
            Stat::make('Deuda Total por Cobrar', CurrencyHelper::format($deudaTotal))
                ->description('Total adeudado por clientes')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color($deudaTotal > 100000 ? 'danger' : ($deudaTotal > 50000 ? 'warning' : 'success')),
                
            Stat::make('Clientes con Deuda', $clientesConDeuda)
                ->description('Clientes con saldo pendiente')
                ->descriptionIcon('heroicon-m-user-group')
                ->color($clientesConDeuda > 10 ? 'warning' : 'primary'),
                
            Stat::make('Clientes Bloqueados', $clientesBloqueados)
                ->description('Por exceder límite de crédito')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($clientesBloqueados > 0 ? 'danger' : 'success'),
                
            Stat::make('Límites Próximos a Agotar', $limitesProximosAgotar)
                ->description('Crédito usado >= 80%')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($limitesProximosAgotar > 0 ? 'warning' : 'success'),
                
            Stat::make('Pagos del Mes', CurrencyHelper::format($pagosDelMes))
                ->description('Total cobrado este mes')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
                
            Stat::make('Promedio Días Crédito', number_format($promedioDiasCredito, 1) . ' días')
                ->description('Días promedio otorgados')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}
