<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Pengajuan perpanjangan jatuh tempo — keputusan (approve/reject) di App\Filament\Resources\DueDateExtensionResource. */
class ExtensionsRelationManager extends RelationManager
{
    protected static string $relationship = 'extensions';

    protected static ?string $title = 'Perpanjangan Jatuh Tempo';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('requested_by')
                ->label('Diajukan Oleh')
                ->options(fn () => User::pluck('name', 'id'))
                ->default(fn () => auth()->id())
                ->searchable()
                ->required(),
            DatePicker::make('requested_due_date')->label('Jatuh Tempo Baru yang Diminta')->required(),
            Textarea::make('reason')->label('Alasan')->required()->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('requester.name')->label('Diajukan Oleh'),
                TextColumn::make('requested_due_date')->label('Diminta Sampai')->date('d M Y'),
                TextColumn::make('reason')->label('Alasan')->limit(40),
                BadgeColumn::make('status')->colors([
                    'gray' => 'menunggu',
                    'success' => 'disetujui',
                    'danger' => 'ditolak',
                ]),
            ])
            ->headerActions([CreateAction::make()]);
    }
}
