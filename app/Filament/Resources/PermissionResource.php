<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Models\Core\Permission;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Core System';

    protected static ?string $modelLabel = 'Permission';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                TextInput::make('code')->required()->unique(ignoreRecord: true)->helperText('mis. akademik.nilai.edit'),
                TextInput::make('module')->label('Modul')->required()->helperText('Pengelompokan, mis. Akademik, Keuangan'),
                TextInput::make('name')->label('Nama')->required()->columnSpanFull(),
                Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('module')->label('Modul')->badge()->sortable(),
            ])
            ->filters([
                SelectFilter::make('module')->options(fn () => Permission::query()->distinct()->pluck('module', 'module')),
            ])
            ->defaultSort('module');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
