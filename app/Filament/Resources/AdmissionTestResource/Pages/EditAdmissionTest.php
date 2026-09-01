<?php

namespace App\Filament\Resources\AdmissionTestResource\Pages;

use App\Filament\Resources\AdmissionTestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdmissionTest extends EditRecord
{
    protected static string $resource = AdmissionTestResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
