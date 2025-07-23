<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CompraResource\Pages;
use App\Models\Compra;
use App\Models\CompraItem;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Helpers\CurrencyHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;
use Filament\Forms\Set;

class CompraResource extends Resource
{
    protected static ?string $model = Compra::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationLabel = 'Compras';
    
    protected static ?string $navigationGroup = 'Movimientos';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información General')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('numero_factura')
                                    ->label('Factura / Remisión')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(100),
                                    
                                DatePicker::make('fecha_compra')
                                    ->label('Fecha de Compra')
                                    ->required()
                                    ->default(now())
                                    ->native(false),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                Select::make('proveedor_id')
                                    ->label('Proveedor')
                                    ->relationship('proveedor', 'nombre')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('rfc')
                                            ->maxLength(13),
                                        TextInput::make('telefono')
                                            ->maxLength(20),
                                    ]),
                                    
                                Select::make('tipo_pago')
                                    ->label('Tipo de Pago')
                                    ->required()
                                    ->options([
                                        'contado' => 'Contado',
                                        'credito' => 'Crédito',
                                        'credito_enganche' => 'Crédito con Enganche'
                                    ])
                                    ->default('contado')
                                    ->live()
                                    ->native(false),
                            ]),
                    ]),
                    
                Section::make('Información de Crédito')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('fecha_limite_pago')
                                    ->label('Fecha Límite de Pago')
                                    ->native(false),
                                    
                                TextInput::make('monto_enganche')
                                    ->label('Monto de Enganche')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->minValue(0),
                            ]),
                    ])
                    ->visible(fn (Get $get): bool => in_array($get('tipo_pago'), ['credito', 'credito_enganche'])),
                    
                Section::make('Productos')
                    ->schema([
                        Repeater::make('items')
                            ->label('Items de Compra')
                            ->relationship()
                            ->required()
                            ->minItems(1)
                            ->defaultItems(1)
                            ->schema([
                                Grid::make(6)
                                    ->schema([
                                        Select::make('producto_id')
                                            ->label('Producto')
                                            ->relationship('producto', 'nombre')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->columnSpan(2)
                                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                                if ($state) {
                                                    $producto = Producto::find($state);
                                                    if ($producto) {
                                                        $set('precio_unitario', $producto->precio_compra ?? 0);
                                                    }
                                                }
                                                self::updateItemSubtotal($set, $get);
                                            }),
                                            
                                        TextInput::make('cantidad')
                                            ->label('Cantidad')
                                            ->required()
                                            ->numeric()
                                            ->step(0.01)
                                            ->minValue(0.01)
                                            ->columnSpan(1)
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                self::updateItemSubtotal($set, $get);
                                            }),
                                            
                                        TextInput::make('precio_unitario')
                                            ->label('Precio Unit.')
                                            ->required()
                                            ->numeric()
                                            ->step(0.01)
                                            ->minValue(0)
                                            ->prefix('$')
                                            ->columnSpan(1)
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                self::updateItemSubtotal($set, $get);
                                            }),
                                            
                                        TextInput::make('descuento')
                                            ->label('Descuento')
                                            ->numeric()
                                            ->step(0.01)
                                            ->minValue(0)
                                            ->prefix('$')
                                            ->default(0)
                                            ->columnSpan(1)
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                self::updateItemSubtotal($set, $get);
                                            }),
                                            
                                        TextInput::make('subtotal')
                                            ->label('Subtotal')
                                            ->numeric()
                                            ->prefix('$')
                                            ->readOnly()
                                            ->columnSpan(1)
                                            ->formatStateUsing(fn ($state) => CurrencyHelper::format($state ?? 0)),
                                    ]),
                                    
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('lote')
                                            ->label('Lote')
                                            ->maxLength(50)
                                            ->placeholder('Se genera automáticamente')
                                            ->columnSpan(1),
                                            
                                        DatePicker::make('fecha_vencimiento')
                                            ->label('Fecha de Vencimiento')
                                            ->native(false)
                                            ->columnSpan(1),
                                    ]),
                            ])
                            ->addActionLabel('Agregar Producto')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                $state['producto_id'] ? 
                                    Producto::find($state['producto_id'])?->nombre . ' - ' . CurrencyHelper::format($state['subtotal'] ?? 0) 
                                    : 'Nuevo Producto'
                            )
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                self::updateGrandTotal($set, $get);
                            })
                            ->deleteAction(
                                fn (Action $action) => $action->requiresConfirmation()->after(function (Set $set, Get $get) {
                                    self::updateGrandTotal($set, $get);
                                })
                            ),
                    ]),
                    
                Section::make('Total')
                    ->schema([
                        TextInput::make('total')
                            ->label('Total General')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated()
                            ->formatStateUsing(fn ($state) => CurrencyHelper::format($state ?? 0)),
                    ]),
                    
                Section::make('Observaciones')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Textarea::make('observaciones')
                                    ->label('Observaciones')
                                    ->maxLength(500)
                                    ->rows(3),
                                    
                                Textarea::make('notas')
                                    ->label('Notas Internas')
                                    ->maxLength(500)
                                    ->rows(3),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
    
    protected static function updateItemSubtotal(Set $set, Get $get): void
    {
        $cantidad = (float) ($get('cantidad') ?? 0);
        $precio = (float) ($get('precio_unitario') ?? 0);
        $descuento = (float) ($get('descuento') ?? 0);
        
        $subtotal = ($cantidad * $precio) - $descuento;
        
        $set('subtotal', number_format($subtotal, 2, '.', ''));
    }
    
    protected static function updateGrandTotal(Set $set, Get $get): void
    {
        $items = $get('items') ?? [];
        $total = 0;
        
        foreach ($items as $item) {
            $total += (float) ($item['subtotal'] ?? 0);
        }
        
        $set('total', number_format($total, 2, '.', ''));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero_factura')
                    ->label('N° Factura')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),
                    
                TextColumn::make('proveedor.nombre')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('items_count')
                    ->label('# Items')
                    ->counts('items')
                    ->alignCenter(),
                    
                TextColumn::make('items')
                    ->label('Productos')
                    ->formatStateUsing(function ($record) {
                        $productos = $record->items->pluck('producto.nombre')->take(3);
                        $texto = $productos->join(', ');
                        if ($record->items->count() > 3) {
                            $texto .= ' +' . ($record->items->count() - 3) . ' más';
                        }
                        return $texto;
                    })
                    ->limit(50),
                    
                TextColumn::make('total')
                    ->label('Total')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),
                    
                TextColumn::make('tipo_pago')
                    ->label('Tipo Pago')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'contado' => 'success',
                        'credito' => 'warning',
                        'credito_enganche' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'contado' => 'Contado',
                        'credito' => 'Crédito',
                        'credito_enganche' => 'Crédito + Enganche',
                        default => $state,
                    }),
                    
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'recibida' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente' => 'Pendiente',
                        'recibida' => 'Recibida',
                        default => $state,
                    }),
                    
                TextColumn::make('items.0.lote')
                    ->label('Lote (1er Item)')
                    ->toggleable()
                    ->searchable(),
                    
                TextColumn::make('fecha_compra')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                TextColumn::make('fecha_limite_pago')
                    ->label('Límite Pago')
                    ->date('d/m/Y')
                    ->toggleable()
                    ->color(function ($record): string {
                        if (!$record->fecha_limite_pago) return 'gray';
                        return $record->fecha_limite_pago->isPast() ? 'danger' : 'success';
                    }),
                    
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha_compra', 'desc')
            ->filters([
                SelectFilter::make('proveedor_id')
                    ->label('Proveedor')
                    ->relationship('proveedor', 'nombre'),
                    
                SelectFilter::make('tipo_pago')
                    ->label('Tipo de Pago')
                    ->options([
                        'contado' => 'Contado',
                        'credito' => 'Crédito',
                        'credito_enganche' => 'Crédito con Enganche'
                    ]),
                    
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'recibida' => 'Recibida'
                    ]),
                    
                Filter::make('fecha_range')
                    ->form([
                        DatePicker::make('fecha_desde')
                            ->label('Desde'),
                        DatePicker::make('fecha_hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['fecha_desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_compra', '>=', $date),
                            )
                            ->when(
                                $data['fecha_hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_compra', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('recibir')
                    ->label('Recibir')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Compra $record) {
                        $record->recibirCompra();
                    })
                    ->visible(fn (Compra $record): bool => $record->estado === 'pendiente')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Recepción')
                    ->modalDescription('¿Está seguro de que desea marcar esta compra como recibida? Esto actualizará el inventario.'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListCompras::route('/'),
            'create' => Pages\CreateCompra::route('/create'),
            'edit' => Pages\EditCompra::route('/{record}/edit'),
        ];
    }
}
