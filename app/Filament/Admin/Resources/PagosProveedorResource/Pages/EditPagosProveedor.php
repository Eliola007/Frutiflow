<?php

namespace App\Filament\Admin\Resources\PagosProveedorResource\Pages;

use App\Filament\Admin\Resources\PagosProveedorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPagosProveedor extends EditRecord
{
    protected static string $resource = PagosProveedorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
