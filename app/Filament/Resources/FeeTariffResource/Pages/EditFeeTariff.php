<?php

namespace App\Filament\Resources\FeeTariffResource\Pages;

use App\Filament\Resources\FeeTariffResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFeeTariff extends EditRecord
{
    protected static string $resource = FeeTariffResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
