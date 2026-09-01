<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassSessionResource\Pages;
use App\Filament\Resources\ClassSessionResource\RelationManagers\AttendancesRelationManager;
use App\Filament\Resources\ClassSessionResource\RelationManagers\MaterialsRelationManager;
use App\Models\Kelas\CourseClass;
use App\Models\Perkuliahan\ClassSession;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClassSessionResource extends Resource
{
    protected static ?string $model = ClassSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = 'Perkuliahan';

    protected static ?string $modelLabel = 'Pertemuan Kuliah';

    protected static ?string $pluralModelLabel = 'Pertemuan Kuliah';

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
                TextInput::make('meeting_number')->label('Pertemuan Ke-')->numeric()->required(),
                TextInput::make('topic')->label('Topik'),
                DatePicker::make('session_date')->label('Tanggal')->required(),
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
                TextColumn::make('meeting_number')->label('Pertemuan Ke-'),
                TextColumn::make('topic')->label('Topik')->limit(40),
                TextColumn::make('session_date')->label('Tanggal')->date('d M Y'),
                BadgeColumn::make('status')->colors([
                    'gray' => 'dijadwalkan',
                    'success' => 'selesai',
                    'danger' => 'dibatalkan',
                ]),
            ])
            ->defaultSort('session_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            AttendancesRelationManager::class,
            MaterialsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassSessions::route('/'),
            'create' => Pages\CreateClassSession::route('/create'),
            'edit' => Pages\EditClassSession::route('/{record}/edit'),
        ];
    }
}
