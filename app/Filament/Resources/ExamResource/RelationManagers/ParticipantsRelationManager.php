<?php

namespace App\Filament\Resources\ExamResource\RelationManagers;

use App\Models\Mahasiswa\Student;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';

    protected static ?string $title = 'Peserta';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('student_id')
                ->label('Mahasiswa')
                ->options(fn () => Student::pluck('nim', 'id'))
                ->searchable()
                ->required(),
            Select::make('status')
                ->options(['terdaftar' => 'Terdaftar', 'hadir' => 'Hadir', 'tidak_hadir' => 'Tidak Hadir'])
                ->default('terdaftar')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.nim')->label('NIM'),
                TextColumn::make('student.user.name')->label('Nama'),
                BadgeColumn::make('status')->colors([
                    'gray' => 'terdaftar',
                    'success' => 'hadir',
                    'danger' => 'tidak_hadir',
                ]),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
