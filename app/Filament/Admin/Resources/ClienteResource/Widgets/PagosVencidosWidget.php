<?php

namespace App\Filament\Admin\Resources\ClienteResource\Widgets;

use App\Models\Cliente;
use App\Helpers\CurrencyHelper;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Carbon\Carbon;

class PagosVencidosWidget extends BaseWidget
{
    protected static ?string $heading = 'Clientes con Crédito Vencido';
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Cliente::query()
                    ->where('saldo_pendiente', '>', 0)
                    ->where('dias_credito', '>', 0)
                    ->whereRaw('DATE(updated_at, "+" || dias_credito || " days") < DATE("now")')
                    ->orderBy('updated_at', 'asc')
            )
            ->columns([
                TextColumn::make('nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('documento')
                    ->label('Documento')
                    ->searchable(),
                    
                TextColumn::make('telefono')
                    ->label('Teléfono'),
                    
                TextColumn::make('saldo_pendiente')
                    ->label('Saldo Vencido')
                    ->formatStateUsing(fn ($state) => CurrencyHelper::format($state))
                    ->sortable()
                    ->color('danger'),
                    
                TextColumn::make('dias_credito')
                    ->label('Días de Crédito')
                    ->suffix(' días'),
                    
                TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                    
                TextColumn::make('dias_vencido')
                    ->label('Días Vencido')
                    ->getStateUsing(function ($record) {
                        $fechaVencimiento = $record->updated_at->addDays($record->dias_credito);
                        return Carbon::now()->diffInDays($fechaVencimiento, false) * -1;
                    })
                    ->suffix(' días')
                    ->badge()
                    ->color('danger'),
                    
                BadgeColumn::make('estado_credito')
                    ->label('Estado')
                    ->colors([
                        'success' => 'activo',
                        'warning' => 'suspendido',
                        'danger' => 'bloqueado',
                    ]),
            ])
            ->defaultSort('updated_at', 'asc')
            ->striped()
            ->emptyStateDescription('No hay clientes con pagos vencidos')
            ->emptyStateHeading('¡Excelente!')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
