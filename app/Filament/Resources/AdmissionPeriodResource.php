<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdmissionPeriodResource\Pages;
use App\Models\Pmb\AdmissionPeriod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdmissionPeriodResource extends Resource
{
    protected static ?string $model = AdmissionPeriod::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'PMB';

    protected static ?string $modelLabel = 'Periode PMB';

    protected static ?string $pluralModelLabel = 'Periode PMB';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                TextInput::make('code')->required()->unique(ignoreRecord: true),
                TextInput::make('name')->label('Nama')->required(),
                TextInput::make('academic_year')->label('Tahun Ajaran')->required()->helperText('mis. 2027/2028'),
                Select::make('status')
                    ->options(['draft' => 'Draft', 'aktif' => 'Aktif', 'ditutup' => 'Ditutup'])
                    ->default('draft')
                    ->required(),
                DatePicker::make('opens_at')->label('Buka')->required(),
                DatePicker::make('closes_at')->label('Tutup')->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('academic_year')->label('Tahun Ajaran'),
                BadgeColumn::make('status')->colors([
                    'gray' => 'draft',
                    'success' => 'aktif',
                    'danger' => 'ditutup',
                ]),
                TextColumn::make('opens_at')->label('Buka')->date('d M Y'),
                TextColumn::make('closes_at')->label('Tutup')->date('d M Y'),
                TextColumn::make('applicants_count')->counts('applicants')->label('Pendaftar'),
            ])
            ->defaultSort('opens_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmissionPeriods::route('/'),
            'create' => Pages\CreateAdmissionPeriod::route('/create'),
            'edit' => Pages\EditAdmissionPeriod::route('/{record}/edit'),
        ];
    }
}
