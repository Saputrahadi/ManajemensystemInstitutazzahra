<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Core\Unit;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Core System';

    protected static ?string $modelLabel = 'Pengguna';

    /** Data-scoping juga diterapkan di panel admin — bukan hanya API. */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->visibleTo(auth()->user());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Identitas')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->label('Nama')->required()->maxLength(255),
                    TextInput::make('username')->label('Username')->unique(ignoreRecord: true)->maxLength(255),
                    TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true),
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->dehydrateStateUsing(fn (?string $state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn (?string $state) => filled($state))
                        ->required(fn (string $context) => $context === 'create'),
                ]),
            Section::make('Organisasi & Status')
                ->columns(2)
                ->schema([
                    Select::make('unit_id')
                        ->label('Unit Utama')
                        ->options(fn () => Unit::query()->where('is_active', true)->pluck('name', 'id'))
                        ->searchable(),
                    Select::make('status')
                        ->options(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'suspend' => 'Suspend'])
                        ->default('aktif')
                        ->required(),
                    Select::make('roles')
                        ->label('Role')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('unit.name')->label('Unit')->badge(),
                TextColumn::make('roles.name')->label('Role')->badge()->separator(','),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'aktif',
                        'gray' => 'nonaktif',
                        'danger' => 'suspend',
                    ]),
                TextColumn::make('last_login_at')->label('Login Terakhir')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'suspend' => 'Suspend']),
                SelectFilter::make('unit_id')->label('Unit')->relationship('unit', 'name'),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
