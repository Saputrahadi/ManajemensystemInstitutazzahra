<?php

namespace App\Filament\Resources\FeeTariffResource\Pages;

use App\Filament\Resources\FeeTariffResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFeeTariffs extends ListRecords
{
    protected static string $resource = FeeTariffResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
