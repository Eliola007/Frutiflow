<?php

namespace App\Filament\Admin\Resources\PagoClienteResource\Pages;

use App\Filament\Admin\Resources\PagoClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPagoClientes extends ListRecords
{
    protected static string $resource = PagoClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            PagoClienteResource\Widgets\EvolucionPagosChart::class,
        ];
    }
}
