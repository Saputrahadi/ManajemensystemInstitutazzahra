<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentSemesterResultResource\Pages;
use App\Models\HasilAkademik\StudentSemesterResult;
use App\Models\Mahasiswa\Student;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** `credits_taken`/`credits_earned`/`semester_gpa` sengaja tidak ada di form — dihitung otomatis, lihat StudentSemesterResult::booted(). */
class StudentSemesterResultResource extends Resource
{
    protected static ?string $model = StudentSemesterResult::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Hasil Akademik';

    protected static ?string $modelLabel = 'IP Semester';

    protected static ?string $pluralModelLabel = 'IP Semester';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('student_id')
                    ->label('Mahasiswa')
                    ->options(fn () => Student::pluck('nim', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('academic_year')->label('Tahun Akademik')->required()->helperText('mis. 2026/2027'),
                Select::make('term')
                    ->label('Semester')
                    ->options(['ganjil' => 'Ganjil', 'genap' => 'Genap'])
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.nim')->label('NIM'),
                TextColumn::make('student.user.name')->label('Nama'),
                TextColumn::make('academic_year')->label('Tahun Akademik'),
                TextColumn::make('term')->label('Semester'),
                TextColumn::make('credits_taken')->label('SKS Diambil'),
                TextColumn::make('credits_earned')->label('SKS Lulus'),
                TextColumn::make('semester_gpa')->label('IP Semester'),
            ])
            ->defaultSort('academic_year', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentSemesterResults::route('/'),
            'create' => Pages\CreateStudentSemesterResult::route('/create'),
            'edit' => Pages\EditStudentSemesterResult::route('/{record}/edit'),
        ];
    }
}
