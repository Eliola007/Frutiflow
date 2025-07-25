<?php

namespace App\Filament\Admin\Resources\ProveedorResource\Pages;

use App\Filament\Admin\Resources\ProveedorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProveedors extends ListRecords
{
    protected static string $resource = ProveedorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            ProveedorResource\Widgets\EstadisticasProveedoresWidget::class,
        ];
    }
    
    protected function getFooterWidgets(): array
    {
        return [
            ProveedorResource\Widgets\ProveedoresMayorDeudaWidget::class,
        ];
    }
}
