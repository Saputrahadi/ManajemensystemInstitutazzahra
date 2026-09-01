<?php

namespace App\Filament\Resources\ApplicantResource\RelationManagers;

use App\Models\Pmb\AdmissionTest;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'testResults';

    protected static ?string $title = 'Hasil Ujian';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('admission_test_id')
                ->label('Ujian')
                ->options(fn () => AdmissionTest::pluck('name', 'id'))
                ->required(),
            TextInput::make('score')->label('Nilai')->numeric()->step(0.01),
            Select::make('status')->options(['lulus' => 'Lulus', 'tidak_lulus' => 'Tidak Lulus']),
            Textarea::make('notes')->label('Catatan')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('admissionTest.name')->label('Ujian'),
                TextColumn::make('score')->label('Nilai'),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
