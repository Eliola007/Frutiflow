<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GastoResource\Pages;
use App\Models\Gasto;
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
                                    
                                Select::make('categoria')
                                    ->label('Categoría')
                                    ->required()
                                    ->options([
                                        'servicios' => 'Servicios',
                                        'suministros' => 'Suministros',
                                        'transporte' => 'Transporte',
                                        'mantenimiento' => 'Mantenimiento',
                                        'publicidad' => 'Publicidad',
                                        'impuestos' => 'Impuestos',
                                        'seguros' => 'Seguros',
                                        'otros' => 'Otros'
                                    ])
                                    ->native(false),
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
                            
                        TextInput::make('proveedor_gasto')
                            ->label('Proveedor/Empresa')
                            ->maxLength(255),
                            
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
                    
                TextColumn::make('categoria')
                    ->label('Categoría')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'servicios' => 'primary',
                        'suministros' => 'success',
                        'transporte' => 'warning',
                        'mantenimiento' => 'info',
                        'publicidad' => 'secondary',
                        'impuestos' => 'danger',
                        'seguros' => 'gray',
                        default => 'gray',
                    }),
                    
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable(),
                    
                TextColumn::make('monto')
                    ->label('Monto')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('danger'),
                    
                TextColumn::make('proveedor_gasto')
                    ->label('Proveedor')
                    ->searchable()
                    ->toggleable(),
                    
                TextColumn::make('fecha_gasto')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                    
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
                    ->options([
                        'servicios' => 'Servicios',
                        'suministros' => 'Suministros',
                        'transporte' => 'Transporte',
                        'mantenimiento' => 'Mantenimiento',
                        'publicidad' => 'Publicidad',
                        'impuestos' => 'Impuestos',
                        'seguros' => 'Seguros',
                        'otros' => 'Otros'
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
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_gasto', '>=', $date),
                            )
                            ->when(
                                $data['fecha_hasta'],
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
}
