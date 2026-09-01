<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GraduationVerificationResource\Pages;
use App\Models\HasilAkademik\GraduationRequirement;
use App\Models\HasilAkademik\GraduationVerification;
use App\Models\Mahasiswa\Student;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * `meets_requirements` disembunyikan saat create supaya selalu disarankan
 * otomatis (bandingkan IPK/SKS mahasiswa vs syarat prodi, lihat
 * GraduationVerification::booted()) — baru muncul & bisa ditimpa manual
 * saat edit, untuk kasus pengecualian yang butuh penilaian verifikator.
 */
class GraduationVerificationResource extends Resource
{
    protected static ?string $model = GraduationVerification::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationGroup = 'Hasil Akademik';

    protected static ?string $modelLabel = 'Verifikasi Kelulusan';

    protected static ?string $pluralModelLabel = 'Verifikasi Kelulusan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('student_id')
                    ->label('Mahasiswa')
                    ->options(fn () => Student::pluck('nim', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('graduation_requirement_id')
                    ->label('Syarat Kelulusan')
                    ->options(fn () => GraduationRequirement::with('studyProgram')->get()
                        ->mapWithKeys(fn (GraduationRequirement $requirement) => [
                            $requirement->id => $requirement->studyProgram->name,
                        ]))
                    ->searchable()
                    ->required(),
                Select::make('verified_by')
                    ->label('Diverifikasi Oleh')
                    ->options(fn () => User::pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()
                    ->required(),
                Toggle::make('meets_requirements')
                    ->label('Memenuhi Syarat')
                    ->hidden(fn (string $context) => $context === 'create')
                    ->helperText('Disarankan otomatis saat dibuat; boleh ditimpa di sini.'),
                Textarea::make('notes')->label('Catatan')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.nim')->label('NIM'),
                TextColumn::make('student.user.name')->label('Nama'),
                TextColumn::make('graduationRequirement.studyProgram.name')->label('Prodi'),
                IconColumn::make('meets_requirements')->label('Memenuhi Syarat')->boolean(),
                TextColumn::make('verified_at')->label('Waktu Verifikasi')->dateTime('d M Y H:i'),
            ])
            ->defaultSort('verified_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGraduationVerifications::route('/'),
            'create' => Pages\CreateGraduationVerification::route('/create'),
            'edit' => Pages\EditGraduationVerification::route('/{record}/edit'),
        ];
    }
}
