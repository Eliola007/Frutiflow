<?php

namespace App\Filament\Admin\Resources\AuditResource\Pages;

use App\Filament\Admin\Resources\AuditResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAudits extends ListRecords
{
    protected static string $resource = AuditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
