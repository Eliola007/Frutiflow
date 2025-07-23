<?php

namespace App\Filament\Admin\Resources\ClienteResource\Widgets;

use App\Models\Cliente;
use App\Helpers\CurrencyHelper;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class ClientesMayorDeudaWidget extends BaseWidget
{
    protected static ?string $heading = 'Clientes con Mayor Deuda';
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Cliente::query()
                    ->where('saldo_pendiente', '>', 0)
                    ->orderBy('saldo_pendiente', 'desc')
                    ->limit(10)
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
                    ->label('Saldo Pendiente')
                    ->formatStateUsing(fn ($state) => CurrencyHelper::format($state))
                    ->sortable()
                    ->color('danger'),
                    
                TextColumn::make('limite_credito')
                    ->label('Límite de Crédito')
                    ->formatStateUsing(fn ($state) => CurrencyHelper::format($state))
                    ->sortable(),
                    
                TextColumn::make('porcentaje_credito_usado')
                    ->label('% Crédito Usado')
                    ->formatStateUsing(fn ($state) => number_format($state, 1) . '%')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 90 => 'danger',
                        $state >= 70 => 'warning',
                        default => 'success',
                    }),
                    
                BadgeColumn::make('estado_credito')
                    ->label('Estado')
                    ->colors([
                        'success' => 'activo',
                        'warning' => 'suspendido',
                        'danger' => 'bloqueado',
                    ]),
            ])
            ->defaultSort('saldo_pendiente', 'desc')
            ->striped();
    }
}
