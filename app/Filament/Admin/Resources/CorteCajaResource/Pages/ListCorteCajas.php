<?php

namespace App\Filament\Admin\Resources\CorteCajaResource\Pages;

use App\Filament\Admin\Resources\CorteCajaResource;
use Filament\Resources\Pages\ListRecords;

class ListCorteCajas extends ListRecords
{
    protected static string $resource = CorteCajaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->label('Nuevo Corte de Caja'),
        ];
    }
}
