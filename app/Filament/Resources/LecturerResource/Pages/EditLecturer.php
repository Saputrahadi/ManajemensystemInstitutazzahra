<?php

namespace App\Filament\Resources\LecturerResource\Pages;

use App\Filament\Resources\LecturerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EditLecturer extends EditRecord
{
    protected static string $resource = LecturerResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['name'] = $this->record->user?->name;
        $data['email'] = $this->record->user?->email;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            $record->user?->update([
                'name' => $data['name'],
                'email' => $data['email'],
                ...(filled($data['password'] ?? null) ? ['password' => Hash::make($data['password'])] : []),
            ]);

            $record->update([
                'nidn_nidk' => $data['nidn_nidk'],
                'study_program_id' => $data['study_program_id'],
                'academic_rank' => $data['academic_rank'] ?? null,
                'employment_status' => $data['employment_status'],
                'status' => $data['status'],
            ]);

            return $record;
        });
    }
}
