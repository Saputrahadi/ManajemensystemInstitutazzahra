<?php

namespace App\Filament\Resources\GradeComponentResource\RelationManagers;

use App\Models\Mahasiswa\Student;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GradesRelationManager extends RelationManager
{
    protected static string $relationship = 'grades';

    protected static ?string $title = 'Nilai Mahasiswa';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('student_id')
                ->label('Mahasiswa')
                ->options(fn () => Student::pluck('nim', 'id'))
                ->searchable()
                ->required(),
            TextInput::make('score')->label('Skor')->numeric()->minValue(0)->maxValue(100)->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.nim')->label('NIM'),
                TextColumn::make('student.user.name')->label('Nama'),
                TextColumn::make('score')->label('Skor'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
