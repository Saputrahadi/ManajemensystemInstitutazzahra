<?php

namespace App\Filament\Resources\GradeChangeRequestResource\Pages;

use App\Filament\Resources\GradeChangeRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGradeChangeRequest extends EditRecord
{
    protected static string $resource = GradeChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
