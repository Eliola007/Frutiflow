<?php

namespace App\Filament\Admin\Resources\CompraResource\Pages;

use App\Filament\Admin\Resources\CompraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompra extends EditRecord
{
    protected static string $resource = CompraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
