<?php

namespace App\Filament\Resources\GradeFinalizationResource\RelationManagers;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Diajukan di sini, diputuskan (approve/reject) lewat GradeChangeRequestResource — lihat catatan di sana. */
class ChangeRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'changeRequests';

    protected static ?string $title = 'Permintaan Koreksi';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('requested_by')
                ->label('Diajukan Oleh')
                ->options(fn () => User::pluck('name', 'id'))
                ->default(fn () => auth()->id())
                ->searchable()
                ->required(),
            Textarea::make('reason')->label('Alasan')->required()->columnSpanFull(),
            TextInput::make('requested_score')->label('Nilai Diminta')->numeric()->required(),
            Select::make('requested_letter_grade')
                ->label('Huruf Mutu Diminta')
                ->options(['A' => 'A', 'AB' => 'AB', 'B' => 'B', 'BC' => 'BC', 'C' => 'C', 'CD' => 'CD', 'D' => 'D', 'E' => 'E'])
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('requester.name')->label('Diajukan Oleh'),
                TextColumn::make('reason')->label('Alasan')->limit(40),
                TextColumn::make('requested_score')->label('Nilai Diminta'),
                TextColumn::make('requested_letter_grade')->label('Huruf Diminta'),
                BadgeColumn::make('status')->colors([
                    'gray' => 'menunggu',
                    'success' => 'disetujui',
                    'danger' => 'ditolak',
                ]),
            ])
            ->headerActions([CreateAction::make()]);
    }
}
