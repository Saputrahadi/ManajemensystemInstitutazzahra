<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Core\Document;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Core System';

    protected static ?string $modelLabel = 'Dokumen';

    protected static ?string $pluralModelLabel = 'Dokumen';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                TextInput::make('title')->label('Judul')->required()->maxLength(255)->columnSpanFull(),
                TextInput::make('category')->label('Kategori')->helperText('mis. SK, Berkas PMB, Dokumen Mutu — bebas.'),
                DatePicker::make('expires_at')->label('Kadaluarsa')->helperText('Kosongkan jika tidak ada batas waktu.'),
                Toggle::make('is_public')->label('Publik'),
                FileUpload::make('file_path')
                    ->label('File')
                    ->disk('local')
                    ->directory('documents')
                    ->required(fn (?Document $record) => ! $record)
                    ->columnSpanFull()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state instanceof TemporaryUploadedFile) {
                            $set('file_name', $state->getClientOriginalName());
                            $set('mime_type', $state->getMimeType());
                            $set('size', $state->getSize());
                        }
                    }),
                Hidden::make('file_name'),
                Hidden::make('mime_type'),
                Hidden::make('size'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Judul')->searchable(),
                TextColumn::make('category')->label('Kategori')->badge(),
                TextColumn::make('uploader.name')->label('Diunggah oleh')->default('—'),
                TextColumn::make('size')->label('Ukuran')->formatStateUsing(fn (?int $state) => $state ? number_format($state / 1024, 1).' KB' : '—'),
                IconColumn::make('is_public')->label('Publik')->boolean(),
                TextColumn::make('expires_at')->label('Kadaluarsa')->date('d M Y')->default('—'),
                TextColumn::make('created_at')->label('Diunggah')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
