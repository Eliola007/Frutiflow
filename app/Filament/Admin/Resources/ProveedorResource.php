<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProveedorResource\Pages;
use App\Models\Proveedor;
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
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;

class ProveedorResource extends Resource
{
    protected static ?string $model = Proveedor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    
    protected static ?string $navigationLabel = 'Proveedores';
    
    protected static ?string $modelLabel = 'Proveedor';
    
    protected static ?string $pluralModelLabel = 'Proveedores';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationGroup = 'Gestión de Proveedores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Básica')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Nombre/Razón Social')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ej: Distribuidora de Frutas SA')
                                    ->columnSpan(1),
                                    
                                TextInput::make('rfc')
                                    ->label('RFC')
                                    ->required()
                                    ->length(13)
                                    ->placeholder('ABCD123456EFG')
                                    ->rule('regex:/^[A-Z0-9]{13}$/')
                                    ->helperText('Debe contener exactamente 13 caracteres alfanuméricos')
                                    ->columnSpan(1),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                TextInput::make('telefono')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder('Ej: +52 55 1234 5678'),
                                    
                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->maxLength(255),
                            ]),
                            
                        TextInput::make('contacto_principal')
                            ->label('Contacto Principal')
                            ->maxLength(255)
                            ->placeholder('Nombre del contacto principal'),
                            
                        Textarea::make('direccion')
                            ->label('Dirección')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('Dirección completa del proveedor'),
                            
                        Grid::make(2)
                            ->schema([
                                TextInput::make('ciudad')
                                    ->label('Ciudad')
                                    ->maxLength(255),
                                    
                                Toggle::make('activo')
                                    ->label('Activo')
                                    ->default(true),
                            ]),
                    ]),
                    
                Section::make('Control de Crédito')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('limite_credito')
                                    ->label('Límite de Crédito')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix(CurrencyHelper::getCurrencySymbol())
                                    ->default(0)
                                    ->helperText('Máximo crédito permitido en ' . CurrencyHelper::getCurrency()),
                                    
                                TextInput::make('dias_credito')
                                    ->label('Días de Crédito')
                                    ->numeric()
                                    ->default(30)
                                    ->minValue(1)
                                    ->maxValue(365)
                                    ->suffix('días')
                                    ->helperText('Días máximos para pagar'),
                                    
                                Select::make('estado_credito')
                                    ->label('Estado de Crédito')
                                    ->options([
                                        'activo' => 'Activo',
                                        'suspendido' => 'Suspendido',
                                        'bloqueado' => 'Bloqueado',
                                    ])
                                    ->default('activo')
                                    ->native(false),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                TextInput::make('saldo_pendiente')
                                    ->label('Saldo Pendiente')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix(CurrencyHelper::getCurrencySymbol())
                                    ->default(0)
                                    ->disabled()
                                    ->helperText('Se actualiza automáticamente'),
                                    
                                TextInput::make('descuento_especial')
                                    ->label('Descuento Especial (%)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                    
                TextColumn::make('rfc')
                    ->label('RFC')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                    
                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('contacto_principal')
                    ->label('Contacto')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('limite_credito')
                    ->label('Límite Crédito')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable(),
                    
                TextColumn::make('saldo_pendiente')
                    ->label('Saldo Pendiente')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                    
                TextColumn::make('porcentaje_credito_usado')
                    ->label('% Crédito')
                    ->formatStateUsing(fn ($state) => number_format($state, 1) . '%')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 90 => 'danger',
                        $state >= 70 => 'warning',
                        default => 'success',
                    }),
                    
                BadgeColumn::make('estado_credito')
                    ->label('Estado Crédito')
                    ->colors([
                        'success' => 'activo',
                        'warning' => 'suspendido',
                        'danger' => 'bloqueado',
                    ]),
                    
                TextColumn::make('activo')
                    ->label('Activo')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo'),
                    
                TextColumn::make('created_at')
                    ->label('Fecha Registro')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado_credito')
                    ->label('Estado de Crédito')
                    ->options([
                        'activo' => 'Activo',
                        'suspendido' => 'Suspendido',
                        'bloqueado' => 'Bloqueado',
                    ]),
                    
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
    
    public static function getWidgets(): array
    {
        return [
            ProveedorResource\Widgets\EstadisticasProveedoresWidget::class,
            ProveedorResource\Widgets\ProveedoresMayorDeudaWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProveedors::route('/'),
            'create' => Pages\CreateProveedor::route('/create'),
            'edit' => Pages\EditProveedor::route('/{record}/edit'),
        ];
    }
}
