<?php

namespace App\Filament\Resources\ApplicantResource\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Dokumen';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('document_type')->label('Jenis Dokumen')->required()->helperText('mis. ktp, kk, ijazah, foto'),
            FileUpload::make('file_path')->label('File')->disk('local')->directory('applicant-documents')->required(),
            TextInput::make('file_name')->label('Nama File')->required(),
            Textarea::make('notes')->label('Catatan')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('document_type')
            ->columns([
                TextColumn::make('document_type')->label('Jenis'),
                TextColumn::make('file_name')->label('File'),
                BadgeColumn::make('status')->colors([
                    'gray' => 'menunggu',
                    'success' => 'terverifikasi',
                    'danger' => 'ditolak',
                ]),
                TextColumn::make('verifier.name')->label('Diverifikasi oleh')->default('—'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([
                Action::make('verifikasi')
                    ->label('Terverifikasi')
                    ->icon('heroicon-o-check')
                    ->visible(fn ($record) => $record->status !== 'terverifikasi')
                    ->action(fn ($record) => $record->update([
                        'status' => 'terverifikasi',
                        'verified_by' => auth()->id(),
                        'verified_at' => now(),
                    ])),
                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== 'ditolak')
                    ->action(fn ($record) => $record->update([
                        'status' => 'ditolak',
                        'verified_by' => auth()->id(),
                        'verified_at' => now(),
                    ])),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
