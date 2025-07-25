<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserRoleResource\Pages;
use App\Filament\Admin\Resources\UserRoleResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;

class UserRoleResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Usuarios y Roles';
    
    protected static ?string $navigationGroup = 'Administración';
    
    protected static ?int $navigationSort = 4;
    
    protected static ?string $modelLabel = 'Usuario';
    
    protected static ?string $pluralModelLabel = 'Usuarios y Roles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\Select::make('role_id')
                    ->label('Rol Tradicional')
                    ->relationship('role', 'nombre')
                    ->required(),
                    
                Forms\Components\CheckboxList::make('spatie_roles')
                    ->label('Roles de Spatie')
                    ->relationship('roles', 'name')
                    ->options(Role::all()->pluck('name', 'id'))
                    ->columns(2)
                    ->helperText('Selecciona los roles de Spatie para este usuario'),
                    
                Forms\Components\Toggle::make('activo')
                    ->label('Usuario Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('role.nombre')
                    ->label('Rol Tradicional')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles Spatie')
                    ->badge()
                    ->separator(',')
                    ->color('success'),
                    
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role_id')
                    ->label('Rol Tradicional')
                    ->relationship('role', 'nombre'),
                    
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Rol Spatie')
                    ->relationship('roles', 'name'),
                    
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Usuario Activo'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('assign_products')
                    ->label('Asignar Productos')
                    ->icon('heroicon-o-shopping-bag')
                    ->color('warning')
                    ->visible(fn ($record) => $record->hasRole('Socio Comercial'))
                    ->url(fn ($record) => route('permisos.asignar-productos', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUserRoles::route('/'),
            'create' => Pages\CreateUserRole::route('/create'),
            'edit' => Pages\EditUserRole::route('/{record}/edit'),
        ];
    }
}
