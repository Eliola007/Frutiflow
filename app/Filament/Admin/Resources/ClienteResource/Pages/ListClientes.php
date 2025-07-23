<?php

namespace App\Filament\Admin\Resources\ClienteResource\Pages;

use App\Filament\Admin\Resources\ClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientes extends ListRecords
{
    protected static string $resource = ClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            ClienteResource\Widgets\EstadisticasCreditoWidget::class,
        ];
    }
    
    protected function getFooterWidgets(): array
    {
        return [
            ClienteResource\Widgets\ClientesMayorDeudaWidget::class,
            ClienteResource\Widgets\PagosVencidosWidget::class,
        ];
    }
}
