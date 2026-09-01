<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Core\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Core System';

    protected static ?string $modelLabel = 'Pengaturan';

    protected static ?string $pluralModelLabel = 'Pengaturan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                TextInput::make('key')->label('Key')->required()->unique(ignoreRecord: true)
                    ->helperText('mis. app.nama_institusi — dipakai di kode lewat Setting::get(key).'),
                TextInput::make('group')->label('Grup')->default('umum'),
                Textarea::make('value')->label('Value')->columnSpanFull()
                    ->helperText('Boleh JSON (mis. "teks", 123, true) atau teks polos — otomatis di-decode saat dibaca lewat Setting::get().'),
                Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
                Toggle::make('is_public')->label('Publik')->helperText('Boleh dibaca tanpa login (mis. lewat API publik).'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->label('Key')->searchable(),
                TextColumn::make('group')->label('Grup')->badge(),
                TextColumn::make('value')->label('Value')->limit(40),
                IconColumn::make('is_public')->label('Publik')->boolean(),
                TextColumn::make('description')->label('Deskripsi')->limit(50),
            ])
            ->defaultSort('key');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
