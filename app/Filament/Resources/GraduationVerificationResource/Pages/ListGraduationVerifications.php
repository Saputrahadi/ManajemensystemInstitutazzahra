<?php

namespace App\Filament\Resources\GraduationVerificationResource\Pages;

use App\Filament\Resources\GraduationVerificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGraduationVerifications extends ListRecords
{
    protected static string $resource = GraduationVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
