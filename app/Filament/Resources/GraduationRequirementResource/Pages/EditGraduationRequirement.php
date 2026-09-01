<?php

namespace App\Filament\Resources\GraduationRequirementResource\Pages;

use App\Filament\Resources\GraduationRequirementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGraduationRequirement extends EditRecord
{
    protected static string $resource = GraduationRequirementResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
