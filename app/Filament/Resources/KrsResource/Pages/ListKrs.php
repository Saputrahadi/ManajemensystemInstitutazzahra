<?php

namespace App\Filament\Resources\KrsResource\Pages;

use App\Filament\Resources\KrsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKrs extends ListRecords
{
    protected static string $resource = KrsResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
