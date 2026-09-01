<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use App\Models\Kurikulum\LearningOutcome;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** CPMK: pemetaan Mata Kuliah ini ke CPL milik prodi yang sama. */
class LearningOutcomesRelationManager extends RelationManager
{
    protected static string $relationship = 'learningOutcomes';

    protected static ?string $title = 'CPMK (Capaian Pembelajaran Mata Kuliah)';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('learning_outcome_id')
                ->label('CPL')
                ->options(fn () => LearningOutcome::where('study_program_id', $this->getOwnerRecord()->study_program_id)
                    ->pluck('code', 'id'))
                ->required(),
            TextInput::make('code')->label('Kode CPMK')->required(),
            Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->columns([
                TextColumn::make('code')->label('Kode CPMK'),
                TextColumn::make('learningOutcome.code')->label('CPL'),
                TextColumn::make('description')->label('Deskripsi')->limit(50),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
