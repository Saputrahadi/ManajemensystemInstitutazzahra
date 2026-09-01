<?php

namespace App\Filament\Resources\LearningOutcomeResource\Pages;

use App\Filament\Resources\LearningOutcomeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLearningOutcomes extends ListRecords
{
    protected static string $resource = LearningOutcomeResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
