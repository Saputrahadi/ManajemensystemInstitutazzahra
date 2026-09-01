<?php

namespace App\Filament\Resources\AdmissionPeriodResource\Pages;

use App\Filament\Resources\AdmissionPeriodResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdmissionPeriod extends EditRecord
{
    protected static string $resource = AdmissionPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
