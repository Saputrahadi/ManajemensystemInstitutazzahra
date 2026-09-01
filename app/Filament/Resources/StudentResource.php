<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers\AddressesRelationManager;
use App\Filament\Resources\StudentResource\RelationManagers\FamiliesRelationManager;
use App\Filament\Resources\StudentResource\RelationManagers\StatusHistoriesRelationManager;
use App\Models\Core\StudyProgram;
use App\Models\Dosen\Lecturer;
use App\Models\Mahasiswa\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Mahasiswa';

    protected static ?string $modelLabel = 'Mahasiswa';

    protected static ?string $pluralModelLabel = 'Mahasiswa';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Identitas')
                ->columns(2)
                ->schema([
                    TextInput::make('nim')->label('NIM')->disabled(),
                    Select::make('study_program_id')
                        ->label('Program Studi')
                        ->options(fn () => StudyProgram::pluck('name', 'id'))
                        ->required(),
                    TextInput::make('admission_year')->label('Tahun Masuk')->required(),
                    TextInput::make('entry_path')->label('Jalur Masuk'),
                    DatePicker::make('enrollment_date')->label('Tanggal Registrasi'),
                    Select::make('advisor_id')
                        ->label('Dosen Wali')
                        ->options(fn () => Lecturer::with('user')->get()->mapWithKeys(fn (Lecturer $lecturer) => [$lecturer->id => $lecturer->user->name.' — '.$lecturer->nidn_nidk]))
                        ->searchable(),
                    Select::make('student_status')
                        ->label('Status')
                        ->options([
                            'registrasi' => 'Registrasi (Belum Aktif)',
                            'aktif' => 'Aktif',
                            'cuti' => 'Cuti',
                            'nonaktif' => 'Nonaktif',
                            'lulus' => 'Lulus',
                            'mengundurkan_diri' => 'Mengundurkan Diri',
                            'dikeluarkan' => 'Dikeluarkan',
                            'alumni' => 'Alumni',
                        ])
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nim')->label('NIM')->searchable(),
                TextColumn::make('user.name')->label('Nama')->searchable(),
                TextColumn::make('studyProgram.name')->label('Prodi'),
                TextColumn::make('admission_year')->label('Tahun Masuk'),
                TextColumn::make('advisor.user.name')->label('Dosen Wali')->default('—'),
                BadgeColumn::make('student_status')->label('Status')->colors([
                    'gray' => 'registrasi',
                    'success' => ['aktif', 'lulus', 'alumni'],
                    'warning' => 'cuti',
                    'danger' => ['nonaktif', 'mengundurkan_diri', 'dikeluarkan'],
                ]),
            ])
            ->filters([
                SelectFilter::make('study_program_id')->label('Prodi')->relationship('studyProgram', 'name'),
                SelectFilter::make('student_status')->label('Status')->options([
                    'registrasi' => 'Registrasi (Belum Aktif)',
                    'aktif' => 'Aktif',
                    'cuti' => 'Cuti',
                    'nonaktif' => 'Nonaktif',
                    'lulus' => 'Lulus',
                    'mengundurkan_diri' => 'Mengundurkan Diri',
                    'dikeluarkan' => 'Dikeluarkan',
                    'alumni' => 'Alumni',
                ]),
            ])
            ->defaultSort('nim');
    }

    public static function getRelations(): array
    {
        return [
            AddressesRelationManager::class,
            FamiliesRelationManager::class,
            StatusHistoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
