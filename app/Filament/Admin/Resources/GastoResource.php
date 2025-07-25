<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GastoResource\Pages;
use App\Filament\Admin\Resources\GastoResource\Widgets;
use App\Models\Gasto;
use App\Models\ConceptoGasto;
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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;

class GastoResource extends Resource
{
    protected static ?string $model = Gasto::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    
    protected static ?string $navigationLabel = 'Gastos';
    
    protected static ?string $navigationGroup = 'Movimientos';
    
    protected static ?int $navigationSort = 3;
    
    protected static ?string $modelLabel = 'Gasto';
    
    protected static ?string $pluralModelLabel = 'Gastos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Gasto')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('numero_comprobante')
                                    ->label('Número de Comprobante')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50),
                                    
                                Hidden::make('numero_interno')
                                    ->default(fn () => (new Gasto())->generarNumeroInterno()),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('concepto_gasto_id')
                                    ->label('Concepto de Gasto')
                                    ->relationship('conceptoGasto', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->grupo ? "{$record->grupo} - {$record->nombre}" : $record->nombre)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $concepto = ConceptoGasto::find($state);
                                            if ($concepto) {
                                                $set('categoria', $concepto->categoria);
                                            }
                                        }
                                    })
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->label('Nombre del Concepto')
                                            ->required()
                                            ->maxLength(100),
                                        
                                        Select::make('categoria')
                                            ->label('Categoría')
                                            ->required()
                                            ->options([
                                                'operativo' => 'Operativo',
                                                'logistica' => 'Logística',
                                                'personal' => 'Personal',
                                                'servicios' => 'Servicios',
                                                'mantenimiento' => 'Mantenimiento',
                                                'sanitario' => 'Sanitario',
                                                'administrativo' => 'Administrativo',
                                                'comisiones' => 'Comisiones',
                                                'otros' => 'Otros'
                                            ]),
                                        
                                        TextInput::make('grupo')
                                            ->label('Grupo (opcional)')
                                            ->maxLength(50),
                                        
                                        Toggle::make('es_recurrente')
                                            ->label('¿Es recurrente?')
                                            ->default(false),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        return ConceptoGasto::create($data)->id;
                                    }),
                                    
                                Select::make('categoria')
                                    ->label('Categoría')
                                    ->required()
                                    ->options(fn () => \App\Models\Gasto::getCategoriasDisponibles())
                                    ->native(false)
                                    ->allowHtml()
                                    ->createOptionForm([
                                        TextInput::make('categoria_nueva')
                                            ->label('Nueva Categoría')
                                            ->required()
                                            ->maxLength(50)
                                            ->helperText('Ingresa el nombre de la nueva categoría (ej: Marketing, Seguros, etc.)')
                                            ->placeholder('Ej: Marketing')
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        // Limpiar y formatear la nueva categoría
                                        $nuevaCategoria = trim($data['categoria_nueva']);
                                        $nuevaCategoria = strtolower($nuevaCategoria);
                                        $nuevaCategoria = preg_replace('/[^a-z0-9\s]/', '', $nuevaCategoria); // Eliminar caracteres especiales
                                        $nuevaCategoria = preg_replace('/\s+/', '_', $nuevaCategoria); // Convertir espacios a guiones bajos
                                        return $nuevaCategoria;
                                    }),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('proveedor_id')
                                    ->label('Proveedor/Empresa')
                                    ->relationship('proveedor', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->label('Nombre del Proveedor')
                                            ->required()
                                            ->maxLength(100),
                                        
                                        TextInput::make('telefono')
                                            ->label('Teléfono')
                                            ->tel()
                                            ->maxLength(15),
                                        
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->maxLength(100),
                                        
                                        Textarea::make('direccion')
                                            ->label('Dirección')
                                            ->maxLength(255)
                                            ->rows(2),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        return Proveedor::create($data)->id;
                                    }),

                                Select::make('producto_id')
                                    ->label('Producto (opcional)')
                                    ->relationship('producto', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Seleccionar producto relacionado'),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                TextInput::make('monto')
                                    ->label('Monto')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->minValue(0),
                                    
                                DatePicker::make('fecha_gasto')
                                    ->label('Fecha del Gasto')
                                    ->required()
                                    ->default(now())
                                    ->native(false),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('es_recurrente')
                                    ->label('¿Es un gasto recurrente?')
                                    ->default(false)
                                    ->reactive(),

                                Select::make('periodo_recurrencia')
                                    ->label('Periodo de Recurrencia')
                                    ->options([
                                        'semanal' => 'Semanal',
                                        'quincenal' => 'Quincenal',
                                        'mensual' => 'Mensual',
                                        'bimestral' => 'Bimestral',
                                        'trimestral' => 'Trimestral',
                                        'anual' => 'Anual'
                                    ])
                                    ->visible(fn (callable $get) => $get('es_recurrente'))
                                    ->required(fn (callable $get) => $get('es_recurrente')),
                            ]),
                            
                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->required()
                            ->maxLength(500)
                            ->rows(3),
                            
                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->maxLength(500)
                            ->rows(2),
                            
                        FileUpload::make('archivo_soporte')
                            ->label('Archivo de Soporte')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(5120) // 5MB
                            ->directory('gastos/soportes')
                            ->visibility('private'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero_comprobante')
                    ->label('N° Comprobante')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('numero_interno')
                    ->label('N° Interno')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('conceptoGasto.nombre')
                    ->label('Concepto')
                    ->searchable()
                    ->limit(30)
                    ->formatStateUsing(function ($record) {
                        $concepto = $record->conceptoGasto;
                        if (!$concepto) return 'N/A';
                        return $concepto->grupo ? "{$concepto->grupo} - {$concepto->nombre}" : $concepto->nombre;
                    })
                    ->tooltip(function (TextColumn $column): ?string {
                        $record = $column->getRecord();
                        $concepto = $record->conceptoGasto;
                        if (!$concepto) return null;
                        $fullName = $concepto->grupo ? "{$concepto->grupo} - {$concepto->nombre}" : $concepto->nombre;
                        if (strlen($fullName) <= 30) {
                            return null;
                        }
                        return $fullName;
                    }),
                    
                TextColumn::make('categoria')
                    ->label('Categoría')
                    ->badge()
                    ->formatStateUsing(function (string $state): string {
                        $categorias = [
                            'operativo' => 'Operativo',
                            'logistica' => 'Logística',
                            'personal' => 'Personal',
                            'servicios' => 'Servicios',
                            'mantenimiento' => 'Mantenimiento',
                            'sanitario' => 'Sanitario',
                            'administrativo' => 'Administrativo',
                            'comisiones' => 'Comisiones',
                            'otros' => 'Otros'
                        ];
                        return $categorias[$state] ?? ucfirst(str_replace('_', ' ', $state));
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'operativo' => 'primary',
                        'logistica' => 'warning',
                        'personal' => 'success',
                        'servicios' => 'info',
                        'mantenimiento' => 'gray',
                        'sanitario' => 'danger',
                        'administrativo' => 'secondary',
                        'comisiones' => 'indigo',
                        'otros' => 'gray',
                        default => 'slate', // Color por defecto para categorías personalizadas
                    }),

                TextColumn::make('proveedor.nombre')
                    ->label('Proveedor')
                    ->searchable()
                    ->limit(25)
                    ->toggleable(),

                TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->searchable()
                    ->limit(20)
                    ->toggleable()
                    ->placeholder('N/A'),
                    
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(),
                    
                TextColumn::make('monto')
                    ->label('Monto')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('danger'),
                    
                TextColumn::make('fecha_gasto')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('es_recurrente')
                    ->label('Recurrente')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sí' : 'No')
                    ->toggleable(),
                    
                TextColumn::make('usuario.name')
                    ->label('Registrado por')
                    ->toggleable(),
                    
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha_gasto', 'desc')
            ->filters([
                SelectFilter::make('categoria')
                    ->label('Categoría')
                    ->options(fn () => \App\Models\Gasto::getCategoriasDisponibles()),

                SelectFilter::make('concepto_gasto_id')
                    ->label('Concepto')
                    ->relationship('conceptoGasto', 'nombre')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('proveedor_id')
                    ->label('Proveedor')
                    ->relationship('proveedor', 'nombre')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('es_recurrente')
                    ->label('Recurrente')
                    ->options([
                        1 => 'Sí',
                        0 => 'No'
                    ]),

                Filter::make('fecha_gasto')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_gasto', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_gasto', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListGastos::route('/'),
            'create' => Pages\CreateGasto::route('/create'),
            'view' => Pages\ViewGasto::route('/{record}'),
            'edit' => Pages\EditGasto::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\GastosWidget::class,
        ];
    }
}
