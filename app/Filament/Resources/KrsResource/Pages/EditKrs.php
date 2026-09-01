<?php

namespace App\Filament\Resources\KrsResource\Pages;

use App\Filament\Resources\KrsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKrs extends EditRecord
{
    protected static string $resource = KrsResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
