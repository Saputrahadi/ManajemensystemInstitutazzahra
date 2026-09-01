<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DueDateExtensionResource\Pages;
use App\Filament\Resources\DueDateExtensionResource\RelationManagers\ApprovalsRelationManager;
use App\Models\Keuangan\DueDateExtension;
use App\Models\Keuangan\Invoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Diajukan lewat tab Perpanjangan Jatuh Tempo di InvoiceResource; diputuskan di sini lewat tab Persetujuan. */
class DueDateExtensionResource extends Resource
{
    protected static ?string $model = DueDateExtension::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $modelLabel = 'Perpanjangan Jatuh Tempo';

    protected static ?string $pluralModelLabel = 'Perpanjangan Jatuh Tempo';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('invoice_id')
                    ->label('Tagihan')
                    ->options(fn () => Invoice::with('student')->get()
                        ->mapWithKeys(fn (Invoice $invoice) => [
                            $invoice->id => "{$invoice->invoice_number} - {$invoice->student->nim}",
                        ]))
                    ->searchable()
                    ->required(),
                DatePicker::make('requested_due_date')->label('Jatuh Tempo Baru yang Diminta')->required(),
                Textarea::make('reason')->label('Alasan')->required()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice.invoice_number')->label('No. Invoice'),
                TextColumn::make('invoice.student.nim')->label('NIM'),
                TextColumn::make('requested_due_date')->label('Diminta Sampai')->date('d M Y'),
                BadgeColumn::make('status')->colors([
                    'gray' => 'menunggu',
                    'success' => 'disetujui',
                    'danger' => 'ditolak',
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ApprovalsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDueDateExtensions::route('/'),
            'create' => Pages\CreateDueDateExtension::route('/create'),
            'edit' => Pages\EditDueDateExtension::route('/{record}/edit'),
        ];
    }
}
