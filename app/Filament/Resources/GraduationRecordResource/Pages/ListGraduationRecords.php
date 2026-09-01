<?php

namespace App\Filament\Resources\GraduationRecordResource\Pages;

use App\Filament\Resources\GraduationRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGraduationRecords extends ListRecords
{
    protected static string $resource = GraduationRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
