<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeTypeResource\Pages;
use App\Models\Keuangan\FeeType;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FeeTypeResource extends Resource
{
    protected static ?string $model = FeeType::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $modelLabel = 'Jenis Tagihan';

    protected static ?string $pluralModelLabel = 'Jenis Tagihan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                TextInput::make('code')->label('Kode')->required()->unique(ignoreRecord: true),
                TextInput::make('name')->label('Nama')->required(),
                Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'ukt' => 'UKT/SPP',
                        'pmb' => 'PMB',
                        'registrasi' => 'Registrasi',
                        'wisuda' => 'Wisuda',
                        'layanan' => 'Layanan',
                        'beasiswa' => 'Beasiswa',
                        'dispensasi' => 'Dispensasi',
                    ])
                    ->required(),
                Toggle::make('is_recurring')->label('Berulang per Semester')->helperText('Aktifkan untuk UKT/SPP.'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Kode')->searchable(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('category')->label('Kategori'),
                IconColumn::make('is_recurring')->label('Berulang')->boolean(),
            ])
            ->defaultSort('code');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeTypes::route('/'),
            'create' => Pages\CreateFeeType::route('/create'),
            'edit' => Pages\EditFeeType::route('/{record}/edit'),
        ];
    }
}
