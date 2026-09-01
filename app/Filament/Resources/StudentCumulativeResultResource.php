<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentCumulativeResultResource\Pages;
use App\Models\HasilAkademik\StudentCumulativeResult;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Tidak ada Create — baris ini murni hasil recalculate otomatis dari
 * StudentSemesterResult (lihat StudentCumulativeResult::recalculateForStudent()),
 * bukan data yang diinput manual.
 */
class StudentCumulativeResultResource extends Resource
{
    protected static ?string $model = StudentCumulativeResult::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationGroup = 'Hasil Akademik';

    protected static ?string $modelLabel = 'IPK Kumulatif';

    protected static ?string $pluralModelLabel = 'IPK Kumulatif';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.nim')->label('NIM'),
                TextColumn::make('student.user.name')->label('Nama'),
                TextColumn::make('total_credits_earned')->label('Total SKS Lulus'),
                TextColumn::make('cumulative_gpa')->label('IPK'),
            ])
            ->defaultSort('cumulative_gpa', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentCumulativeResults::route('/'),
        ];
    }
}
