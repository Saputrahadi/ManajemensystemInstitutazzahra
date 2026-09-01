<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LecturerResource\Pages;
use App\Filament\Resources\LecturerResource\RelationManagers\AssignmentsRelationManager;
use App\Filament\Resources\LecturerResource\RelationManagers\EducationsRelationManager;
use App\Filament\Resources\LecturerResource\RelationManagers\ExpertisesRelationManager;
use App\Models\Core\StudyProgram;
use App\Models\Dosen\Lecturer;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Satu akun login per dosen — dibuat/diubah sekaligus dengan data Lecturer
 * dalam satu transaksi (lihat Pages\CreateLecturer/EditLecturer), pola sama
 * dengan EmployeeController di project sistem-yayasan-laravel.
 */
class LecturerResource extends Resource
{
    protected static ?string $model = Lecturer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Dosen';

    protected static ?string $modelLabel = 'Dosen';

    protected static ?string $pluralModelLabel = 'Dosen';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Akun')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->label('Nama')->required(),
                    TextInput::make('email')->label('Email')->email()->required()->unique(table: 'users', ignoreRecord: true),
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->required(fn (string $context) => $context === 'create')
                        ->helperText('Kosongkan saat edit jika tidak ingin mengubah password.'),
                ]),
            Section::make('Data Dosen')
                ->columns(2)
                ->schema([
                    TextInput::make('nidn_nidk')->label('NIDN/NIDK')->required()->unique(ignoreRecord: true),
                    Select::make('study_program_id')
                        ->label('Program Studi')
                        ->options(fn () => StudyProgram::pluck('name', 'id'))
                        ->required(),
                    TextInput::make('academic_rank')->label('Jabatan Akademik')->helperText('mis. Asisten Ahli, Lektor'),
                    Select::make('employment_status')
                        ->label('Status Kepegawaian')
                        ->options(['tetap' => 'Tetap', 'tidak_tetap' => 'Tidak Tetap'])
                        ->default('tetap')
                        ->required(),
                    Select::make('status')
                        ->options(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'])
                        ->default('aktif')
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nidn_nidk')->label('NIDN/NIDK')->searchable(),
                TextColumn::make('user.name')->label('Nama')->searchable(),
                TextColumn::make('studyProgram.name')->label('Prodi'),
                TextColumn::make('academic_rank')->label('Jabatan'),
                BadgeColumn::make('employment_status')->label('Kepegawaian')->colors([
                    'success' => 'tetap',
                    'gray' => 'tidak_tetap',
                ]),
                BadgeColumn::make('status')->colors([
                    'success' => 'aktif',
                    'danger' => 'nonaktif',
                ]),
            ])
            ->defaultSort('nidn_nidk');
    }

    public static function getRelations(): array
    {
        return [
            EducationsRelationManager::class,
            ExpertisesRelationManager::class,
            AssignmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLecturers::route('/'),
            'create' => Pages\CreateLecturer::route('/create'),
            'edit' => Pages\EditLecturer::route('/{record}/edit'),
        ];
    }
}
