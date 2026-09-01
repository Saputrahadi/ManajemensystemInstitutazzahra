<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers\ExtensionsRelationManager;
use App\Filament\Resources\InvoiceResource\RelationManagers\PaymentsRelationManager;
use App\Models\Keuangan\FeeType;
use App\Models\Keuangan\Invoice;
use App\Models\Mahasiswa\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Nomor invoice + Virtual Account dibuat otomatis lewat Services\Keuangan\InvoiceService — lihat Pages\CreateInvoice. */
class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $modelLabel = 'Tagihan';

    protected static ?string $pluralModelLabel = 'Tagihan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('student_id')
                    ->label('Mahasiswa')
                    ->options(fn () => Student::pluck('nim', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('fee_type_id')
                    ->label('Jenis Tagihan')
                    ->options(fn () => FeeType::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('academic_year')->label('Tahun Akademik')->required()->helperText('mis. 2026/2027'),
                Select::make('term')
                    ->label('Semester')
                    ->options(['ganjil' => 'Ganjil', 'genap' => 'Genap'])
                    ->helperText('Kosongkan untuk biaya sekali bayar (PMB, wisuda).'),
                TextInput::make('amount')->label('Nominal')->numeric()->prefix('Rp')->required(),
                DatePicker::make('due_date')->label('Jatuh Tempo')->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')->label('No. Invoice')->searchable(),
                TextColumn::make('student.nim')->label('NIM'),
                TextColumn::make('student.user.name')->label('Nama'),
                TextColumn::make('feeType.name')->label('Jenis Tagihan'),
                TextColumn::make('amount')->label('Nominal')->money('IDR'),
                TextColumn::make('due_date')->label('Jatuh Tempo')->date('d M Y'),
                TextColumn::make('virtualAccount.va_number')->label('No. VA')->default('—'),
                BadgeColumn::make('status')->colors([
                    'gray' => 'belum_bayar',
                    'success' => 'lunas',
                    'danger' => ['overdue', 'dibatalkan'],
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PaymentsRelationManager::class,
            ExtensionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
