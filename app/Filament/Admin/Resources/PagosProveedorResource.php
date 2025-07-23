<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PagosProveedorResource\Pages;
use App\Filament\Admin\Resources\PagosProveedorResource\RelationManagers;
use App\Filament\Admin\Resources\PagosProveedorResource\Widgets;
use App\Helpers\CurrencyHelper;
use App\Models\PagoProveedor;
use App\Models\Proveedor;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;

class PagosProveedorResource extends Resource
{
    protected static ?string $model = PagoProveedor::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationLabel = 'Pagos a Proveedores';
    
    protected static ?string $navigationGroup = 'Gestión de Proveedores';
    
    protected static ?string $modelLabel = 'Pago a Proveedor';
    
    protected static ?string $pluralModelLabel = 'Pagos a Proveedores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Pago')
                    ->schema([
                        Select::make('proveedor_id')
                            ->label('Proveedor')
                            ->options(Proveedor::where('activo', true)->pluck('nombre', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('monto')
                            ->label('Monto')
                            ->numeric()
                            ->required()
                            ->prefix(CurrencyHelper::getCurrencySymbol())
                            ->step(0.01)
                            ->minValue(0.01)
                            ->helperText('Monto en ' . CurrencyHelper::getCurrency()),

                        Select::make('tipo_pago')
                            ->label('Tipo de Pago')
                            ->options([
                                'pago' => 'Pago',
                                'anticipo' => 'Anticipo',
                                'abono' => 'Abono',
                            ])
                            ->required()
                            ->default('pago'),

                        Select::make('metodo_pago')
                            ->label('Método de Pago')
                            ->options([
                                'efectivo' => 'Efectivo',
                                'transferencia' => 'Transferencia',
                                'cheque' => 'Cheque',
                                'tarjeta' => 'Tarjeta',
                            ])
                            ->required()
                            ->default('efectivo'),

                        DatePicker::make('fecha_pago')
                            ->label('Fecha de Pago')
                            ->required()
                            ->default(today()),
                    ])
                    ->columns(2),

                Section::make('Detalles Adicionales')
                    ->schema([
                        TextInput::make('referencia')
                            ->label('Referencia')
                            ->maxLength(255)
                            ->placeholder('Número de referencia, cheque, etc.'),

                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),

                        Select::make('user_id')
                            ->label('Usuario')
                            ->options(User::where('activo', true)->pluck('name', 'id'))
                            ->required()
                            ->default(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('proveedor.nombre')
                    ->label('Proveedor')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('tipo_pago')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pago' => 'success',
                        'anticipo' => 'warning',
                        'abono' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('metodo_pago')
                    ->label('Método')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('fecha_pago')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('referencia')
                    ->label('Referencia')
                    ->limit(20)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 20 ? $state : null;
                    }),

                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('proveedor_id')
                    ->label('Proveedor')
                    ->options(Proveedor::where('activo', true)->pluck('nombre', 'id')),

                Tables\Filters\SelectFilter::make('tipo_pago')
                    ->label('Tipo de Pago')
                    ->options([
                        'pago' => 'Pago',
                        'anticipo' => 'Anticipo',
                        'abono' => 'Abono',
                    ]),

                Tables\Filters\SelectFilter::make('metodo_pago')
                    ->label('Método de Pago')
                    ->options([
                        'efectivo' => 'Efectivo',
                        'transferencia' => 'Transferencia',
                        'cheque' => 'Cheque',
                        'tarjeta' => 'Tarjeta',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_pago', 'desc')
            ->paginated([10, 25, 50, 100]);
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
            Widgets\EvolucionPagosChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPagosProveedors::route('/'),
            'create' => Pages\CreatePagosProveedor::route('/create'),
            'edit' => Pages\EditPagosProveedor::route('/{record}/edit'),
        ];
    }
}
