<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PagoClienteResource\Pages;
use App\Models\PagoCliente;
use App\Models\Cliente;
use App\Models\Venta;
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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Auth;

class PagoClienteResource extends Resource
{
    protected static ?string $model = PagoCliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static ?string $navigationLabel = 'Pagos de Clientes';
    
    protected static ?string $modelLabel = 'Pago';
    
    protected static ?string $pluralModelLabel = 'Pagos de Clientes';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Pago')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('cliente_id')
                                    ->label('Cliente')
                                    ->relationship('cliente', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $cliente = Cliente::find($state);
                                            if ($cliente && $cliente->saldo_pendiente > 0) {
                                                $set('monto', $cliente->saldo_pendiente);
                                            }
                                        }
                                    }),
                                    
                                Select::make('venta_id')
                                    ->label('Venta Relacionada (Opcional)')
                                    ->relationship('venta', 'id')
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Seleccionar si el pago es específico para una venta'),
                            ]),
                            
                        Grid::make(3)
                            ->schema([
                                TextInput::make('monto')
                                    ->label('Monto del Pago')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix(CurrencyHelper::getCurrencySymbol())
                                    ->required()
                                    ->helperText('Monto en ' . CurrencyHelper::getCurrency()),
                                    
                                Select::make('tipo_pago')
                                    ->label('Tipo de Pago')
                                    ->options([
                                        'abono' => 'Abono Parcial',
                                        'pago_completo' => 'Pago Completo',
                                        'ajuste' => 'Ajuste',
                                    ])
                                    ->default('abono')
                                    ->required()
                                    ->native(false),
                                    
                                Select::make('metodo_pago')
                                    ->label('Método de Pago')
                                    ->options([
                                        'efectivo' => 'Efectivo',
                                        'transferencia' => 'Transferencia',
                                        'cheque' => 'Cheque',
                                        'tarjeta' => 'Tarjeta',
                                    ])
                                    ->default('efectivo')
                                    ->required()
                                    ->native(false),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                TextInput::make('referencia')
                                    ->label('Referencia')
                                    ->maxLength(100)
                                    ->placeholder('Número de cheque, referencia de transferencia, etc.'),
                                    
                                DatePicker::make('fecha_pago')
                                    ->label('Fecha del Pago')
                                    ->default(now())
                                    ->required(),
                            ]),
                            
                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Observaciones adicionales sobre el pago'),
                            
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => Auth::id()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                    
                TextColumn::make('monto')
                    ->label('Monto')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable()
                    ->weight(FontWeight::Bold),
                    
                TextColumn::make('tipo_pago')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'abono' => 'Abono Parcial',
                        'pago_completo' => 'Pago Completo',
                        'ajuste' => 'Ajuste',
                        default => $state
                    })
                    ->color(fn ($state) => match($state) {
                        'abono' => 'warning',
                        'pago_completo' => 'success',
                        'ajuste' => 'info',
                        default => 'gray'
                    }),
                    
                TextColumn::make('metodo_pago')
                    ->label('Método')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'efectivo' => 'Efectivo',
                        'transferencia' => 'Transferencia',
                        'cheque' => 'Cheque',
                        'tarjeta' => 'Tarjeta',
                        default => $state
                    })
                    ->color('primary'),
                    
                TextColumn::make('referencia')
                    ->label('Referencia')
                    ->limit(20)
                    ->toggleable(),
                    
                TextColumn::make('fecha_pago')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                TextColumn::make('usuario.name')
                    ->label('Registrado por')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('venta_id')
                    ->label('Venta #')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('observaciones')
                    ->label('Observaciones')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo_pago')
                    ->label('Tipo de Pago')
                    ->options([
                        'abono' => 'Abono Parcial',
                        'pago_completo' => 'Pago Completo',
                        'ajuste' => 'Ajuste',
                    ]),
                    
                SelectFilter::make('metodo_pago')
                    ->label('Método de Pago')
                    ->options([
                        'efectivo' => 'Efectivo',
                        'transferencia' => 'Transferencia',
                        'cheque' => 'Cheque',
                        'tarjeta' => 'Tarjeta',
                    ]),
                    
                Tables\Filters\Filter::make('fecha_pago')
                    ->form([
                        DatePicker::make('fecha_desde')->label('Desde'),
                        DatePicker::make('fecha_hasta')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['fecha_desde'], fn ($q) => $q->where('fecha_pago', '>=', $data['fecha_desde']))
                            ->when($data['fecha_hasta'], fn ($q) => $q->where('fecha_pago', '<=', $data['fecha_hasta']));
                    }),
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
            ->defaultSort('fecha_pago', 'desc');
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
            PagoClienteResource\Widgets\EvolucionPagosChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPagoClientes::route('/'),
            'create' => Pages\CreatePagoCliente::route('/create'),
            'edit' => Pages\EditPagoCliente::route('/{record}/edit'),
        ];
    }
}
