<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductoResource\Pages;
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

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $navigationLabel = 'Productos';
    
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
                                    ->label('Precio Compra Referencia')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix(CurrencyHelper::getCurrencySymbol())
                                    ->helperText('Precio promedio de compra en ' . CurrencyHelper::getCurrency()),
                                    
                                TextInput::make('precio_venta_sugerido')
                                    ->label('Precio Venta Sugerido')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix(CurrencyHelper::getCurrencySymbol())
                                    ->helperText('Precio sugerido de venta en ' . CurrencyHelper::getCurrency()),
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
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('precio_venta_sugerido')
                    ->label('P. Venta')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('margen_ganancia')
                    ->label('Margen %')
                    ->state(function ($record) {
                        if ($record->precio_compra_referencia && $record->precio_venta_sugerido && $record->precio_compra_referencia > 0) {
                            $margen = (($record->precio_venta_sugerido - $record->precio_compra_referencia) / $record->precio_compra_referencia) * 100;
                            return number_format($margen, 1) . '%';
                        }
                        return 'N/A';
                    })
                    ->color(function ($record) {
                        if ($record->precio_compra_referencia && $record->precio_venta_sugerido && $record->precio_compra_referencia > 0) {
                            $margen = (($record->precio_venta_sugerido - $record->precio_compra_referencia) / $record->precio_compra_referencia) * 100;
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}
