<?php

namespace App\Filament\Resources\AdmissionPathResource\Pages;

use App\Filament\Resources\AdmissionPathResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdmissionPaths extends ListRecords
{
    protected static string $resource = AdmissionPathResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
