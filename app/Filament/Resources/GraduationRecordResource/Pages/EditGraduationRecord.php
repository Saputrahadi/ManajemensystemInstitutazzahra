<?php

namespace App\Filament\Resources\GraduationRecordResource\Pages;

use App\Filament\Resources\GraduationRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGraduationRecord extends EditRecord
{
    protected static string $resource = GraduationRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
