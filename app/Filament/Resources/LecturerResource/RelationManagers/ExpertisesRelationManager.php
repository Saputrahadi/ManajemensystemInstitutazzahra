<?php

namespace App\Filament\Resources\LecturerResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpertisesRelationManager extends RelationManager
{
    protected static string $relationship = 'expertises';

    protected static ?string $title = 'Keahlian';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('expertise_name')->label('Bidang Keahlian')->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('expertise_name')
            ->columns([
                TextColumn::make('expertise_name')->label('Bidang Keahlian'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([DeleteAction::make()]);
    }
}
