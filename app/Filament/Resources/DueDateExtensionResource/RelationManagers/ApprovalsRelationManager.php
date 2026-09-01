<?php

namespace App\Filament\Resources\DueDateExtensionResource\RelationManagers;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Append-only — satu baris baru langsung menjalankan contoh "Human
 * Decision, System Execution" blueprint (lihat DueDateExtensionApproval::booted()):
 * ubah jatuh tempo Invoice + kirim notifikasi ke mahasiswa.
 */
class ApprovalsRelationManager extends RelationManager
{
    protected static string $relationship = 'approvals';

    protected static ?string $title = 'Persetujuan';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('approver_id')
                ->label('Penyetuju')
                ->options(fn () => User::pluck('name', 'id'))
                ->default(fn () => auth()->id())
                ->searchable()
                ->required(),
            Select::make('status')
                ->options(['disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'])
                ->required(),
            Textarea::make('notes')->label('Catatan')->columnSpanFull(),
            DateTimePicker::make('decided_at')->label('Waktu Keputusan')->default(now())->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('decided_at')->label('Waktu')->dateTime('d M Y H:i'),
                TextColumn::make('approver.name')->label('Penyetuju'),
                BadgeColumn::make('status')->colors([
                    'success' => 'disetujui',
                    'danger' => 'ditolak',
                ]),
                TextColumn::make('notes')->label('Catatan')->limit(50),
            ])
            ->headerActions([CreateAction::make()]);
    }
}
