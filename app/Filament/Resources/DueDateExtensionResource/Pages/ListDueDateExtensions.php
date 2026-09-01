<?php

namespace App\Filament\Resources\DueDateExtensionResource\Pages;

use App\Filament\Resources\DueDateExtensionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDueDateExtensions extends ListRecords
{
    protected static string $resource = DueDateExtensionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
