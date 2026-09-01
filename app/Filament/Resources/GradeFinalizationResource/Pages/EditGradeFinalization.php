<?php

namespace App\Filament\Resources\GradeFinalizationResource\Pages;

use App\Filament\Resources\GradeFinalizationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGradeFinalization extends EditRecord
{
    protected static string $resource = GradeFinalizationResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
