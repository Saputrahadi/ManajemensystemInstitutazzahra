<?php

namespace App\Filament\Resources\ClassResource\RelationManagers;

use App\Models\Kelas\Room;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $title = 'Jadwal';

    private const DAYS = [
        1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
        5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu',
    ];

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('room_id')
                ->label('Ruang')
                ->options(fn () => Room::pluck('name', 'id')),
            Select::make('day_of_week')->label('Hari')->options(self::DAYS)->required(),
            TimePicker::make('start_time')->label('Jam Mulai')->required(),
            TimePicker::make('end_time')->label('Jam Selesai')->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('day_of_week')->label('Hari')->formatStateUsing(fn (int $state) => self::DAYS[$state] ?? $state),
                TextColumn::make('start_time')->label('Jam Mulai')->time('H:i'),
                TextColumn::make('end_time')->label('Jam Selesai')->time('H:i'),
                TextColumn::make('room.name')->label('Ruang'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
