<?php

namespace App\Filament\Resources\CourseOfferingResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClassesRelationManager extends RelationManager
{
    protected static string $relationship = 'classes';

    protected static ?string $title = 'Kelas';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('code')->label('Kode Kelas')->required()->helperText('mis. A, B, C'),
            TextInput::make('capacity')->label('Kapasitas')->numeric(),
            Select::make('status')
                ->options(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'])
                ->default('aktif')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->columns([
                TextColumn::make('code')->label('Kode Kelas'),
                TextColumn::make('capacity')->label('Kapasitas'),
                TextColumn::make('status')->label('Status'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
