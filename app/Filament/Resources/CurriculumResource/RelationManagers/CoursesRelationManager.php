<?php

namespace App\Filament\Resources\CurriculumResource\RelationManagers;

use App\Models\Kurikulum\Course;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    protected static ?string $title = 'Mata Kuliah dalam Kurikulum';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('semester')->label('Semester')->numeric()->required(),
            Toggle::make('is_mandatory')->label('Wajib')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('code')->label('Kode'),
                TextColumn::make('name')->label('Nama Mata Kuliah')->searchable(),
                TextColumn::make('credits')->label('SKS'),
                TextColumn::make('pivot.semester')->label('Semester'),
                IconColumn::make('pivot.is_mandatory')->label('Wajib')->boolean(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->recordSelect(fn ($select) => $select->options(fn () => Course::pluck('name', 'id')))
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('semester')->label('Semester')->numeric()->required(),
                        Toggle::make('is_mandatory')->label('Wajib')->default(true),
                    ]),
            ])
            ->actions([EditAction::make(), DetachAction::make()]);
    }
}
