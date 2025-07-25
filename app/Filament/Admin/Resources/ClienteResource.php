<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ClienteResource\Pages;
use App\Models\Cliente;
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

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Clientes';
    
    protected static ?string $navigationGroup = 'Gestión de Clientes';
    
    protected static ?string $modelLabel = 'Cliente';
    
    protected static ?string $pluralModelLabel = 'Clientes';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Básica')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Nombre Completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ej: Juan Pérez García')
                                    ->columnSpan(2),
                                    
                                TextInput::make('telefono')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(20)
                                    ->placeholder('Ej: +52 55 1234 5678'),
                            ]),
                            
                        Grid::make(1)
                            ->schema([
                                TextInput::make('rfc')
                                    ->label('RFC (Opcional)')
                                    ->length(13)
                                    ->placeholder('Ej: XAXX010101000')
                                    ->helperText('RFC de 13 caracteres (4 letras + 6 números + 3 alfanuméricos)')
                                    ->rule('regex:/^[A-Z]{4}[0-9]{6}[A-Z0-9]{3}$/')
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, \Closure $fail) {
                                                if ($value && !\App\Models\Cliente::validarRFC($value)) {
                                                    $fail('El RFC no tiene un formato válido o contiene una fecha inválida.');
                                                }
                                            };
                                        },
                                    ])
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Este RFC ya está registrado en el sistema.',
                                        'length' => 'El RFC debe tener exactamente 13 caracteres.',
                                        'regex' => 'El RFC debe tener el formato correcto: 4 letras + 6 números + 3 alfanuméricos.',
                                    ])
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $set('rfc', strtoupper($state));
                                        }
                                    }),
                            ]),
                            
                        Textarea::make('direccion')
                            ->label('Dirección')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('Dirección completa del cliente'),
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
                                TextInput::make('descuento_especial')
                                    ->label('Descuento Especial')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->helperText('Descuento especial para este cliente'),
                                    
                                TextInput::make('saldo_pendiente')
                                    ->label('Saldo Pendiente')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix(CurrencyHelper::getCurrencySymbol())
                                    ->default(0)
                                    ->disabled()
                                    ->helperText('Se actualiza automáticamente con ventas y pagos'),
                            ]),
                    ]),
                    
                Section::make('Estado del Cliente')
                    ->schema([
                        Toggle::make('activo')
                            ->label('Cliente Activo')
                            ->helperText('Desactivar para ocultar el cliente sin eliminarlo')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                    
                TextColumn::make('rfc')
                    ->label('RFC')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight(FontWeight::Bold)
                    ->color('primary')
                    ->placeholder('Sin RFC'),
                    
                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),
                    
                TextColumn::make('direccion')
                    ->label('Dirección')
                    ->limit(30)
                    ->toggleable(),
                    
                TextColumn::make('limite_credito')
                    ->label('Límite Crédito')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable(),
                    
                TextColumn::make('saldo_pendiente')
                    ->label('Saldo Pendiente')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                    ->weight(FontWeight::Bold)
                    ->sortable(),
                    
                TextColumn::make('credito_disponible')
                    ->label('Crédito Disponible')
                    ->state(fn ($record) => $record->credito_disponible)
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->color(fn ($record) => $record->credito_disponible > 0 ? 'success' : 'danger'),
                    
                TextColumn::make('credito_utilizado_porcentaje')
                    ->label('Utilizado %')
                    ->state(fn ($record) => number_format($record->credito_utilizado_porcentaje, 1) . '%')
                    ->color(function ($record) {
                        $porcentaje = $record->credito_utilizado_porcentaje;
                        return $porcentaje >= 90 ? 'danger' : ($porcentaje >= 70 ? 'warning' : 'success');
                    }),
                    
                TextColumn::make('estado_credito')
                    ->label('Estado Crédito')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'activo' => 'Activo',
                        'suspendido' => 'Suspendido',
                        'bloqueado' => 'Bloqueado',
                        default => $state
                    })
                    ->color(fn ($state) => match($state) {
                        'activo' => 'success',
                        'suspendido' => 'warning',
                        'bloqueado' => 'danger',
                        default => 'gray'
                    }),
                    
                TextColumn::make('dias_credito')
                    ->label('Días Crédito')
                    ->suffix(' días')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('descuento_especial')
                    ->label('Descuento')
                    ->formatStateUsing(fn ($state) => $state ? $state . '%' : 'Sin descuento')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('activo')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Activo' : 'Inactivo')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                    
                TextColumn::make('ultima_compra')
                    ->label('Última Compra')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
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
                    
                Tables\Filters\Filter::make('con_deuda')
                    ->label('Con Deuda')
                    ->query(fn ($query) => $query->where('saldo_pendiente', '>', 0)),
                    
                Tables\Filters\Filter::make('credito_alto')
                    ->label('Crédito > 90%')
                    ->query(fn ($query) => $query->whereRaw('(saldo_pendiente / limite_credito) * 100 >= 90')),
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
            ClienteResource\Widgets\EstadisticasCreditoWidget::class,
            ClienteResource\Widgets\ClientesMayorDeudaWidget::class,
            ClienteResource\Widgets\PagosVencidosWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}
