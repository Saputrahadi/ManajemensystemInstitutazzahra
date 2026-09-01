<?php

namespace App\Filament\Resources\LecturerResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EducationsRelationManager extends RelationManager
{
    protected static string $relationship = 'educations';

    protected static ?string $title = 'Riwayat Pendidikan';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('degree_level')->label('Jenjang')->options(['S1' => 'S1', 'S2' => 'S2', 'S3' => 'S3'])->required(),
            TextInput::make('institution_name')->label('Institusi')->required(),
            TextInput::make('major')->label('Program Studi'),
            TextInput::make('graduation_year')->label('Tahun Lulus')->numeric(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('institution_name')
            ->columns([
                TextColumn::make('degree_level')->label('Jenjang'),
                TextColumn::make('institution_name')->label('Institusi'),
                TextColumn::make('major')->label('Program Studi'),
                TextColumn::make('graduation_year')->label('Tahun Lulus'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
