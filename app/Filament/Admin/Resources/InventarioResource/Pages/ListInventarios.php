<?php

namespace App\Filament\Admin\Resources\InventarioResource\Pages;

use App\Filament\Admin\Resources\InventarioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInventarios extends ListRecords
{
    protected static string $resource = InventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('ajustes')
                ->label('⚖️ Ajustes')
                ->color('warning')
                ->url(static::getResource()::getUrl('ajustes'))
                ->icon('heroicon-o-adjustments-horizontal'),
            Actions\Action::make('estadisticas')
                ->label('📊 Estadísticas')
                ->color('info')
                ->url(static::getResource()::getUrl('estadisticas'))
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}
