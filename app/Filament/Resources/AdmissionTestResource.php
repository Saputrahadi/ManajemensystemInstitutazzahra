<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdmissionTestResource\Pages;
use App\Models\Pmb\AdmissionPeriod;
use App\Models\Pmb\AdmissionTest;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdmissionTestResource extends Resource
{
    protected static ?string $model = AdmissionTest::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationGroup = 'PMB';

    protected static ?string $modelLabel = 'Ujian Seleksi';

    protected static ?string $pluralModelLabel = 'Ujian Seleksi';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('admission_period_id')
                    ->label('Periode')
                    ->options(fn () => AdmissionPeriod::pluck('name', 'id'))
                    ->required(),
                TextInput::make('name')->label('Nama Ujian')->required(),
                DateTimePicker::make('test_date')->label('Tanggal & Waktu')->required(),
                TextInput::make('location')->label('Lokasi'),
                Textarea::make('description')->label('Keterangan')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Ujian')->searchable(),
                TextColumn::make('admissionPeriod.name')->label('Periode'),
                TextColumn::make('test_date')->label('Tanggal & Waktu')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('location')->label('Lokasi'),
                TextColumn::make('results_count')->counts('results')->label('Peserta'),
            ])
            ->defaultSort('test_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmissionTests::route('/'),
            'create' => Pages\CreateAdmissionTest::route('/create'),
            'edit' => Pages\EditAdmissionTest::route('/{record}/edit'),
        ];
    }
}
