<?php

namespace App\Filament\Resources\GraduationVerificationResource\Pages;

use App\Filament\Resources\GraduationVerificationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGraduationVerification extends EditRecord
{
    protected static string $resource = GraduationVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
