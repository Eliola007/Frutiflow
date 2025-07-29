<?php

namespace App\Filament\Admin\Resources\CorteCajaResource\Pages;

use App\Filament\Admin\Resources\CorteCajaResource;
use Filament\Resources\Pages\EditRecord;

class EditCorteCaja extends EditRecord
{
    protected static string $resource = CorteCajaResource::class;

    protected function afterSave(): void
    {
        // Recalcular totales al editar
        $this->record->calcularTotalesDia();
        $this->record->save();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
