<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductoResource\Pages;
use App\Filament\Admin\Resources\ProductoResource\Widgets;
use App\Models\Producto;
use App\Helpers\CurrencyHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Collection;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $navigationLabel = 'Productos';
    
    protected static ?string $navigationGroup = 'Inventario y Productos';
    
    protected static ?string $modelLabel = 'Producto';
    
    protected static ?string $pluralModelLabel = 'Productos';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Básica')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('codigo')
                                    ->label('Código')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50)
                                    ->placeholder('Ej: MANZ001'),
                                    
                                TextInput::make('nombre')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ej: Manzana Red Delicious'),
                            ]),
                            
                        Grid::make(3)
                            ->schema([
                                Select::make('calidad')
                                    ->label('Calidad')
                                    ->options([
                                        '1A' => 'Primera A (1A)',
                                        '2A' => 'Segunda A (2A)', 
                                        '3A' => 'Tercera A (3A)',
                                    ])
                                    ->required()
                                    ->native(false),
                                    
                                Select::make('tamaño')
                                    ->label('Tamaño')
                                    ->options([
                                        'Small' => 'Pequeño (S)',
                                        'Medium' => 'Mediano (M)',
                                        'Large' => 'Grande (L)',
                                        'X-Large' => 'Extra Grande (XL)',
                                        'ND' => 'No Definido (ND)',
                                    ])
                                    ->required()
                                    ->native(false),
                                    
                                Select::make('grupo')
                                    ->label('Grupo')
                                    ->options([
                                        'Cítricos' => 'Cítricos',
                                        'Tropicales' => 'Tropicales',
                                        'Berries' => 'Berries',
                                        'Manzanas' => 'Manzanas',
                                        'Peras' => 'Peras',
                                        'Bananos' => 'Bananos',
                                        'Uvas' => 'Uvas',
                                        'Melones' => 'Melones',
                                        'Otros' => 'Otros',
                                    ])
                                    ->required()
                                    ->native(false),
                            ]),
                            
                        Toggle::make('configuracion_avanzada')
                            ->label('Configuración Avanzada de Unidades')
                            ->helperText('Activar para configurar unidades personalizadas (por defecto: Caja)')
                            ->live(),
                    ]),
                    
                Section::make('Configuración de Unidades')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('unidad_base')
                                    ->label('Unidad Base')
                                    ->options([
                                        'caja' => 'Cajas',
                                        'kg' => 'Kilogramos (kg)',
                                        'lb' => 'Libras (lb)',
                                        'ton' => 'Toneladas (ton)',
                                        'und' => 'Unidades (und)',
                                        'bulto' => 'Bultos',
                                    ])
                                    ->default('caja')
                                    ->native(false),
                                    
                                TextInput::make('factor_conversion')
                                    ->label('Factor de Conversión')
                                    ->numeric()
                                    ->step(0.001)
                                    ->default(20.000)
                                    ->helperText('Factor numérico de conversión (ej: 1 tonelada = 200 cajas, 1 caja = 20 kg)'),
                            ]),
                    ])
                    ->visible(fn (callable $get) => $get('configuracion_avanzada')),
                    
                Section::make('Precios de Referencia')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('precio_compra_referencia')
                                    ->label('Precio Compra Promedio')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix(CurrencyHelper::getCurrencySymbol())
                                    ->helperText('Calculado automáticamente como promedio de compras del año ' . now()->year)
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(function (callable $get) {
                                        // Si es un producto existente, calcular el promedio
                                        $record = $get('../../record');
                                        if ($record instanceof \App\Models\Producto) {
                                            return $record->calcularPrecioPromedioCompraAnual();
                                        }
                                        return 0;
                                    })
                                    ->formatStateUsing(function ($state, $record) {
                                        if ($record instanceof \App\Models\Producto) {
                                            return $record->calcularPrecioPromedioCompraAnual();
                                        }
                                        return $state ?? 0;
                                    }),
                                    
                                TextInput::make('precio_venta_sugerido')
                                    ->label('Precio Venta promedio')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix(CurrencyHelper::getCurrencySymbol())
                                    ->helperText('Calculado automáticamente como promedio de ventas del año ' . now()->year)
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(function (callable $get) {
                                        // Si es un producto existente, calcular el promedio
                                        $record = $get('../../record');
                                        if ($record instanceof \App\Models\Producto) {
                                            return $record->calcularPrecioPromedioVentaAnual();
                                        }
                                        return 0;
                                    })
                                    ->formatStateUsing(function ($state, $record) {
                                        if ($record instanceof \App\Models\Producto) {
                                            return $record->calcularPrecioPromedioVentaAnual();
                                        }
                                        return $state ?? 0;
                                    }),
                            ]),
                    ]),
                    
                Section::make('Estado del Producto')
                    ->schema([
                        Toggle::make('activo')
                            ->label('Producto Activo')
                            ->helperText('Desactivar para ocultar el producto sin eliminarlo')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                    
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                TextColumn::make('calidad')
                    ->label('Calidad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1A' => 'success',
                        '2A' => 'warning',
                        '3A' => 'danger',
                        default => 'gray',
                    }),
                    
                TextColumn::make('tamaño')
                    ->label('Tamaño')
                    ->badge()
                    ->color('info'),
                    
                TextColumn::make('grupo')
                    ->label('Grupo')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('unidad_base')
                    ->label('Unidad')
                    ->badge()
                    ->color('gray'),
                    
                TextColumn::make('configuracion_avanzada')
                    ->label('Config. Avanzada')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
                    
                TextColumn::make('factor_conversion')
                    ->label('Factor')
                    ->formatStateUsing(fn ($state) => number_format($state, 1))
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('stock_actual')
                    ->label('Stock Actual')
                    ->formatStateUsing(fn ($record, $state) => number_format($state ?? 0, 2) . ' ' . $record->unidad_base)
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->weight(FontWeight::Bold),
                    
                TextColumn::make('stock_disponible')
                    ->label('Stock Disponible')
                    ->formatStateUsing(fn ($record, $state) => number_format($state ?? 0, 2) . ' ' . $record->unidad_base)
                    ->color(fn ($state) => $state > 0 ? 'success' : 'warning'),
                    
                TextColumn::make('precio_compra_referencia')
                    ->label('P. Compra')
                    ->state(function ($record) {
                        return $record->calcularPrecioPromedioCompraAnual();
                    })
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('precio_venta_sugerido')
                    ->label('P. Venta')
                    ->state(function ($record) {
                        return $record->calcularPrecioPromedioVentaAnual();
                    })
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('margen_ganancia')
                    ->label('Margen %')
                    ->state(function ($record) {
                        $precioCompra = $record->calcularPrecioPromedioCompraAnual();
                        $precioVenta = $record->calcularPrecioPromedioVentaAnual();
                        
                        if ($precioCompra && $precioVenta && $precioCompra > 0) {
                            $margen = (($precioVenta - $precioCompra) / $precioCompra) * 100;
                            return number_format($margen, 1) . '%';
                        }
                        return 'N/A';
                    })
                    ->color(function ($record) {
                        $precioCompra = $record->calcularPrecioPromedioCompraAnual();
                        $precioVenta = $record->calcularPrecioPromedioVentaAnual();
                        
                        if ($precioCompra && $precioVenta && $precioCompra > 0) {
                            $margen = (($precioVenta - $precioCompra) / $precioCompra) * 100;
                            return $margen >= 30 ? 'success' : ($margen >= 15 ? 'warning' : 'danger');
                        }
                        return 'gray';
                    })
                    ->toggleable(),
                    
                TextColumn::make('activo')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Activo' : 'Inactivo')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                    
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('calidad')
                    ->label('Calidad')
                    ->options([
                        '1A' => 'Primera A (1A)',
                        '2A' => 'Segunda A (2A)',
                        '3A' => 'Tercera A (3A)',
                    ])
                    ->multiple(),
                    
                SelectFilter::make('grupo')
                    ->label('Grupo')
                    ->options([
                        'Cítricos' => 'Cítricos',
                        'Tropicales' => 'Tropicales',
                        'Berries' => 'Berries',
                        'Manzanas' => 'Manzanas',
                        'Peras' => 'Peras',
                        'Bananos' => 'Bananos',
                        'Uvas' => 'Uvas',
                        'Melones' => 'Melones',
                        'Otros' => 'Otros',
                    ])
                    ->multiple(),
                    
                SelectFilter::make('tamaño')
                    ->label('Tamaño')
                    ->options([
                        'Small' => 'Pequeño (S)',
                        'Medium' => 'Mediano (M)',
                        'Large' => 'Grande (L)',
                        'X-Large' => 'Extra Grande (XL)',
                        'ND' => 'No Definido (ND)',
                    ])
                    ->multiple(),
                    
                SelectFilter::make('activo')
                    ->label('Estado')
                    ->options([
                        true => 'Activo',
                        false => 'Inactivo',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('recalcular_precios')
                    ->label('Recalcular Precios')
                    ->icon('heroicon-o-calculator')
                    ->color('warning')
                    ->action(function (Producto $record) {
                        $precioCompraAnterior = $record->precio_compra_referencia;
                        $precioVentaAnterior = $record->precio_venta_sugerido;
                        
                        $record->actualizarPrecioReferenciaAutomatico();
                        $record->actualizarPrecioVentaAutomatico();
                        $record->refresh();
                        
                        $cambiosCompra = $record->precio_compra_referencia != $precioCompraAnterior;
                        $cambiosVenta = $record->precio_venta_sugerido != $precioVentaAnterior;
                        
                        if ($cambiosCompra || $cambiosVenta) {
                            $mensaje = "Precios actualizados:\n";
                            if ($cambiosCompra) {
                                $mensaje .= "• Compra: $" . number_format($precioCompraAnterior, 2) . " → $" . number_format($record->precio_compra_referencia, 2) . "\n";
                            }
                            if ($cambiosVenta) {
                                $mensaje .= "• Venta: $" . number_format($precioVentaAnterior, 2) . " → $" . number_format($record->precio_venta_sugerido, 2);
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Precios actualizados')
                                ->body($mensaje)
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Sin cambios')
                                ->body('Los precios no requieren actualización')
                                ->info()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Recalcular Precios de Compra y Venta')
                    ->modalDescription('¿Estás seguro de que quieres recalcular los precios basados en el promedio de compras y ventas del año actual?'),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('recalcular_precios_masivo')
                        ->label('Recalcular Precios (Compra y Venta)')
                        ->icon('heroicon-o-calculator')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $actualizadosCompra = 0;
                            $actualizadosVenta = 0;
                            
                            foreach ($records as $record) {
                                $precioCompraAnterior = $record->precio_compra_referencia;
                                $precioVentaAnterior = $record->precio_venta_sugerido;
                                
                                $record->actualizarPrecioReferenciaAutomatico();
                                $record->actualizarPrecioVentaAutomatico();
                                $record->refresh();
                                
                                if ($record->precio_compra_referencia != $precioCompraAnterior) {
                                    $actualizadosCompra++;
                                }
                                if ($record->precio_venta_sugerido != $precioVentaAnterior) {
                                    $actualizadosVenta++;
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Recalculo completado')
                                ->body("Precios actualizados: {$actualizadosCompra} compras y {$actualizadosVenta} ventas de {$records->count()} productos")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Recalcular Precios de Compra y Venta')
                        ->modalDescription('¿Estás seguro de que quieres recalcular los precios de compra y venta de los productos seleccionados?'),
                    DeleteBulkAction::make()
                        ->label('Eliminar Seleccionados'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\ProductosOverviewWidget::class,
            Widgets\ProductosStockChart::class,
            Widgets\ProductosGrupoChart::class,
            Widgets\InventarioValorChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
            'dashboard' => Pages\ProductosDashboard::route('/dashboard'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}
