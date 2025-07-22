<?php

namespace App\Filament\Admin\Resources\ProveedorResource\Pages;

use App\Filament\Admin\Resources\ProveedorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProveedor extends EditRecord
{
    protected static string $resource = ProveedorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
