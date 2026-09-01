<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GraduationRequirementResource\Pages;
use App\Models\Core\StudyProgram;
use App\Models\HasilAkademik\GraduationRequirement;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GraduationRequirementResource extends Resource
{
    protected static ?string $model = GraduationRequirement::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Hasil Akademik';

    protected static ?string $modelLabel = 'Syarat Kelulusan';

    protected static ?string $pluralModelLabel = 'Syarat Kelulusan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('study_program_id')
                    ->label('Program Studi')
                    ->options(fn () => StudyProgram::pluck('name', 'id'))
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('min_credits')->label('Minimal SKS')->numeric()->required(),
                TextInput::make('min_gpa')->label('Minimal IPK')->numeric()->step(0.01)->required(),
                TextInput::make('max_study_years')->label('Maks. Lama Studi (Tahun)')->numeric(),
                Textarea::make('notes')->label('Catatan')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('studyProgram.name')->label('Prodi'),
                TextColumn::make('min_credits')->label('Minimal SKS'),
                TextColumn::make('min_gpa')->label('Minimal IPK'),
                TextColumn::make('max_study_years')->label('Maks. Lama Studi')->default('—'),
            ])
            ->defaultSort('study_program_id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGraduationRequirements::route('/'),
            'create' => Pages\CreateGraduationRequirement::route('/create'),
            'edit' => Pages\EditGraduationRequirement::route('/{record}/edit'),
        ];
    }
}
