<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentCallbackResource\Pages;
use App\Models\Keuangan\PaymentCallback;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Log mentah tiap callback masuk dari BSI — "Transaction Log, Error Handling" di blueprint modul BSI. Tanpa Create, hanya dibuat lewat Services\Keuangan\BsiCallbackService. */
class PaymentCallbackResource extends Resource
{
    protected static ?string $model = PaymentCallback::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $modelLabel = 'Log Callback BSI';

    protected static ?string $pluralModelLabel = 'Log Callback BSI';

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
                TextColumn::make('virtualAccount.va_number')->label('No. VA')->default('—'),
                TextColumn::make('payment.invoice.invoice_number')->label('No. Invoice')->default('—'),
                IconColumn::make('processed')->label('Diproses')->boolean(),
                TextColumn::make('error_message')->label('Pesan Error')->limit(50)->default('—'),
                TextColumn::make('received_at')->label('Diterima')->dateTime('d M Y H:i'),
            ])
            ->defaultSort('received_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentCallbacks::route('/'),
        ];
    }
}
