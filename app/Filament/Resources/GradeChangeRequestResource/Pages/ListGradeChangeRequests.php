<?php

namespace App\Filament\Resources\GradeChangeRequestResource\Pages;

use App\Filament\Resources\GradeChangeRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGradeChangeRequests extends ListRecords
{
    protected static string $resource = GradeChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
