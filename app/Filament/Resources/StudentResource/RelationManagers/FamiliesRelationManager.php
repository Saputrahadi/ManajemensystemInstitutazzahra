<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FamiliesRelationManager extends RelationManager
{
    protected static string $relationship = 'families';

    protected static ?string $title = 'Keluarga/Wali';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('relation_type')->label('Hubungan')->options(['ayah' => 'Ayah', 'ibu' => 'Ibu', 'wali' => 'Wali'])->required(),
            TextInput::make('name')->label('Nama')->required(),
            TextInput::make('occupation')->label('Pekerjaan'),
            TextInput::make('phone')->label('No. HP'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('relation_type')->label('Hubungan'),
                TextColumn::make('name')->label('Nama'),
                TextColumn::make('occupation')->label('Pekerjaan'),
                TextColumn::make('phone')->label('No. HP'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
