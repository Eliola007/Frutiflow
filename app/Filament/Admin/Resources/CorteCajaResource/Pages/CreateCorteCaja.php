<?php

namespace App\Filament\Admin\Resources\CorteCajaResource\Pages;

use App\Filament\Admin\Resources\CorteCajaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCorteCaja extends CreateRecord
{
    protected static string $resource = CorteCajaResource::class;

    protected function afterCreate(): void
    {
        // Calcular totales automáticamente después de crear
        $this->record->calcularTotalesDia();
        $this->record->save();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
