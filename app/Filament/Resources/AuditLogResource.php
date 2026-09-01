<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\Core\AuditLog;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Audit Log — read-only sesuai AuditLogPolicy (create/update/delete selalu
 * false). Tidak ada form(), tidak ada aksi Create/Edit/Delete.
 */
class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Core System';

    protected static ?string $modelLabel = 'Audit Log';

    protected static ?string $navigationLabel = 'Audit Log';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i:s')->sortable(),
                TextColumn::make('user.name')->label('Pengguna')->default('Sistem'),
                TextColumn::make('role_snapshot')->label('Role'),
                BadgeColumn::make('action')
                    ->colors([
                        'success' => 'created',
                        'warning' => 'updated',
                        'danger' => 'deleted',
                        'info' => fn ($state) => in_array($state, ['login', 'approved']),
                    ]),
                TextColumn::make('auditable_type')->label('Entitas')->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '—'),
                TextColumn::make('auditable_id')->label('ID'),
                TextColumn::make('description')->label('Keterangan')->wrap(),
                TextColumn::make('ip_address')->label('IP'),
            ])
            ->filters([
                SelectFilter::make('action')->options([
                    'created' => 'Created',
                    'updated' => 'Updated',
                    'deleted' => 'Deleted',
                    'login' => 'Login',
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
