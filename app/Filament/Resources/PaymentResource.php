<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Keuangan\Payment;
use App\Services\Keuangan\PaymentVerificationService;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Tampilan global semua pembayaran lintas tagihan — pencatatan/verifikasi per-invoice tetap lewat tab Pembayaran di InvoiceResource. */
class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $modelLabel = 'Pembayaran';

    protected static ?string $pluralModelLabel = 'Pembayaran';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice.invoice_number')->label('No. Invoice')->searchable(),
                TextColumn::make('invoice.student.nim')->label('NIM'),
                TextColumn::make('amount_paid')->label('Nominal')->money('IDR'),
                TextColumn::make('payment_method')->label('Metode'),
                BadgeColumn::make('status')->colors([
                    'gray' => 'menunggu',
                    'success' => 'berhasil',
                    'danger' => 'gagal',
                ]),
                TextColumn::make('paid_at')->label('Waktu Bayar')->dateTime('d M Y H:i')->default('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('verifikasi')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-badge')
                    ->requiresConfirmation()
                    ->visible(fn (Payment $record) => $record->status !== 'berhasil')
                    ->action(fn (Payment $record) => app(PaymentVerificationService::class)->verify($record, auth()->id())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
        ];
    }
}
