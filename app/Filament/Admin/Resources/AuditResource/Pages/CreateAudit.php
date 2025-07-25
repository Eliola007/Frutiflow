<?php

namespace App\Filament\Admin\Resources\AuditResource\Pages;

use App\Filament\Admin\Resources\AuditResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAudit extends CreateRecord
{
    protected static string $resource = AuditResource::class;
}
