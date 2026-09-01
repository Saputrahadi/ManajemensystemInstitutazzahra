<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdmissionPathResource\Pages;
use App\Models\Pmb\AdmissionPath;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdmissionPathResource extends Resource
{
    protected static ?string $model = AdmissionPath::class;

    protected static ?string $navigationIcon = 'heroicon-o-signal';

    protected static ?string $navigationGroup = 'PMB';

    protected static ?string $modelLabel = 'Jalur Pendaftaran';

    protected static ?string $pluralModelLabel = 'Jalur Pendaftaran';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                TextInput::make('code')->required()->unique(ignoreRecord: true),
                TextInput::make('name')->label('Nama')->required(),
                Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
                Toggle::make('is_active')->label('Aktif')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('description')->label('Deskripsi')->limit(50),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmissionPaths::route('/'),
            'create' => Pages\CreateAdmissionPath::route('/create'),
            'edit' => Pages\EditAdmissionPath::route('/{record}/edit'),
        ];
    }
}
