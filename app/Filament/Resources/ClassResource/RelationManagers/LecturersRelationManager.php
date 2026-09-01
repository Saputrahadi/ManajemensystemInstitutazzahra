<?php

namespace App\Filament\Resources\ClassResource\RelationManagers;

use App\Models\Dosen\Lecturer;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LecturersRelationManager extends RelationManager
{
    protected static string $relationship = 'lecturers';

    protected static ?string $title = 'Dosen Pengampu';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('role')
                ->label('Peran')
                ->options(['pengampu' => 'Pengampu', 'asisten' => 'Asisten'])
                ->default('pengampu')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nidn_nidk')
            ->columns([
                TextColumn::make('nidn_nidk')->label('NIDN/NIDK'),
                TextColumn::make('user.name')->label('Nama'),
                TextColumn::make('pivot.role')->label('Peran'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->recordSelect(fn ($select) => $select->options(fn () => Lecturer::with('user')->get()
                        ->mapWithKeys(fn (Lecturer $lecturer) => [$lecturer->id => $lecturer->user->name.' — '.$lecturer->nidn_nidk])))
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('role')
                            ->label('Peran')
                            ->options(['pengampu' => 'Pengampu', 'asisten' => 'Asisten'])
                            ->default('pengampu')
                            ->required(),
                    ]),
            ])
            ->actions([EditAction::make(), DetachAction::make()]);
    }
}
