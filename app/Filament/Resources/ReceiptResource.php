<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReceiptResource\Pages;
use App\Models\Keuangan\Receipt;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Tanpa Create — Kwitansi selalu diterbitkan otomatis lewat Services\Keuangan\PaymentVerificationService, tidak pernah diinput manual. */
class ReceiptResource extends Resource
{
    protected static ?string $model = Receipt::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $modelLabel = 'Kwitansi';

    protected static ?string $pluralModelLabel = 'Kwitansi';

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
                TextColumn::make('receipt_number')->label('No. Kwitansi')->searchable(),
                TextColumn::make('payment.invoice.invoice_number')->label('No. Invoice'),
                TextColumn::make('payment.invoice.student.nim')->label('NIM'),
                TextColumn::make('payment.amount_paid')->label('Nominal')->money('IDR'),
                TextColumn::make('issued_at')->label('Diterbitkan')->dateTime('d M Y H:i'),
            ])
            ->defaultSort('issued_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReceipts::route('/'),
        ];
    }
}
