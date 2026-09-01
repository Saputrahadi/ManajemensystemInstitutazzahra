<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Services\Keuangan\PaymentVerificationService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Entri manual untuk pembayaran non-VA (mis. tunai/transfer manual di
 * luar BSI) — pembayaran lewat VA BSI masuk otomatis lewat
 * Services\Keuangan\BsiCallbackService, bukan lewat form ini. Aksi
 * "Verifikasi" memanggil PaymentVerificationService yang sama dengan
 * yang dipakai callback BSI, supaya kedua jalur konsisten.
 */
class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Pembayaran';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('amount_paid')->label('Nominal Dibayar')->numeric()->prefix('Rp')->required(),
            DateTimePicker::make('paid_at')->label('Waktu Bayar')->default(now())->required(),
            Select::make('payment_method')
                ->label('Metode')
                ->options(['manual' => 'Manual (Tunai/Transfer)', 'va_bsi' => 'VA BSI'])
                ->default('manual')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount_paid')->label('Nominal')->money('IDR'),
                TextColumn::make('payment_method')->label('Metode'),
                BadgeColumn::make('status')->colors([
                    'gray' => 'menunggu',
                    'success' => 'berhasil',
                    'danger' => 'gagal',
                ]),
                TextColumn::make('paid_at')->label('Waktu Bayar')->dateTime('d M Y H:i')->default('—'),
                TextColumn::make('verifier.name')->label('Diverifikasi Oleh')->default('—'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([
                Action::make('verifikasi')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-badge')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status !== 'berhasil')
                    ->action(fn ($record) => app(PaymentVerificationService::class)->verify($record, auth()->id())),
            ]);
    }
}
