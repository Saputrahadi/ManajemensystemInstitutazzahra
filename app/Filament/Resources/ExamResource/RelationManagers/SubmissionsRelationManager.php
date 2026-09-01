<?php

namespace App\Filament\Resources\ExamResource\RelationManagers;

use App\Models\Mahasiswa\Student;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
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

class SubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    protected static ?string $title = 'Pengumpulan Jawaban';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('student_id')
                ->label('Mahasiswa')
                ->options(fn () => Student::pluck('nim', 'id'))
                ->searchable()
                ->required(),
            FileUpload::make('file_path')->label('File')->disk('local')->directory('exam-submissions'),
            DateTimePicker::make('submitted_at')->label('Waktu Kumpul')->default(now()),
            TextInput::make('score')->label('Skor')->numeric(),
            Textarea::make('feedback')->label('Umpan Balik')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.nim')->label('NIM'),
                TextColumn::make('student.user.name')->label('Nama'),
                TextColumn::make('submitted_at')->label('Waktu Kumpul')->dateTime('d M Y H:i')->default('—'),
                TextColumn::make('score')->label('Skor')->default('—'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
