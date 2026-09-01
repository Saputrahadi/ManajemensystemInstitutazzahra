<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

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

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    protected static ?string $title = 'Alamat';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('address_type')->label('Jenis')->options(['ktp' => 'KTP', 'domisili' => 'Domisili'])->required(),
            Textarea::make('address')->label('Alamat')->required()->columnSpanFull(),
            TextInput::make('city')->label('Kota/Kabupaten'),
            TextInput::make('province')->label('Provinsi'),
            TextInput::make('postal_code')->label('Kode Pos'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('address')
            ->columns([
                TextColumn::make('address_type')->label('Jenis'),
                TextColumn::make('address')->label('Alamat')->limit(50),
                TextColumn::make('city')->label('Kota'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
