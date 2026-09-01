<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassResource\Pages;
use App\Filament\Resources\ClassResource\RelationManagers\LecturersRelationManager;
use App\Filament\Resources\ClassResource\RelationManagers\SchedulesRelationManager;
use App\Models\Kelas\CourseClass;
use App\Models\Kelas\CourseOffering;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClassResource extends Resource
{
    protected static ?string $model = CourseClass::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Kelas & Jadwal';

    protected static ?string $modelLabel = 'Kelas';

    protected static ?string $pluralModelLabel = 'Kelas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('course_offering_id')
                    ->label('Pembukaan Mata Kuliah')
                    ->options(fn () => CourseOffering::with('course')->get()
                        ->mapWithKeys(fn (CourseOffering $offering) => [
                            $offering->id => "{$offering->course->code} — {$offering->academic_year} ({$offering->term})",
                        ]))
                    ->searchable()
                    ->required(),
                TextInput::make('code')->label('Kode Kelas')->required()->helperText('mis. A, B, C'),
                TextInput::make('capacity')->label('Kapasitas')->numeric(),
                Select::make('status')
                    ->options(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'])
                    ->default('aktif')
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('courseOffering.course.code')->label('Kode MK'),
                TextColumn::make('courseOffering.academic_year')->label('Tahun Akademik'),
                TextColumn::make('courseOffering.term')->label('Semester'),
                TextColumn::make('code')->label('Kelas')->searchable(),
                TextColumn::make('capacity')->label('Kapasitas'),
                BadgeColumn::make('status')->colors([
                    'success' => 'aktif',
                    'danger' => 'nonaktif',
                ]),
            ])
            ->defaultSort('code');
    }

    public static function getRelations(): array
    {
        return [
            LecturersRelationManager::class,
            SchedulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClasses::route('/'),
            'create' => Pages\CreateClass::route('/create'),
            'edit' => Pages\EditClass::route('/{record}/edit'),
        ];
    }
}
