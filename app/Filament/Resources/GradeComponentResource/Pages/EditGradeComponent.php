<?php

namespace App\Filament\Resources\GradeComponentResource\Pages;

use App\Filament\Resources\GradeComponentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGradeComponent extends EditRecord
{
    protected static string $resource = GradeComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
