<?php

namespace App\Filament\Resources\ClassSessionResource\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaterialsRelationManager extends RelationManager
{
    protected static string $relationship = 'materials';

    protected static ?string $title = 'Materi';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')->label('Judul')->required(),
            Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
            FileUpload::make('file_path')->label('File')->disk('local')->directory('teaching-materials'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')->label('Judul'),
                TextColumn::make('description')->label('Deskripsi')->limit(50),
                TextColumn::make('file_path')->label('File')->limit(30),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
