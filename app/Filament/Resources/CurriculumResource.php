<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurriculumResource\Pages;
use App\Filament\Resources\CurriculumResource\RelationManagers\CoursesRelationManager;
use App\Models\Core\StudyProgram;
use App\Models\Kurikulum\Curriculum;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CurriculumResource extends Resource
{
    protected static ?string $model = Curriculum::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Kurikulum';

    protected static ?string $modelLabel = 'Kurikulum';

    protected static ?string $pluralModelLabel = 'Kurikulum';

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
                TextInput::make('academic_year')->label('Tahun Akademik')->required()->helperText('mis. 2026/2027'),
                TextInput::make('total_credits')->label('Total SKS')->numeric()->required(),
                Select::make('status')
                    ->options(['draft' => 'Draft', 'aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'])
                    ->default('draft')
                    ->required(),
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
                TextColumn::make('academic_year')->label('Tahun Akademik'),
                TextColumn::make('total_credits')->label('Total SKS'),
                BadgeColumn::make('status')->colors([
                    'gray' => 'draft',
                    'success' => 'aktif',
                    'danger' => 'nonaktif',
                ]),
            ])
            ->defaultSort('academic_year', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            CoursesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurricula::route('/'),
            'create' => Pages\CreateCurriculum::route('/create'),
            'edit' => Pages\EditCurriculum::route('/{record}/edit'),
        ];
    }
}
