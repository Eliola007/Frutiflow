<?php

namespace App\Filament\Admin\Resources\GastoResource\Pages;

use App\Filament\Admin\Resources\GastoResource;
use App\Filament\Admin\Resources\GastoResource\Widgets\GastosWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGastos extends ListRecords
{
    protected static string $resource = GastoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            GastosWidget::class,
        ];
    }
}
