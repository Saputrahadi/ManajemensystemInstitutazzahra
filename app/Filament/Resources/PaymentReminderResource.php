<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentReminderResource\Pages;
use App\Models\Keuangan\PaymentReminder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Log reminder yang sudah dikirim — hanya dibuat lewat Services\Keuangan\PaymentReminderService (Artisan command keuangan:send-reminders), tanpa Create manual. */
class PaymentReminderResource extends Resource
{
    protected static ?string $model = PaymentReminder::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $modelLabel = 'Log Reminder';

    protected static ?string $pluralModelLabel = 'Log Reminder';

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
                TextColumn::make('invoice.invoice_number')->label('No. Invoice'),
                TextColumn::make('invoice.student.nim')->label('NIM'),
                TextColumn::make('reminder_type')->label('Jenis')->formatStateUsing(fn (string $state) => strtoupper($state)),
                TextColumn::make('channel')->label('Kanal'),
                TextColumn::make('sent_at')->label('Terkirim')->dateTime('d M Y H:i'),
            ])
            ->defaultSort('sent_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentReminders::route('/'),
        ];
    }
}
