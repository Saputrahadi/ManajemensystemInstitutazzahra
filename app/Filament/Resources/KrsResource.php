<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KrsResource\Pages;
use App\Filament\Resources\KrsResource\RelationManagers\ApprovalsRelationManager;
use App\Filament\Resources\KrsResource\RelationManagers\DetailsRelationManager;
use App\Models\Krs\Krs;
use App\Models\Mahasiswa\Student;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KrsResource extends Resource
{
    protected static ?string $model = Krs::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'KRS';

    protected static ?string $modelLabel = 'KRS';

    protected static ?string $pluralModelLabel = 'KRS';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('student_id')
                    ->label('Mahasiswa')
                    ->options(fn () => Student::pluck('nim', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('academic_year')->label('Tahun Akademik')->required()->helperText('mis. 2026/2027'),
                Select::make('term')
                    ->label('Semester')
                    ->options(['ganjil' => 'Ganjil', 'genap' => 'Genap'])
                    ->required(),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'diajukan' => 'Diajukan',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ])
                    ->default('draft')
                    ->disabled(fn (string $context) => $context === 'edit')
                    ->dehydrated()
                    ->required()
                    ->helperText('Berubah otomatis lewat tab Persetujuan setelah KRS dibuat.'),
                TextInput::make('total_credits')->label('Total SKS')->numeric()->disabled()->dehydrated(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.nim')->label('NIM')->searchable(),
                TextColumn::make('student.user.name')->label('Nama'),
                TextColumn::make('academic_year')->label('Tahun Akademik'),
                TextColumn::make('term')->label('Semester'),
                TextColumn::make('total_credits')->label('Total SKS'),
                BadgeColumn::make('status')->colors([
                    'gray' => 'draft',
                    'warning' => 'diajukan',
                    'success' => 'disetujui',
                    'danger' => 'ditolak',
                ]),
            ])
            ->defaultSort('academic_year', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            DetailsRelationManager::class,
            ApprovalsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKrs::route('/'),
            'create' => Pages\CreateKrs::route('/create'),
            'edit' => Pages\EditKrs::route('/{record}/edit'),
        ];
    }
}
