<?php

namespace App\Filament\Resources\LecturerResource\Pages;

use App\Filament\Resources\LecturerResource;
use App\Models\Core\Role;
use App\Models\Dosen\Lecturer;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateLecturer extends CreateRecord
{
    protected static string $resource = LecturerResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]);

            if ($dosenRole = Role::where('code', 'dosen')->first()) {
                $user->roles()->attach($dosenRole->id, ['unit_id' => null, 'assigned_at' => now()]);
            }

            return Lecturer::create([
                'user_id' => $user->id,
                'nidn_nidk' => $data['nidn_nidk'],
                'study_program_id' => $data['study_program_id'],
                'academic_rank' => $data['academic_rank'] ?? null,
                'employment_status' => $data['employment_status'],
                'status' => $data['status'],
            ]);
        });
    }
}
