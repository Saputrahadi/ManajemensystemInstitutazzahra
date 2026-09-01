<?php

namespace App\Filament\Resources\StudentSemesterResultResource\Pages;

use App\Filament\Resources\StudentSemesterResultResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudentSemesterResults extends ListRecords
{
    protected static string $resource = StudentSemesterResultResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
