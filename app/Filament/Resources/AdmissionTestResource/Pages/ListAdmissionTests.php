<?php

namespace App\Filament\Resources\AdmissionTestResource\Pages;

use App\Filament\Resources\AdmissionTestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdmissionTests extends ListRecords
{
    protected static string $resource = AdmissionTestResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
