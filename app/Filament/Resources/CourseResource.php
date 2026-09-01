<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers\LearningOutcomesRelationManager;
use App\Filament\Resources\CourseResource\RelationManagers\PrerequisitesRelationManager;
use App\Models\Core\StudyProgram;
use App\Models\Kurikulum\Course;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Kurikulum';

    protected static ?string $modelLabel = 'Mata Kuliah';

    protected static ?string $pluralModelLabel = 'Mata Kuliah';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('study_program_id')
                    ->label('Program Studi')
                    ->options(fn () => StudyProgram::pluck('name', 'id'))
                    ->required(),
                TextInput::make('code')->label('Kode')->required()->unique(ignoreRecord: true),
                TextInput::make('name')->label('Nama')->required(),
                TextInput::make('credits')->label('SKS')->numeric()->required(),
                Select::make('status')
                    ->options(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'])
                    ->default('aktif')
                    ->required(),
                Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Kode')->searchable(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('studyProgram.name')->label('Prodi'),
                TextColumn::make('credits')->label('SKS'),
                BadgeColumn::make('status')->colors([
                    'success' => 'aktif',
                    'danger' => 'nonaktif',
                ]),
            ])
            ->defaultSort('code');
    }

    public static function getRelations(): array
    {
        return [
            PrerequisitesRelationManager::class,
            LearningOutcomesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
