<?php

namespace App\Filament\Resources\LecturerResource\RelationManagers;

use App\Models\Core\StudyProgram;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Satu dosen bisa punya lebih dari satu penugasan sekaligus (mis. Dosen +
 * Dosen Wali di Prodi yang sama) — lihat blueprint #unit contoh rantai akses.
 */
class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    protected static ?string $title = 'Penugasan';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('study_program_id')->label('Program Studi')->options(fn () => StudyProgram::pluck('name', 'id'))->required(),
            Select::make('assignment_type')->label('Jenis Penugasan')->options(['dosen' => 'Dosen', 'dosen_wali' => 'Dosen Wali'])->required(),
            DatePicker::make('start_date')->label('Mulai')->required(),
            DatePicker::make('end_date')->label('Selesai'),
            Select::make('status')->options(['aktif' => 'Aktif', 'selesai' => 'Selesai'])->default('aktif')->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('assignment_type')
            ->columns([
                TextColumn::make('studyProgram.name')->label('Prodi'),
                TextColumn::make('assignment_type')->label('Jenis')->badge(),
                TextColumn::make('start_date')->label('Mulai')->date('d M Y'),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
