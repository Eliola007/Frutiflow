<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InventarioResource\Pages;
use App\Filament\Admin\Resources\InventarioResource\Widgets;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Compra;
use App\Helpers\CurrencyHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class InventarioResource extends Resource
{
    protected static ?string $model = Inventario::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $navigationLabel = 'Inventario PEPS';
    
    protected static ?string $navigationGroup = 'Inventario y Productos';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $modelLabel = 'Registro de Inventario';
    
    protected static ?string $pluralModelLabel = 'Inventario PEPS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Lote')
                    ->description('Datos principales del registro de inventario')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('producto_id')
                                    ->label('Producto')
                                    ->relationship('producto', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $producto = Producto::find($state);
                                            if ($producto) {
                                                $set('precio_costo', $producto->precio_costo ?? 0);
                                            }
                                        }
                                    }),

                                Select::make('compra_id')
                                    ->label('Compra Origen')
                                    ->relationship('compra', 'numero_factura')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Seleccionar compra (opcional)'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('lote')
                                    ->label('Lote')
                                    ->required()
                                    ->maxLength(50)
                                    ->placeholder('LOTE-001-2025')
                                    ->helperText('Identificador único del lote'),

                                DatePicker::make('fecha_ingreso')
                                    ->label('Fecha de Ingreso')
                                    ->required()
                                    ->default(now())
                                    ->native(false),

                                DatePicker::make('fecha_vencimiento')
                                    ->label('Fecha de Vencimiento')
                                    ->native(false)
                                    ->helperText('Opcional para productos no perecederos'),
                            ]),
                    ])
                    ->columns(1),

                Section::make('Cantidades y Costos')
                    ->description('Control de cantidades y valores monetarios')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('cantidad_inicial')
                                    ->label('Cantidad Inicial')
                                    ->numeric()
                                    ->required()
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        // Si no hay cantidad actual, usar la inicial
                                        if (!$get('cantidad_actual')) {
                                            $set('cantidad_actual', $state);
                                        }
                                    }),

                                TextInput::make('cantidad_actual')
                                    ->label('Cantidad Actual')
                                    ->numeric()
                                    ->required()
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $inicial = $get('cantidad_inicial');
                                        $precio = $get('precio_costo');
                                        
                                        // Validar que no exceda la cantidad inicial
                                        if ($inicial && $state > $inicial) {
                                            $set('cantidad_actual', $inicial);
                                        }
                                        
                                        // Calcular valor total
                                        if ($precio) {
                                            $valor = $state * $precio;
                                            $set('valor_total', number_format($valor, 2));
                                        }
                                    }),

                                TextInput::make('precio_costo')
                                    ->label('Precio de Costo')
                                    ->numeric()
                                    ->required()
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->prefix('$')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $cantidad = $get('cantidad_actual');
                                        if ($cantidad) {
                                            $valor = $cantidad * $state;
                                            $set('valor_total', number_format($valor, 2));
                                        }
                                    }),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Placeholder::make('valor_total')
                                    ->label('Valor Total del Lote')
                                    ->content(function (callable $get) {
                                        $cantidad = $get('cantidad_actual') ?? 0;
                                        $precio = $get('precio_costo') ?? 0;
                                        $total = $cantidad * $precio;
                                        return '$' . number_format($total, 2);
                                    }),

                                Select::make('estado')
                                    ->label('Estado')
                                    ->options([
                                        'disponible' => 'Disponible',
                                        'reservado' => 'Reservado',
                                        'vendido' => 'Vendido',
                                        'vencido' => 'Vencido',
                                        'dañado' => 'Dañado',
                                        'devuelto' => 'Devuelto'
                                    ])
                                    ->default('disponible')
                                    ->required()
                                    ->helperText('Estado actual del lote'),
                            ]),
                    ])
                    ->columns(1),

                Section::make('Observaciones')
                    ->description('Notas adicionales sobre el lote')
                    ->schema([
                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Notas sobre el estado, calidad, etc.'),
                    ])
                    ->collapsed()
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),

                TextColumn::make('lote')
                    ->label('Lote')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('fecha_ingreso')
                    ->label('Ingreso')
                    ->date('d/m/Y')
                    ->sortable()
                    ->tooltip(function ($record) {
                        return 'Días en inventario: ' . $record->fecha_ingreso->diffInDays(now());
                    }),

                TextColumn::make('fecha_vencimiento')
                    ->label('Vencimiento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(function ($record) {
                        if (!$record->fecha_vencimiento) return null;
                        
                        $dias = $record->fecha_vencimiento->diffInDays(now(), false);
                        
                        if ($dias < 0) return 'danger'; // Vencido
                        if ($dias <= 7) return 'warning'; // Próximo a vencer
                        return 'success'; // Fresco
                    })
                    ->tooltip(function ($record) {
                        if (!$record->fecha_vencimiento) return 'Sin fecha de vencimiento';
                        
                        $dias = $record->fecha_vencimiento->diffInDays(now(), false);
                        
                        if ($dias < 0) return 'Vencido hace ' . abs($dias) . ' días';
                        if ($dias == 0) return 'Vence hoy';
                        return 'Vence en ' . $dias . ' días';
                    })
                    ->placeholder('Sin venc.'),

                TextColumn::make('cantidad_inicial')
                    ->label('Cant. Inicial')
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd()
                    ->toggleable(),

                TextColumn::make('cantidad_actual')
                    ->label('Cant. Actual')
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd()
                    ->color(function ($record) {
                        $porcentaje = $record->cantidad_inicial > 0 
                            ? ($record->cantidad_actual / $record->cantidad_inicial) * 100 
                            : 0;
                        
                        if ($porcentaje <= 10) return 'danger';
                        if ($porcentaje <= 30) return 'warning';
                        return 'success';
                    })
                    ->weight(FontWeight::Bold),

                TextColumn::make('precio_costo')
                    ->label('Costo Unit.')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('valor_total')
                    ->label('Valor Total')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->getStateUsing(fn ($record) => $record->cantidad_actual * $record->precio_costo)
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'disponible' => 'Disponible',
                        'reservado' => 'Reservado', 
                        'vendido' => 'Vendido',
                        'vencido' => 'Vencido',
                        'dañado' => 'Dañado',
                        'devuelto' => 'Devuelto',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'disponible' => 'success',
                        'reservado' => 'warning',
                        'vendido' => 'info',
                        'vencido' => 'danger',
                        'dañado' => 'danger',
                        'devuelto' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('compra.numero_factura')
                    ->label('Compra')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Manual'),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha_ingreso', 'asc')
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'disponible' => 'Disponible',
                        'reservado' => 'Reservado',
                        'vendido' => 'Vendido',
                        'vencido' => 'Vencido',
                        'dañado' => 'Dañado',
                        'devuelto' => 'Devuelto'
                    ])
                    ->default('disponible'),

                SelectFilter::make('producto_id')
                    ->label('Producto')
                    ->relationship('producto', 'nombre')
                    ->searchable()
                    ->preload(),

                Filter::make('proximos_vencer')
                    ->label('Próximos a Vencer')
                    ->query(fn (Builder $query): Builder => $query->proximosAVencer(7))
                    ->toggle(),

                Filter::make('vencidos')
                    ->label('Vencidos')
                    ->query(fn (Builder $query): Builder => $query->vencidos())
                    ->toggle(),

                Filter::make('sin_stock')
                    ->label('Sin Stock')
                    ->query(fn (Builder $query): Builder => $query->where('cantidad_actual', '<=', 0))
                    ->toggle(),

                Filter::make('fecha_ingreso')
                    ->form([
                        DatePicker::make('desde')
                            ->label('Desde'),
                        DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_ingreso', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_ingreso', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\Action::make('marcar_vencido')
                        ->label('Marcar Vencido')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn ($record) => $record->estado !== 'vencido' && $record->vencido)
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->marcarComoVencido();
                            \Filament\Notifications\Notification::make()
                                ->title('Lote marcado como vencido')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('ajustar_stock')
                        ->label('Ajustar Stock')
                        ->icon('heroicon-o-calculator')
                        ->color('warning')
                        ->form([
                            TextInput::make('nueva_cantidad')
                                ->label('Nueva Cantidad')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                                ->default(fn ($record) => $record->cantidad_actual),
                            Textarea::make('motivo')
                                ->label('Motivo del Ajuste')
                                ->required()
                                ->placeholder('Ej: Merma, rotura, corrección de inventario'),
                        ])
                        ->action(function ($record, array $data) {
                            $cantidadAnterior = $record->cantidad_actual;
                            $nuevaCantidad = $data['nueva_cantidad'];
                            $diferencia = $nuevaCantidad - $cantidadAnterior;
                            
                            $record->update([
                                'cantidad_actual' => $nuevaCantidad,
                                'observaciones' => $record->observaciones . "\n" . 
                                    "[" . now()->format('d/m/Y H:i') . "] Ajuste: " . 
                                    "De {$cantidadAnterior} a {$nuevaCantidad} " .
                                    "(" . ($diferencia >= 0 ? '+' : '') . "{$diferencia}). Motivo: " . $data['motivo']
                            ]);

                            // Actualizar stock del producto
                            $producto = $record->producto;
                            $producto->stock_actual += $diferencia;
                            $producto->save();

                            \Filament\Notifications\Notification::make()
                                ->title('Stock ajustado correctamente')
                                ->body("Diferencia: " . ($diferencia >= 0 ? '+' : '') . "{$diferencia} unidades")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('generar_reporte')
                        ->label('Reporte PEPS')
                        ->icon('heroicon-o-document-text')
                        ->color('info')
                        ->url(function ($record) {
                            return route('inventario.reporte-peps', $record->producto_id);
                        })
                        ->openUrlInNewTab(),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn ($record) => $record->cantidad_actual == 0),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Eliminar registros de inventario')
                        ->modalDescription('¿Estás seguro? Solo se pueden eliminar registros sin stock.')
                        ->before(function ($records) {
                            // Verificar que todos los registros no tengan stock
                            foreach ($records as $record) {
                                if ($record->cantidad_actual > 0) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Error')
                                        ->body('No se pueden eliminar registros con stock disponible')
                                        ->danger()
                                        ->send();
                                    return false;
                                }
                            }
                        }),
                    
                    Tables\Actions\BulkAction::make('marcar_vencidos')
                        ->label('Marcar como Vencidos')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->vencido && $record->estado !== 'vencido') {
                                    $record->marcarComoVencido();
                                    $count++;
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title("{$count} lotes marcados como vencidos")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('exportar_seleccionados')
                        ->label('Exportar Seleccionados')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->action(function ($records) {
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="inventario-seleccionado-' . date('Y-m-d') . '.csv"',
                            ];

                            $callback = function() use ($records) {
                                $file = fopen('php://output', 'w');
                                
                                // UTF-8 BOM
                                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                                
                                // Headers
                                fputcsv($file, [
                                    'Producto', 'Lote', 'Fecha Ingreso', 'Fecha Vencimiento',
                                    'Cantidad Inicial', 'Cantidad Actual', 'Precio Costo',
                                    'Valor Total', 'Estado'
                                ]);

                                foreach ($records as $item) {
                                    fputcsv($file, [
                                        $item->producto->nombre ?? '',
                                        $item->lote,
                                        $item->fecha_ingreso->format('d/m/Y'),
                                        $item->fecha_vencimiento ? $item->fecha_vencimiento->format('d/m/Y') : '',
                                        $item->cantidad_inicial,
                                        $item->cantidad_actual,
                                        $item->precio_costo,
                                        $item->valor_total,
                                        $item->estado
                                    ]);
                                }

                                fclose($file);
                            };

                            return response()->stream($callback, 200, $headers);
                        }),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('reporte_general')
                    ->label('📊 Reporte General')
                    ->color('info')
                    ->url(route('inventario.reporte-general'))
                    ->openUrlInNewTab(),
                    
                Tables\Actions\Action::make('reporte_vencimientos')
                    ->label('⏰ Vencimientos')
                    ->color('warning')
                    ->url(route('inventario.reporte-vencimientos'))
                    ->openUrlInNewTab(),
                    
                Tables\Actions\Action::make('exportar_completo')
                    ->label('📥 Exportar Todo')
                    ->color('success')
                    ->url(route('inventario.exportar-csv'))
                    ->openUrlInNewTab(),
                    
                Tables\Actions\Action::make('actualizar_vencimientos')
                    ->label('🔄 Actualizar Vencidos')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Actualizar productos vencidos')
                    ->modalDescription('Esta acción marcará automáticamente como vencidos todos los productos que hayan pasado su fecha de vencimiento.')
                    ->action(function () {
                        // Ejecutar el comando de actualización
                        $exitCode = Artisan::call('inventario:actualizar-vencimientos', ['--force' => true]);
                        
                        if ($exitCode === 0) {
                            \Filament\Notifications\Notification::make()
                                ->title('Vencimientos actualizados')
                                ->body('Se han procesado todos los productos vencidos')
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Error al actualizar')
                                ->body('Hubo un problema al procesar los vencimientos')
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventarios::route('/'),
            'create' => Pages\CreateInventario::route('/create'),
            'edit' => Pages\EditInventario::route('/{record}/edit'),
            'estadisticas' => Pages\EstadisticasInventario::route('/estadisticas'),
            'ajustes' => Pages\AjustesInventario::route('/ajustes'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\InventarioStatsWidget::class,
            Widgets\InventarioVencimientosWidget::class,
        ];
    }
}
