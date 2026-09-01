<?php

namespace App\Filament\Resources\ApplicantResource\RelationManagers;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InterviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'interviews';

    protected static ?string $title = 'Wawancara';

    public function form(Form $form): Form
    {
        return $form->schema([
            DateTimePicker::make('scheduled_at')->label('Jadwal')->required(),
            TextInput::make('location')->label('Lokasi'),
            Select::make('interviewer_user_id')->label('Pewawancara')->options(fn () => User::pluck('name', 'id')),
            Select::make('status')
                ->options(['dijadwalkan' => 'Dijadwalkan', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'])
                ->default('dijadwalkan')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('scheduled_at')->label('Jadwal')->dateTime('d M Y H:i'),
                TextColumn::make('interviewer.name')->label('Pewawancara')->default('—'),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('result.score')->label('Nilai')->default('—'),
                TextColumn::make('result.recommendation')->label('Rekomendasi')->default('—'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([
                Action::make('isiHasil')
                    ->label('Isi Hasil')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->form([
                        TextInput::make('score')->label('Nilai')->numeric()->step(0.01),
                        Select::make('recommendation')
                            ->label('Rekomendasi')
                            ->options(['lulus' => 'Lulus', 'tidak_lulus' => 'Tidak Lulus', 'pertimbangan' => 'Pertimbangan']),
                        Textarea::make('notes')->label('Catatan'),
                    ])
                    ->fillForm(fn ($record) => $record->result?->only(['score', 'recommendation', 'notes']) ?? [])
                    ->action(function ($record, array $data) {
                        $record->result()->updateOrCreate([], $data);
                        $record->update(['status' => 'selesai']);
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
