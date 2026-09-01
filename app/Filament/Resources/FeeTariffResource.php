<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeTariffResource\Pages;
use App\Models\Core\StudyProgram;
use App\Models\Keuangan\FeeTariff;
use App\Models\Keuangan\FeeType;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FeeTariffResource extends Resource
{
    protected static ?string $model = FeeTariff::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $modelLabel = 'Master Tarif';

    protected static ?string $pluralModelLabel = 'Master Tarif';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('fee_type_id')
                    ->label('Jenis Tagihan')
                    ->options(fn () => FeeType::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('study_program_id')
                    ->label('Program Studi')
                    ->options(fn () => StudyProgram::pluck('name', 'id'))
                    ->searchable()
                    ->helperText('Kosongkan untuk berlaku di semua Prodi.'),
                TextInput::make('academic_year')->label('Tahun Akademik')->required()->helperText('mis. 2026/2027'),
                TextInput::make('amount')->label('Nominal')->numeric()->prefix('Rp')->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('feeType.name')->label('Jenis Tagihan')->searchable(),
                TextColumn::make('studyProgram.name')->label('Prodi')->default('Semua Prodi'),
                TextColumn::make('academic_year')->label('Tahun Akademik'),
                TextColumn::make('amount')->label('Nominal')->money('IDR'),
            ])
            ->defaultSort('academic_year', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeTariffs::route('/'),
            'create' => Pages\CreateFeeTariff::route('/create'),
            'edit' => Pages\EditFeeTariff::route('/{record}/edit'),
        ];
    }
}
