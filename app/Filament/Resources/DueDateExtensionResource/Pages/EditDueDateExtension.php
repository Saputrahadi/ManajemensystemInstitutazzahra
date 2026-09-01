<?php

namespace App\Filament\Resources\DueDateExtensionResource\Pages;

use App\Filament\Resources\DueDateExtensionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDueDateExtension extends EditRecord
{
    protected static string $resource = DueDateExtensionResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
