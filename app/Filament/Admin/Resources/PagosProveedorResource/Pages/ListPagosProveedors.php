<?php

namespace App\Filament\Admin\Resources\PagosProveedorResource\Pages;

use App\Filament\Admin\Resources\PagosProveedorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPagosProveedors extends ListRecords
{
    protected static string $resource = PagosProveedorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PagosProveedorResource\Widgets\EvolucionPagosChart::class,
        ];
    }
}
