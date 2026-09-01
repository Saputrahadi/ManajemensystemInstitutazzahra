<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GraduationRecordResource\Pages;
use App\Models\HasilAkademik\GraduationRecord;
use App\Models\Mahasiswa\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * `final_gpa` sengaja tidak ada di form — dibekukan otomatis dari
 * StudentCumulativeResult saat baris dibuat (lihat GraduationRecord::booted()),
 * yang juga langsung mengubah Student::student_status jadi 'lulus'.
 */
class GraduationRecordResource extends Resource
{
    protected static ?string $model = GraduationRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Hasil Akademik';

    protected static ?string $modelLabel = 'Catatan Kelulusan';

    protected static ?string $pluralModelLabel = 'Catatan Kelulusan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('student_id')
                    ->label('Mahasiswa')
                    ->options(fn () => Student::pluck('nim', 'id'))
                    ->unique(ignoreRecord: true)
                    ->searchable()
                    ->required(),
                DatePicker::make('graduation_date')->label('Tanggal Kelulusan')->required(),
                TextInput::make('thesis_title')->label('Judul Skripsi/Tesis')->columnSpanFull(),
                TextInput::make('graduation_number')->label('Nomor Ijazah')->unique(ignoreRecord: true),
                Select::make('status')
                    ->options(['lulus' => 'Lulus', 'dibatalkan' => 'Dibatalkan'])
                    ->default('lulus')
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
                TextColumn::make('graduation_date')->label('Tanggal Kelulusan')->date('d M Y'),
                TextColumn::make('final_gpa')->label('IPK Akhir'),
                TextColumn::make('graduation_number')->label('Nomor Ijazah')->default('—'),
                BadgeColumn::make('status')->colors([
                    'success' => 'lulus',
                    'danger' => 'dibatalkan',
                ]),
            ])
            ->defaultSort('graduation_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGraduationRecords::route('/'),
            'create' => Pages\CreateGraduationRecord::route('/create'),
            'edit' => Pages\EditGraduationRecord::route('/{record}/edit'),
        ];
    }
}
