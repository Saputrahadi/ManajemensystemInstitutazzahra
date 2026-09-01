<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeFinalizationResource\Pages;
use App\Filament\Resources\GradeFinalizationResource\RelationManagers\ChangeRequestsRelationManager;
use App\Models\Kelas\CourseClass;
use App\Models\Mahasiswa\Student;
use App\Models\Nilai\GradeFinalization;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * `final_score`/`letter_grade`/`grade_point` sengaja tidak ada di form —
 * dihitung otomatis lewat `GradeFinalization::booted()` saat baris dibuat.
 * Koreksi setelahnya lewat tab Permintaan Koreksi, bukan edit langsung.
 */
class GradeFinalizationResource extends Resource
{
    protected static ?string $model = GradeFinalization::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Nilai';

    protected static ?string $modelLabel = 'Nilai Akhir';

    protected static ?string $pluralModelLabel = 'Nilai Akhir';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('class_id')
                    ->label('Kelas')
                    ->options(fn () => CourseClass::with('courseOffering.course')->get()
                        ->mapWithKeys(fn (CourseClass $class) => [
                            $class->id => "{$class->courseOffering->course->code} - {$class->code} ({$class->courseOffering->academic_year})",
                        ]))
                    ->searchable()
                    ->required(),
                Select::make('student_id')
                    ->label('Mahasiswa')
                    ->options(fn () => Student::pluck('nim', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('finalized_by')
                    ->label('Difinalisasi Oleh')
                    ->options(fn () => User::pluck('name', 'id'))
                    ->default(fn () => auth()->id())
                    ->searchable()
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.nim')->label('NIM'),
                TextColumn::make('student.user.name')->label('Nama'),
                TextColumn::make('courseClass.courseOffering.course.code')->label('Kode MK'),
                TextColumn::make('courseClass.code')->label('Kelas'),
                TextColumn::make('final_score')->label('Nilai Akhir'),
                TextColumn::make('letter_grade')->label('Huruf Mutu'),
                TextColumn::make('grade_point')->label('Angka Mutu'),
            ])
            ->actions([
                Action::make('hitung_ulang')
                    ->label('Hitung Ulang')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->action(function (GradeFinalization $record) {
                        $score = GradeFinalization::computeWeightedScore($record->class_id, $record->student_id);
                        [$letter, $point] = GradeFinalization::scoreToScale($score);
                        $record->update(['final_score' => (string) $score, 'letter_grade' => $letter, 'grade_point' => $point]);
                    }),
            ])
            ->defaultSort('final_score', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ChangeRequestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGradeFinalizations::route('/'),
            'create' => Pages\CreateGradeFinalization::route('/create'),
            'edit' => Pages\EditGradeFinalization::route('/{record}/edit'),
        ];
    }
}
