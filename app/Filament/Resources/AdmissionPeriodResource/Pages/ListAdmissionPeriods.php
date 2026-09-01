<?php

namespace App\Filament\Resources\AdmissionPeriodResource\Pages;

use App\Filament\Resources\AdmissionPeriodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdmissionPeriods extends ListRecords
{
    protected static string $resource = AdmissionPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
