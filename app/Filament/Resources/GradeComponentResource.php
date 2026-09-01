<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeComponentResource\Pages;
use App\Filament\Resources\GradeComponentResource\RelationManagers\GradesRelationManager;
use App\Models\Kelas\CourseClass;
use App\Models\Nilai\GradeComponent;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GradeComponentResource extends Resource
{
    protected static ?string $model = GradeComponent::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationGroup = 'Nilai';

    protected static ?string $modelLabel = 'Komponen Nilai';

    protected static ?string $pluralModelLabel = 'Komponen Nilai';

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
                TextInput::make('name')->label('Nama Komponen')->required()->helperText('mis. Tugas, UTS, UAS'),
                TextInput::make('weight')->label('Bobot (%)')->numeric()->minValue(0)->maxValue(100)->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('courseClass.courseOffering.course.code')->label('Kode MK'),
                TextColumn::make('courseClass.code')->label('Kelas'),
                TextColumn::make('name')->label('Komponen')->searchable(),
                TextColumn::make('weight')->label('Bobot (%)'),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            GradesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGradeComponents::route('/'),
            'create' => Pages\CreateGradeComponent::route('/create'),
            'edit' => Pages\EditGradeComponent::route('/{record}/edit'),
        ];
    }
}
