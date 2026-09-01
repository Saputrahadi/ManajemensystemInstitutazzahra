<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Riwayat status — append-only (tidak ada EditAction/DeleteAction), sesuai
 * prinsip "Historical Data" di blueprint #erd. Menambah baris baru di sini
 * juga memperbarui `Student.student_status` (lihat afterCreate hook).
 */
class StatusHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'statusHistories';

    protected static ?string $title = 'Riwayat Status';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('status')
                ->label('Status Baru')
                ->options([
                    'registrasi' => 'Registrasi (Belum Aktif)',
                    'aktif' => 'Aktif',
                    'cuti' => 'Cuti',
                    'nonaktif' => 'Nonaktif',
                    'lulus' => 'Lulus',
                    'mengundurkan_diri' => 'Mengundurkan Diri',
                    'dikeluarkan' => 'Dikeluarkan',
                    'alumni' => 'Alumni',
                ])
                ->required(),
            Textarea::make('notes')->label('Catatan'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status')
            ->columns([
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('changedBy.name')->label('Diubah oleh')->default('—'),
                TextColumn::make('notes')->label('Catatan')->limit(50),
                TextColumn::make('changed_at')->label('Waktu')->dateTime('d M Y H:i'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        $data['changed_by'] = auth()->id();
                        $data['changed_at'] = now();

                        return $data;
                    })
                    ->after(function ($record) {
                        $record->student->update(['student_status' => $record->status]);
                    }),
            ])
            ->actions([]);
    }
}
