<?php

namespace App\Filament\Admin\Resources\GastoResource\Pages;

use App\Filament\Admin\Resources\GastoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGasto extends ViewRecord
{
    protected static string $resource = GastoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
