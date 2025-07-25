<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AuditResource\Pages;
use App\Filament\Admin\Resources\AuditResource\RelationManagers;
use OwenIt\Auditing\Models\Audit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = 'Auditoría';
    
    protected static ?string $navigationGroup = 'Administración';
    
    protected static ?int $navigationSort = 3;
    
    protected static ?string $modelLabel = 'Registro de Auditoría';
    
    protected static ?string $pluralModelLabel = 'Auditoría';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Los registros de auditoría son de solo lectura
                Forms\Components\Placeholder::make('info')
                    ->label('')
                    ->content('Los registros de auditoría son de solo lectura y se generan automáticamente por el sistema.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha/Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->default('Sistema')
                    ->searchable(),
                    
                Tables\Columns\BadgeColumn::make('event')
                    ->label('Evento')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Creado',
                        'updated' => 'Actualizado', 
                        'deleted' => 'Eliminado',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('Modelo')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->badge(),
                    
                Tables\Columns\TextColumn::make('auditable_id')
                    ->label('ID Registro'),
                    
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Navegador')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label('Evento')
                    ->options([
                        'created' => 'Creado',
                        'updated' => 'Actualizado',
                        'deleted' => 'Eliminado',
                    ]),
                    
                Tables\Filters\SelectFilter::make('auditable_type')
                    ->label('Modelo')
                    ->options([
                        'App\Models\User' => 'Usuario',
                        'App\Models\Producto' => 'Producto',
                        'App\Models\Venta' => 'Venta',
                        'App\Models\Compra' => 'Compra',
                        'App\Models\Cliente' => 'Cliente',
                        'App\Models\Proveedor' => 'Proveedor',
                    ]),
                    
                Tables\Filters\Filter::make('today')
                    ->label('Hoy')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today()))
                    ->toggle(),
                    
                Tables\Filters\Filter::make('this_week')
                    ->label('Esta Semana')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Detalles de Auditoría')
                    ->modalContent(function ($record) {
                        return view('filament.audit-details', compact('record'));
                    }),
            ])
            ->bulkActions([
                // Sin acciones masivas para auditoría (solo lectura)
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
            'index' => Pages\ListAudits::route('/'),
            // Solo lectura - sin crear/editar
        ];
    }
}
