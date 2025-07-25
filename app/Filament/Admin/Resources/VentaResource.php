<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VentaResource\Pages;
use App\Filament\Admin\Resources\VentaResource\Widgets;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
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
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationLabel = 'Ventas';
    
    protected static ?string $navigationGroup = 'Movimientos';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $modelLabel = 'Venta';
    
    protected static ?string $pluralModelLabel = 'Ventas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Nueva Venta')
                    ->description('Registro rápido de venta - Touch friendly')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('numero_venta')
                                    ->label('N° de Venta')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->default(fn () => 'V-' . now()->format('ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT))
                                    ->maxLength(50)
                                    ->extraAttributes(['class' => 'text-lg font-bold']),

                                Hidden::make('numero_interno')
                                    ->default(fn () => (new Venta())->generarNumeroInterno()),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Select::make('cliente_id')
                                    ->label('Cliente')
                                    ->relationship('cliente', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->default(fn () => Cliente::where('nombre', 'MOSTRADOR')->first()?->id)
                                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                                        $record->nombre . 
                                        ($record->limite_credito > 0 ? " (Crédito: $" . number_format($record->credito_disponible, 0) . ")" : "")
                                    )
                                    ->required()
                                    ->native(false)
                                    ->extraAttributes(['class' => 'text-lg']),

                                DatePicker::make('fecha_venta')
                                    ->label('Fecha')
                                    ->required()
                                    ->default(now())
                                    ->native(false)
                                    ->extraAttributes(['class' => 'text-lg']),

                                Select::make('estado')
                                    ->label('Estado')
                                    ->options([
                                        'pendiente' => 'Pendiente',
                                        'procesada' => 'Procesada',
                                        'enviada' => 'Enviada',
                                        'entregada' => 'Entregada',
                                        'cancelada' => 'Cancelada',
                                    ])
                                    ->default('procesada')
                                    ->required()
                                    ->native(false)
                                    ->extraAttributes(['class' => 'text-lg']),
                            ]),

                        Hidden::make('user_id')
                            ->default(fn () => \Illuminate\Support\Facades\Auth::id()),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Productos')
                    ->description('Agregar productos a la venta')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        Select::make('producto_id')
                                            ->label('Producto')
                                            ->relationship('producto', 'nombre')
                                            ->searchable(['nombre', 'codigo'])
                                            ->preload()
                                            ->required()
                                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                                $record->nombre . " - $" . number_format($record->precio_venta, 0) . 
                                                " (Stock: " . number_format($record->stock_actual, 0) . " " . $record->unidad_medida . ")"
                                            )
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $producto = Producto::find($state);
                                                    if ($producto) {
                                                        $set('precio_unitario', (string)$producto->precio_venta);
                                                        $set('cantidad', 1);
                                                        $set('descuento', '0');
                                                        $set('subtotal', $producto->precio_venta); // 1 * precio - (0 * 1)
                                                    }
                                                }
                                            })
                                            ->columnSpan(2)
                                            ->extraAttributes(['class' => 'text-base']),

                                        TextInput::make('cantidad')
                                            ->label('Cantidad')
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(0.01)
                                            ->step(1)
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                                $cantidad = is_numeric($state) ? floatval($state) : 1;
                                                $precio = is_numeric($get('precio_unitario')) ? floatval($get('precio_unitario')) : 0;
                                                $descuento = is_numeric($get('descuento')) ? floatval($get('descuento')) : 0;
                                                $subtotal = ($cantidad * $precio) - ($descuento * $cantidad);
                                                $set('subtotal', $subtotal);
                                            })
                                            ->extraAttributes(['class' => 'text-lg font-bold text-center']),

                                        TextInput::make('precio_unitario')
                                            ->label('Precio Unit.')
                                            ->required()
                                            ->reactive()
                                            ->placeholder('0.00')
                                            ->rule('numeric')
                                            ->rule('min:0.01')
                                            ->rule('max:999999.99')
                                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                                $precio = is_numeric($state) ? floatval($state) : 0;
                                                $cantidad = is_numeric($get('cantidad')) ? floatval($get('cantidad')) : 1;
                                                $descuento = is_numeric($get('descuento')) ? floatval($get('descuento')) : 0;
                                                $subtotal = ($cantidad * $precio) - ($descuento * $cantidad);
                                                $set('subtotal', $subtotal);
                                            })
                                            ->extraAttributes(['class' => 'text-lg font-bold']),
                                    ]),

                                Grid::make(4)
                                    ->schema([
                                        TextInput::make('descuento')
                                            ->label('Descuento')
                                            ->default('0')
                                            ->placeholder('0.00')
                                            ->rule('numeric')
                                            ->rule('min:0')
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                                $descuento = is_numeric($state) ? floatval($state) : 0;
                                                $cantidad = is_numeric($get('cantidad')) ? floatval($get('cantidad')) : 1;
                                                $precio = is_numeric($get('precio_unitario')) ? floatval($get('precio_unitario')) : 0;
                                                $subtotal = ($cantidad * $precio) - ($descuento * $cantidad);
                                                $set('subtotal', $subtotal);
                                            })
                                            ->extraAttributes(['class' => 'text-base']),

                                        TextInput::make('subtotal')
                                            ->label('Subtotal')
                                            ->numeric()
                                            ->disabled()
                                            ->dehydrated()
                                            ->default(0)
                                            ->extraAttributes(['class' => 'text-lg font-bold text-green-600'])
                                            ->columnSpan(2),

                                        Textarea::make('observaciones')
                                            ->label('Observaciones')
                                            ->rows(1)
                                            ->columnSpan(1),
                                    ]),
                            ])
                            ->columns(1)
                            ->minItems(1)
                            ->addActionLabel('➕ Agregar Producto')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                isset($state['producto_id']) && $state['producto_id'] 
                                    ? Producto::find($state['producto_id'])?->nombre . " x " . ($state['cantidad'] ?? 1)
                                    : 'Nuevo producto'
                            )
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $subtotalTotal = 0;
                                if (is_array($state)) {
                                    foreach ($state as $item) {
                                        $subtotal = isset($item['subtotal']) && is_numeric($item['subtotal']) 
                                            ? floatval($item['subtotal']) 
                                            : 0;
                                        $subtotalTotal += $subtotal;
                                    }
                                }
                                $set('subtotal', $subtotalTotal);
                                $descuentoTotal = is_numeric($get('descuento_total')) ? floatval($get('descuento_total')) : 0;
                                $set('total', $subtotalTotal - $descuentoTotal);
                            }),
                    ])
                    ->columns(1),

                Section::make('Totales')
                    ->description('Información financiera de la venta')
                    ->columns(3)
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated()
                            ->default(0)
                            ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                            ->extraAttributes(['class' => 'text-lg font-bold']),

                        TextInput::make('descuento_total')
                            ->label('Descuento Total')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->minValue(0)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $subtotal = $get('subtotal') ?? 0;
                                $total = $subtotal - $state;
                                $set('total', $total);
                            })
                            ->extraAttributes(['class' => 'text-lg']),

                        TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated()
                            ->default(0)
                            ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                            ->extraAttributes(['class' => 'text-xl font-bold text-green-600']),
                    ]),

                Section::make('Pago y Crédito')
                    ->description('Información de pago y crédito')
                    ->columns(3)
                    ->schema([
                        Select::make('metodo_pago')
                            ->label('Método de Pago')
                            ->options([
                                'efectivo' => 'Efectivo',
                                'tarjeta_debito' => 'Tarjeta de Débito',
                                'tarjeta_credito' => 'Tarjeta de Crédito',
                                'transferencia' => 'Transferencia',
                                'credito' => 'Crédito',
                                'mixto' => 'Pago Mixto',
                            ])
                            ->default('efectivo')
                            ->required()
                            ->native(false)
                            ->reactive()
                            ->extraAttributes(['class' => 'text-lg']),

                        Select::make('tipo_venta')
                            ->label('Tipo de Venta')
                            ->options([
                                'contado' => 'Al Contado',
                                'credito' => 'A Crédito',
                            ])
                            ->default('contado')
                            ->required()
                            ->native(false)
                            ->reactive()
                            ->extraAttributes(['class' => 'text-lg']),

                        TextInput::make('credito_disponible')
                            ->label('Crédito Disponible')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(function ($state, callable $get) {
                                $clienteId = $get('cliente_id');
                                if ($clienteId) {
                                    $cliente = \App\Models\Cliente::find($clienteId);
                                    return $cliente ? number_format($cliente->credito_disponible, 0, ',', '.') : '0';
                                }
                                return '0';
                            })
                            ->extraAttributes(['class' => 'text-lg font-bold text-blue-600']),

                        TextInput::make('monto_recibido')
                            ->label('Monto Recibido')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $total = $get('total') ?? 0;
                                $cambio = $state - $total;
                                $set('cambio', max(0, $cambio));
                            })
                            ->visible(fn (callable $get) => in_array($get('metodo_pago'), ['efectivo', 'mixto']))
                            ->extraAttributes(['class' => 'text-lg']),

                        TextInput::make('cambio')
                            ->label('Cambio')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated()
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 0, ',', '.'))
                            ->visible(fn (callable $get) => in_array($get('metodo_pago'), ['efectivo', 'mixto']))
                            ->extraAttributes(['class' => 'text-lg font-bold text-green-600']),
                    ])
                    ->collapsible(),

                Section::make('Observaciones')
                    ->schema([
                        Textarea::make('observaciones')
                            ->label('Observaciones de la venta')
                            ->rows(2)
                            ->columnSpanFull(),

                        Textarea::make('notas')
                            ->label('Notas internas')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->collapsed()
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero_venta')
                    ->label('N° Venta')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('primary'),

                TextColumn::make('numero_interno')
                    ->label('N° Interno')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->limit(25)
                    ->tooltip(function (TextColumn $column): ?string {
                        $record = $column->getRecord();
                        $cliente = $record->cliente;
                        if (!$cliente || strlen($cliente->nombre) <= 25) return null;
                        return $cliente->nombre;
                    }),

                TextColumn::make('fecha_venta')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->badge()
                    ->color('info'),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('descuento_general')
                    ->label('Descuento')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable()
                    ->toggleable()
                    ->placeholder('$0'),

                TextColumn::make('total')
                    ->label('Total')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),

                TextColumn::make('tipo_venta')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'contado' => 'Contado',
                        'credito' => 'Crédito',
                        'mayoreo' => 'Mayoreo',
                        'menudeo' => 'Menudeo',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'contado' => 'success',
                        'credito' => 'warning',
                        'mayoreo' => 'info',
                        'menudeo' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('metodo_pago')
                    ->label('Método de Pago')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'efectivo' => 'Efectivo',
                        'tarjeta' => 'Tarjeta',
                        'transferencia' => 'Transferencia',
                        'cheque' => 'Cheque',
                        'mixto' => 'Mixto',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'efectivo' => 'success',
                        'tarjeta' => 'info',
                        'transferencia' => 'primary',
                        'cheque' => 'warning',
                        'mixto' => 'secondary',
                        default => 'gray',
                    })
                    ->toggleable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente' => 'Pendiente',
                        'procesada' => 'Procesada',
                        'entregada' => 'Entregada',
                        'cancelada' => 'Cancelada',
                        'devuelta' => 'Devuelta',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'procesada' => 'info',
                        'entregada' => 'success',
                        'cancelada' => 'danger',
                        'devuelta' => 'gray',
                        default => 'primary',
                    }),

                TextColumn::make('monto_anticipo')
                    ->label('Anticipo')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->toggleable()
                    ->placeholder('$0')
                    ->visible(fn ($record) => $record && $record->tipo_venta === 'credito'),

                TextColumn::make('vendedor.name')
                    ->label('Vendedor')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Registrada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha_venta', 'desc')
            ->filters([
                SelectFilter::make('tipo_venta')
                    ->label('Tipo de Venta')
                    ->options([
                        'contado' => 'Contado',
                        'credito' => 'Crédito',
                        'mayoreo' => 'Mayoreo',
                        'menudeo' => 'Menudeo',
                    ]),

                SelectFilter::make('metodo_pago')
                    ->label('Método de Pago')
                    ->options([
                        'efectivo' => 'Efectivo',
                        'tarjeta' => 'Tarjeta',
                        'transferencia' => 'Transferencia',
                        'cheque' => 'Cheque',
                        'mixto' => 'Mixto',
                    ]),

                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'procesada' => 'Procesada',
                        'entregada' => 'Entregada',
                        'cancelada' => 'Cancelada',
                        'devuelta' => 'Devuelta',
                    ]),

                SelectFilter::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre')
                    ->searchable()
                    ->preload(),

                Filter::make('fecha_venta')
                    ->form([
                        DatePicker::make('from')
                            ->label('Desde'),
                        DatePicker::make('until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_venta', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_venta', '<=', $date),
                            );
                    }),

                Filter::make('monto_minimo')
                    ->form([
                        TextInput::make('min_amount')
                            ->label('Monto mínimo')
                            ->numeric(),
                        TextInput::make('max_amount')
                            ->label('Monto máximo')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('total', '>=', $amount),
                            )
                            ->when(
                                $data['max_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('total', '<=', $amount),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\Action::make('print_ticket')
                        ->label('🖨️ Previsualizar Ticket')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->url(fn ($record) => route('ticket.venta.preview', $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('print_normal')
                        ->label('📄 Ticket Normal')
                        ->icon('heroicon-o-document-text')
                        ->color('primary')
                        ->url(fn ($record) => route('ticket.venta', $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('print_compact')
                        ->label('📱 Ticket Compacto')
                        ->icon('heroicon-o-device-phone-mobile')
                        ->color('secondary')
                        ->url(fn ($record) => route('ticket.venta.compacto', $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('process')
                        ->label('✅ Procesar Venta')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn ($record) => $record->estado === 'pendiente')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            if ($record->procesarVenta()) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Venta procesada exitosamente')
                                    ->success()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Error: Stock insuficiente')
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn ($record) => $record->estado === 'pendiente'),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Eliminar ventas seleccionadas')
                        ->modalDescription('¿Estás seguro de que quieres eliminar las ventas seleccionadas? Solo se pueden eliminar ventas en estado pendiente.')
                        ->modalSubmitActionLabel('Sí, eliminar')
                        ->before(function ($records) {
                            // Verificar que todas las ventas estén en estado pendiente
                            foreach ($records as $record) {
                                if ($record->estado !== 'pendiente') {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Error: No se pueden eliminar ventas procesadas')
                                        ->body('Solo se pueden eliminar ventas en estado pendiente.')
                                        ->danger()
                                        ->send();
                                    return false;
                                }
                            }
                        }),
                ]),
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
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\VentasWidget::class,
        ];
    }
}
