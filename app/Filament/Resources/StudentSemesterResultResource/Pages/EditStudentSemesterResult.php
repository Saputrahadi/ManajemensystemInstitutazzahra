<?php

namespace App\Filament\Resources\StudentSemesterResultResource\Pages;

use App\Filament\Resources\StudentSemesterResultResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStudentSemesterResult extends EditRecord
{
    protected static string $resource = StudentSemesterResultResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
