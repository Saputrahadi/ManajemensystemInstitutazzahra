<?php

namespace App\Filament\Resources\DueDateExtensionResource\Pages;

use App\Filament\Resources\DueDateExtensionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDueDateExtension extends CreateRecord
{
    protected static string $resource = DueDateExtensionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['requested_by'] = auth()->id();

        return $data;
    }
}
