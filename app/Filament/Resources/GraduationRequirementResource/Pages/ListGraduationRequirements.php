<?php

namespace App\Filament\Resources\GraduationRequirementResource\Pages;

use App\Filament\Resources\GraduationRequirementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGraduationRequirements extends ListRecords
{
    protected static string $resource = GraduationRequirementResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
