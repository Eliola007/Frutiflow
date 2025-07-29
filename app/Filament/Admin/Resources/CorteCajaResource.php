<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CorteCajaResource\Pages;
use App\Models\CorteCaja;
use App\Models\User;
use App\Helpers\CurrencyHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CorteCajaResource extends Resource
{
    protected static ?string $model = CorteCaja::class;
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Cortes de Caja';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $modelLabel = 'Corte de Caja';
    protected static ?string $pluralModelLabel = 'Cortes de Caja';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Información del Corte')
                ->schema([
                    Grid::make(2)->schema([
                        DatePicker::make('fecha')
                            ->label('Fecha del corte')
                            ->default(now())
                            ->required()
                            ->unique(ignoreRecord: true),
                        Select::make('usuario_id')
                            ->label('Usuario responsable')
                            ->default(fn() => Auth::id())
                            ->options(User::all()->pluck('name', 'id'))
                            ->required()
                            ->disabled(),
                    ]),
                ]),
                
            Section::make('Efectivo')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('efectivo_inicial')
                            ->label('Efectivo inicial')
                            ->numeric()
                            ->step(0.01)
                            ->prefix(CurrencyHelper::getCurrencySymbol())
                            ->required(),
                        TextInput::make('efectivo_final')
                            ->label('Efectivo final (calculado)')
                            ->numeric()
                            ->step(0.01)
                            ->prefix(CurrencyHelper::getCurrencySymbol())
                            ->disabled()
                            ->dehydrated(),
                    ]),
                ]),
                
            Section::make('Totales (Calculados Automáticamente)')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('total_ventas')
                            ->label('Total ventas')
                            ->numeric()
                            ->step(0.01)
                            ->prefix(CurrencyHelper::getCurrencySymbol())
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('total_ingresos')
                            ->label('Total ingresos')
                            ->numeric()
                            ->step(0.01)
                            ->prefix(CurrencyHelper::getCurrencySymbol())
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('total_egresos')
                            ->label('Total egresos')
                            ->numeric()
                            ->step(0.01)
                            ->prefix(CurrencyHelper::getCurrencySymbol())
                            ->disabled()
                            ->dehydrated(),
                    ]),
                ]),
                
            Section::make('Configuración')
                ->schema([
                    Textarea::make('observaciones')
                        ->label('Observaciones')
                        ->rows(3)
                        ->placeholder('Notas adicionales sobre el corte de caja...'),
                    Toggle::make('editable')
                        ->label('Permitir edición posterior')
                        ->helperText('Solo el administrador puede editar cortes marcados como editables')
                        ->default(false)
                        ->visible(fn() => Auth::user()?->hasRole('admin')),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('fecha')
                ->label('Fecha')
                ->date('d/m/Y')
                ->sortable(),
            TextColumn::make('usuario.name')
                ->label('Usuario')
                ->sortable(),
            TextColumn::make('efectivo_inicial')
                ->label('Efectivo inicial')
                ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                ->sortable(),
            TextColumn::make('total_ventas')
                ->label('Ventas')
                ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                ->sortable(),
            TextColumn::make('total_ingresos')
                ->label('Ingresos')
                ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                ->sortable(),
            TextColumn::make('total_egresos')
                ->label('Egresos')
                ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                ->sortable(),
            TextColumn::make('efectivo_final')
                ->label('Efectivo final')
                ->money(CurrencyHelper::getCurrency(), locale: CurrencyHelper::getLocale())
                ->sortable()
                ->weight('bold'),
            BadgeColumn::make('editable')
                ->label('Estado')
                ->formatStateUsing(fn($state) => $state ? 'Editable' : 'Cerrado')
                ->color(fn($state) => $state ? 'warning' : 'success'),
        ])
        ->filters([
            Filter::make('fecha')
                ->form([
                    DatePicker::make('fecha_desde')->label('Desde'),
                    DatePicker::make('fecha_hasta')->label('Hasta'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['fecha_desde'], fn (Builder $query, $date): Builder => $query->whereDate('fecha', '>=', $date))
                        ->when($data['fecha_hasta'], fn (Builder $query, $date): Builder => $query->whereDate('fecha', '<=', $date));
                }),
            SelectFilter::make('usuario_id')
                ->label('Usuario')
                ->options(User::all()->pluck('name', 'id')),
        ])
        ->actions([
            Action::make('generar_pdf')
                ->label('PDF')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->action(fn(CorteCaja $record) => $record->generarPDF()),
            EditAction::make()
                ->label('Editar')
                ->visible(fn(CorteCaja $record) => 
                    Auth::user()?->hasRole('admin') && $record->editable
                ),
            DeleteAction::make()
                ->label('Eliminar')
                ->visible(fn() => Auth::user()?->hasRole('admin')),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => Auth::user()?->hasRole('admin')),
            ]),
        ])
        ->defaultSort('fecha', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCorteCajas::route('/'),
            'create' => Pages\CreateCorteCaja::route('/create'),
            'edit' => Pages\EditCorteCaja::route('/{record}/edit'),
        ];
    }
}
