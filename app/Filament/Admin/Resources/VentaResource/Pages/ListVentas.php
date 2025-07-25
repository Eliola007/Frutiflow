<?php

namespace App\Filament\Admin\Resources\VentaResource\Pages;

use App\Filament\Admin\Resources\VentaResource;
use App\Filament\Admin\Resources\VentaResource\Widgets\VentasWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVentas extends ListRecords
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('🛒 Nueva Venta')
                ->icon('heroicon-o-plus-circle')
                ->color('success'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            VentasWidget::class,
        ];
    }
}
