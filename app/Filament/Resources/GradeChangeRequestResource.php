<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeChangeRequestResource\Pages;
use App\Filament\Resources\GradeChangeRequestResource\RelationManagers\ApprovalsRelationManager;
use App\Models\Nilai\GradeChangeRequest;
use App\Models\Nilai\GradeFinalization;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Diajukan lewat tab Permintaan Koreksi di GradeFinalizationResource; diputuskan di sini lewat tab Persetujuan. */
class GradeChangeRequestResource extends Resource
{
    protected static ?string $model = GradeChangeRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static ?string $navigationGroup = 'Nilai';

    protected static ?string $modelLabel = 'Permintaan Koreksi Nilai';

    protected static ?string $pluralModelLabel = 'Permintaan Koreksi Nilai';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('grade_finalization_id')
                    ->label('Nilai Akhir')
                    ->options(fn () => GradeFinalization::with(['student', 'courseClass.courseOffering.course'])->get()
                        ->mapWithKeys(fn (GradeFinalization $finalization) => [
                            $finalization->id => "{$finalization->student->nim} - {$finalization->courseClass->courseOffering->course->code} ({$finalization->final_score})",
                        ]))
                    ->searchable()
                    ->required(),
                Select::make('requested_by')
                    ->label('Diajukan Oleh')
                    ->options(fn () => User::pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()
                    ->required(),
                TextInput::make('requested_score')->label('Nilai Diminta')->numeric()->required(),
                Select::make('requested_letter_grade')
                    ->label('Huruf Mutu Diminta')
                    ->options(['A' => 'A', 'AB' => 'AB', 'B' => 'B', 'BC' => 'BC', 'C' => 'C', 'CD' => 'CD', 'D' => 'D', 'E' => 'E'])
                    ->required(),
                Textarea::make('reason')->label('Alasan')->required()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gradeFinalization.student.nim')->label('NIM'),
                TextColumn::make('gradeFinalization.courseClass.courseOffering.course.code')->label('Kode MK'),
                TextColumn::make('requested_score')->label('Nilai Diminta'),
                TextColumn::make('requested_letter_grade')->label('Huruf Diminta'),
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
            'index' => Pages\ListGradeChangeRequests::route('/'),
            'create' => Pages\CreateGradeChangeRequest::route('/create'),
            'edit' => Pages\EditGradeChangeRequest::route('/{record}/edit'),
        ];
    }
}
