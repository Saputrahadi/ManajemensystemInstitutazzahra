<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResource\Pages;
use App\Filament\Resources\ExamResource\RelationManagers\ParticipantsRelationManager;
use App\Filament\Resources\ExamResource\RelationManagers\SubmissionsRelationManager;
use App\Models\Kelas\CourseClass;
use App\Models\Ujian\Exam;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationGroup = 'Ujian';

    protected static ?string $modelLabel = 'Ujian';

    protected static ?string $pluralModelLabel = 'Ujian (UTS/UAS)';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('class_id')
                    ->label('Kelas')
                    ->options(fn () => CourseClass::with('courseOffering.course')->get()
                        ->mapWithKeys(fn (CourseClass $class) => [
                            $class->id => "{$class->courseOffering->course->code} - {$class->code} ({$class->courseOffering->academic_year})",
                        ]))
                    ->searchable()
                    ->required(),
                Select::make('type')
                    ->label('Jenis')
                    ->options(['uts' => 'UTS', 'uas' => 'UAS'])
                    ->required(),
                TextInput::make('title')->label('Judul')->required(),
                DatePicker::make('exam_date')->label('Tanggal')->required(),
                TimePicker::make('start_time')->label('Jam Mulai')->required(),
                TimePicker::make('end_time')->label('Jam Selesai')->required(),
                TextInput::make('max_score')->label('Skor Maksimal')->numeric()->default(100)->required(),
                Select::make('status')
                    ->options(['dijadwalkan' => 'Dijadwalkan', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'])
                    ->default('dijadwalkan')
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('courseClass.courseOffering.course.code')->label('Kode MK'),
                TextColumn::make('courseClass.code')->label('Kelas'),
                TextColumn::make('type')->label('Jenis')->formatStateUsing(fn (string $state) => strtoupper($state)),
                TextColumn::make('exam_date')->label('Tanggal')->date('d M Y'),
                BadgeColumn::make('status')->colors([
                    'gray' => 'dijadwalkan',
                    'success' => 'selesai',
                    'danger' => 'dibatalkan',
                ]),
            ])
            ->defaultSort('exam_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ParticipantsRelationManager::class,
            SubmissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'edit' => Pages\EditExam::route('/{record}/edit'),
        ];
    }
}
