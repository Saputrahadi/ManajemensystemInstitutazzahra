<?php

namespace App\Filament\Resources\ClassSessionResource\RelationManagers;

use App\Models\Mahasiswa\Student;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    protected static ?string $title = 'Presensi';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('student_id')
                ->label('Mahasiswa')
                ->options(fn () => Student::pluck('nim', 'id'))
                ->searchable()
                ->required(),
            Select::make('status')
                ->options(['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'])
                ->required(),
            Textarea::make('notes')->label('Catatan')->columnSpanFull(),
            DateTimePicker::make('recorded_at')->label('Waktu Dicatat')->default(now())->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.nim')->label('NIM'),
                TextColumn::make('student.user.name')->label('Nama'),
                BadgeColumn::make('status')->colors([
                    'success' => 'hadir',
                    'warning' => 'izin',
                    'info' => 'sakit',
                    'danger' => 'alpa',
                ]),
                TextColumn::make('recorded_at')->label('Waktu Dicatat')->dateTime('d M Y H:i'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
