<?php

namespace App\Filament\Resources\KrsResource\RelationManagers;

use App\Models\Kelas\CourseClass;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Kelas yang diambil dalam KRS ini — `credits` di-snapshot otomatis dari Course lewat KrsDetail::booted(), tidak diinput manual. */
class DetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    protected static ?string $title = 'Kelas Diambil';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('class_id')
                ->label('Kelas')
                ->options(fn () => CourseClass::with('courseOffering.course')->get()
                    ->mapWithKeys(fn (CourseClass $class) => [
                        $class->id => "{$class->courseOffering->course->code} - {$class->code} ({$class->courseOffering->course->credits} SKS)",
                    ]))
                ->searchable()
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('courseClass.courseOffering.course.code')->label('Kode MK'),
                TextColumn::make('courseClass.courseOffering.course.name')->label('Mata Kuliah'),
                TextColumn::make('courseClass.code')->label('Kelas'),
                TextColumn::make('credits')->label('SKS'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([DeleteAction::make()]);
    }
}
