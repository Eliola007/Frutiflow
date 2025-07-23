<?php

namespace App\Filament\Admin\Resources\PagoClienteResource\Pages;

use App\Filament\Admin\Resources\PagoClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPagoCliente extends EditRecord
{
    protected static string $resource = PagoClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
