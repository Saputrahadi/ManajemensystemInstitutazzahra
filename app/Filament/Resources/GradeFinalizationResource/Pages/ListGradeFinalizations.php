<?php

namespace App\Filament\Resources\GradeFinalizationResource\Pages;

use App\Filament\Resources\GradeFinalizationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGradeFinalizations extends ListRecords
{
    protected static string $resource = GradeFinalizationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
