<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseOfferingResource\Pages;
use App\Filament\Resources\CourseOfferingResource\RelationManagers\ClassesRelationManager;
use App\Models\Kelas\CourseOffering;
use App\Models\Kurikulum\Course;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CourseOfferingResource extends Resource
{
    protected static ?string $model = CourseOffering::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Kelas & Jadwal';

    protected static ?string $modelLabel = 'Pembukaan Mata Kuliah';

    protected static ?string $pluralModelLabel = 'Pembukaan Mata Kuliah';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                Select::make('course_id')
                    ->label('Mata Kuliah')
                    ->options(fn () => Course::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('academic_year')->label('Tahun Akademik')->required()->helperText('mis. 2026/2027'),
                Select::make('term')
                    ->label('Semester')
                    ->options(['ganjil' => 'Ganjil', 'genap' => 'Genap'])
                    ->required(),
                TextInput::make('quota')->label('Kuota')->numeric(),
                Select::make('status')
                    ->options(['dibuka' => 'Dibuka', 'ditutup' => 'Ditutup'])
                    ->default('dibuka')
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.code')->label('Kode MK'),
                TextColumn::make('course.name')->label('Mata Kuliah')->searchable(),
                TextColumn::make('academic_year')->label('Tahun Akademik'),
                TextColumn::make('term')->label('Semester'),
                TextColumn::make('quota')->label('Kuota'),
                BadgeColumn::make('status')->colors([
                    'success' => 'dibuka',
                    'gray' => 'ditutup',
                ]),
            ])
            ->defaultSort('academic_year', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ClassesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseOfferings::route('/'),
            'create' => Pages\CreateCourseOffering::route('/create'),
            'edit' => Pages\EditCourseOffering::route('/{record}/edit'),
        ];
    }
}
