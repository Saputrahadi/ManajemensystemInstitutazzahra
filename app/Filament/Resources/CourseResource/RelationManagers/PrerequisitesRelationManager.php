<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use App\Models\Kurikulum\Course;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrerequisitesRelationManager extends RelationManager
{
    protected static string $relationship = 'prerequisites';

    protected static ?string $title = 'Mata Kuliah Prasyarat';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('code')->label('Kode'),
                TextColumn::make('name')->label('Nama Mata Kuliah')->searchable(),
                TextColumn::make('credits')->label('SKS'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->recordSelect(fn ($select) => $select->options(
                        fn () => Course::where('id', '!=', $this->getOwnerRecord()->id)->pluck('name', 'id')
                    )),
            ])
            ->actions([DetachAction::make()]);
    }
}
