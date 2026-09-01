<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LearningOutcomeResource\Pages;
use App\Models\Core\StudyProgram;
use App\Models\Kurikulum\LearningOutcome;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LearningOutcomeResource extends Resource
{
    protected static ?string $model = LearningOutcome::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'Kurikulum';

    protected static ?string $modelLabel = 'CPL';

    protected static ?string $pluralModelLabel = 'CPL (Capaian Pembelajaran Lulusan)';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('study_program_id')
                    ->label('Program Studi')
                    ->options(fn () => StudyProgram::pluck('name', 'id'))
                    ->required(),
                TextInput::make('code')->label('Kode CPL')->required()->unique(ignoreRecord: true),
                Textarea::make('description')->label('Deskripsi')->required()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Kode')->searchable(),
                TextColumn::make('studyProgram.name')->label('Prodi'),
                TextColumn::make('description')->label('Deskripsi')->limit(60),
            ])
            ->defaultSort('code');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLearningOutcomes::route('/'),
            'create' => Pages\CreateLearningOutcome::route('/create'),
            'edit' => Pages\EditLearningOutcome::route('/{record}/edit'),
        ];
    }
}
