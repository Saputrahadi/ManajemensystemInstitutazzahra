<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssignmentResource\Pages;
use App\Filament\Resources\AssignmentResource\RelationManagers\SubmissionsRelationManager;
use App\Models\Kelas\CourseClass;
use App\Models\Perkuliahan\Assignment;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Perkuliahan';

    protected static ?string $modelLabel = 'Tugas';

    protected static ?string $pluralModelLabel = 'Tugas';

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
                TextInput::make('title')->label('Judul')->required(),
                DateTimePicker::make('due_date')->label('Tenggat'),
                TextInput::make('max_score')->label('Skor Maksimal')->numeric()->default(100)->required(),
                Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('courseClass.courseOffering.course.code')->label('Kode MK'),
                TextColumn::make('courseClass.code')->label('Kelas'),
                TextColumn::make('title')->label('Judul')->searchable(),
                TextColumn::make('due_date')->label('Tenggat')->dateTime('d M Y H:i')->default('—'),
                TextColumn::make('max_score')->label('Skor Maksimal'),
            ])
            ->defaultSort('due_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            SubmissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssignments::route('/'),
            'create' => Pages\CreateAssignment::route('/create'),
            'edit' => Pages\EditAssignment::route('/{record}/edit'),
        ];
    }
}
