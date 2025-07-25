<?php

namespace App\Filament\Admin\Resources\InventarioResource\Widgets;

use App\Models\Inventario;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Support\Enums\FontWeight;
use Carbon\Carbon;

class InventarioVencimientosWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Productos Próximos a Vencer';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Inventario::query()
                    ->with(['producto', 'compra'])
                    ->where('estado', 'disponible')
                    ->where('cantidad_actual', '>', 0)
                    ->whereNotNull('fecha_vencimiento')
                    ->where('fecha_vencimiento', '<=', Carbon::now()->addDays(30))
                    ->orderBy('fecha_vencimiento', 'asc')
            )
            ->columns([
                TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->weight(FontWeight::Bold)
                    ->limit(30),

                TextColumn::make('lote')
                    ->label('Lote')
                    ->weight(FontWeight::Medium),

                TextColumn::make('cantidad_actual')
                    ->label('Cantidad')
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd(),

                TextColumn::make('fecha_vencimiento')
                    ->label('Vencimiento')
                    ->date('d/m/Y')
                    ->color(function ($record) {
                        $dias = $record->fecha_vencimiento->diffInDays(now(), false);
                        
                        if ($dias < 0) return 'danger'; // Vencido
                        if ($dias <= 3) return 'danger'; // Muy próximo
                        if ($dias <= 7) return 'warning'; // Próximo
                        return 'success'; // Normal
                    })
                    ->weight(FontWeight::Bold),

                BadgeColumn::make('dias_vencimiento')
                    ->label('Estado')
                    ->getStateUsing(function ($record) {
                        $dias = $record->fecha_vencimiento->diffInDays(now(), false);
                        
                        if ($dias < 0) return 'Vencido';
                        if ($dias == 0) return 'Vence hoy';
                        if ($dias == 1) return 'Vence mañana';
                        return "Vence en {$dias} días";
                    })
                    ->color(function ($record) {
                        $dias = $record->fecha_vencimiento->diffInDays(now(), false);
                        
                        if ($dias < 0) return 'danger';
                        if ($dias <= 3) return 'danger';
                        if ($dias <= 7) return 'warning';
                        return 'info';
                    }),

                TextColumn::make('valor_total')
                    ->label('Valor')
                    ->money('MXN', locale: 'es_MX')
                    ->getStateUsing(fn ($record) => $record->cantidad_actual * $record->precio_costo)
                    ->alignEnd(),
            ])
            ->actions([
                Tables\Actions\Action::make('ver_detalle')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.inventarios.edit', $record))
                    ->openUrlInNewTab(),
                    
                Tables\Actions\Action::make('marcar_vencido')
                    ->label('Marcar Vencido')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->fecha_vencimiento->isPast())
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->marcarComoVencido();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Lote marcado como vencido')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('fecha_vencimiento', 'asc')
            ->emptyStateHeading('¡Excelente!')
            ->emptyStateDescription('No hay productos próximos a vencer en los próximos 30 días.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
