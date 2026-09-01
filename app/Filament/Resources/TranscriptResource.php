<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TranscriptResource\Pages;
use App\Models\HasilAkademik\Transcript;
use App\Models\Mahasiswa\Student;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** `cumulative_gpa_snapshot`/`total_credits_snapshot` sengaja tidak ada di form — dibekukan otomatis dari StudentCumulativeResult saat diterbitkan. */
class TranscriptResource extends Resource
{
    protected static ?string $model = Transcript::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Hasil Akademik';

    protected static ?string $modelLabel = 'Transkrip';

    protected static ?string $pluralModelLabel = 'Transkrip';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('student_id')
                    ->label('Mahasiswa')
                    ->options(fn () => Student::pluck('nim', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('issued_by')
                    ->label('Diterbitkan Oleh')
                    ->options(fn () => User::pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()
                    ->required(),
                FileUpload::make('file_path')->label('File PDF')->disk('local')->directory('transcripts')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.nim')->label('NIM'),
                TextColumn::make('student.user.name')->label('Nama'),
                TextColumn::make('cumulative_gpa_snapshot')->label('IPK (saat terbit)'),
                TextColumn::make('total_credits_snapshot')->label('Total SKS (saat terbit)'),
                TextColumn::make('issued_at')->label('Diterbitkan')->dateTime('d M Y H:i'),
            ])
            ->defaultSort('issued_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTranscripts::route('/'),
            'create' => Pages\CreateTranscript::route('/create'),
            'edit' => Pages\EditTranscript::route('/{record}/edit'),
        ];
    }
}
