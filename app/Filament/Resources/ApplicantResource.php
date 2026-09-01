<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicantResource\Pages;
use App\Filament\Resources\ApplicantResource\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\ApplicantResource\RelationManagers\InterviewsRelationManager;
use App\Filament\Resources\ApplicantResource\RelationManagers\TestResultsRelationManager;
use App\Models\Core\StudyProgram;
use App\Models\Pmb\AdmissionPath;
use App\Models\Pmb\AdmissionPeriod;
use App\Models\Pmb\Applicant;
use App\Services\Mahasiswa\StudentEnrollmentService;
use App\Services\Pmb\AdmissionDecisionService;
use App\Services\Pmb\AdmissionScoringService;
use App\Services\Pmb\ApplicantVerificationService;
use App\Services\Pmb\ReRegistrationService;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ApplicantResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'PMB';

    protected static ?string $modelLabel = 'Calon Mahasiswa';

    protected static ?string $pluralModelLabel = 'Calon Mahasiswa';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Identitas')
                ->columns(2)
                ->schema([
                    TextInput::make('registration_number')->label('No. Pendaftaran')->disabled(),
                    TextInput::make('name')->label('Nama')->required(),
                    TextInput::make('nik')->label('NIK'),
                    TextInput::make('phone')->label('No. HP'),
                    TextInput::make('email')->label('Email')->email(),
                ]),
            Section::make('Status Seleksi')
                ->columns(2)
                ->schema([
                    Select::make('admission_period_id')
                        ->label('Periode')
                        ->options(fn () => AdmissionPeriod::pluck('name', 'id'))
                        ->required(),
                    Select::make('admission_path_id')
                        ->label('Jalur')
                        ->options(fn () => AdmissionPath::pluck('name', 'id'))
                        ->required(),
                    Select::make('study_program_id')
                        ->label('Program Studi')
                        ->options(fn () => StudyProgram::pluck('name', 'id'))
                        ->required(),
                    Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'mendaftar' => 'Mendaftar',
                            'menunggu_verifikasi' => 'Menunggu Verifikasi',
                            'terverifikasi' => 'Terverifikasi',
                            'mengikuti_seleksi' => 'Mengikuti Seleksi',
                            'lulus' => 'Lulus',
                            'tidak_lulus' => 'Tidak Lulus',
                            'registrasi_ulang' => 'Registrasi Ulang',
                            'selesai' => 'Selesai',
                        ])
                        ->required(),
                    Select::make('registration_fee_status')
                        ->label('Status Bayar')
                        ->options(['belum_bayar' => 'Belum Bayar', 'lunas' => 'Lunas'])
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration_number')->label('No. Pendaftaran')->searchable(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('admissionPeriod.name')->label('Periode'),
                TextColumn::make('studyProgram.name')->label('Prodi'),
                BadgeColumn::make('status')->colors([
                    'gray' => 'draft',
                    'info' => ['mendaftar', 'menunggu_verifikasi', 'mengikuti_seleksi'],
                    'success' => ['terverifikasi', 'lulus', 'selesai'],
                    'danger' => 'tidak_lulus',
                    'warning' => 'registrasi_ulang',
                ]),
                BadgeColumn::make('registration_fee_status')->label('Bayar')->colors([
                    'danger' => 'belum_bayar',
                    'success' => 'lunas',
                ]),
                TextColumn::make('created_at')->label('Daftar')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('admission_period_id')->label('Periode')->relationship('admissionPeriod', 'name'),
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'mendaftar' => 'Mendaftar',
                    'menunggu_verifikasi' => 'Menunggu Verifikasi',
                    'terverifikasi' => 'Terverifikasi',
                    'mengikuti_seleksi' => 'Mengikuti Seleksi',
                    'lulus' => 'Lulus',
                    'tidak_lulus' => 'Tidak Lulus',
                    'registrasi_ulang' => 'Registrasi Ulang',
                    'selesai' => 'Selesai',
                ]),
            ])
            ->actions([
                Action::make('verifikasi')
                    ->label('Verifikasi Administrasi')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->form([
                        Select::make('status')
                            ->label('Hasil')
                            ->options(['disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'revisi' => 'Perlu Revisi'])
                            ->required(),
                        Textarea::make('notes')->label('Catatan'),
                    ])
                    ->action(function (Applicant $record, array $data, ApplicantVerificationService $service) {
                        $service->verify($record, auth()->user(), $data['status'], $data['notes'] ?? null);
                        Notification::make()->title('Verifikasi tersimpan')->success()->send();
                    }),

                Action::make('hitungSkor')
                    ->label('Hitung Skor')
                    ->icon('heroicon-o-calculator')
                    ->action(function (Applicant $record, AdmissionScoringService $service) {
                        $score = $service->recompute($record);
                        Notification::make()
                            ->title('Skor dihitung ulang')
                            ->body('Skor akhir: '.($score->final_score !== null ? number_format($score->final_score, 2) : '—'))
                            ->success()
                            ->send();
                    }),

                Action::make('keputusan')
                    ->label('Tetapkan Keputusan')
                    ->icon('heroicon-o-flag')
                    ->form([
                        Select::make('decision')
                            ->label('Keputusan')
                            ->options(['diterima' => 'Diterima', 'ditolak' => 'Ditolak', 'cadangan' => 'Cadangan'])
                            ->required(),
                        Textarea::make('notes')->label('Catatan'),
                    ])
                    ->action(function (Applicant $record, array $data, AdmissionDecisionService $service) {
                        $service->decide($record, auth()->user(), $data['decision'], $data['notes'] ?? null);
                        Notification::make()->title('Keputusan tersimpan')->success()->send();
                    }),

                Action::make('registrasiUlang')
                    ->label('Registrasi Ulang')
                    ->icon('heroicon-o-identification')
                    ->visible(fn (Applicant $record) => $record->status === 'lulus')
                    ->requiresConfirmation()
                    ->action(function (Applicant $record, ReRegistrationService $service) {
                        $reRegistration = $service->register($record);
                        Notification::make()
                            ->title('Registrasi ulang selesai')
                            ->body('NIM: '.$reRegistration->nim)
                            ->success()
                            ->send();
                    }),

                Action::make('buatAkunMahasiswa')
                    ->label('Buat Akun Mahasiswa')
                    ->icon('heroicon-o-user-plus')
                    ->visible(fn (Applicant $record) => $record->status === 'selesai' && $record->reRegistration && ! $record->reRegistration->student_id)
                    ->requiresConfirmation()
                    ->action(function (Applicant $record, StudentEnrollmentService $service) {
                        $student = $service->enroll($record->reRegistration);
                        Notification::make()
                            ->title('Akun Mahasiswa dibuat')
                            ->body('NIM: '.$student->nim)
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
            TestResultsRelationManager::class,
            InterviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplicants::route('/'),
            'edit' => Pages\EditApplicant::route('/{record}/edit'),
        ];
    }
}
