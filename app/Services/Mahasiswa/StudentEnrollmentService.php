<?php

namespace App\Services\Mahasiswa;

use App\Models\Core\Role;
use App\Models\Mahasiswa\Student;
use App\Models\Pmb\ReRegistration;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Konversi Applicant (PMB) → Student (Mahasiswa) — lihat blueprint
 * #intimahasiswa: "Applicant → Student → User — dikonversi otomatis, tidak
 * diinput ulang" (Single Source of Truth). Dipicu setelah ReRegistration
 * berstatus selesai (NIM sudah terbit).
 *
 * Status awal `registrasi` (bukan langsung `aktif`) — blueprint menegaskan
 * status Aktif baru menyusul setelah UKT lunas, dan domain Keuangan/UKT
 * belum dibangun di sini.
 */
class StudentEnrollmentService
{
    public function enroll(ReRegistration $reRegistration): Student
    {
        if ($reRegistration->status !== 'selesai' || ! $reRegistration->nim) {
            throw new \RuntimeException('Registrasi ulang belum selesai — NIM belum terbit.');
        }

        if ($reRegistration->student_id) {
            return $reRegistration->student;
        }

        return DB::transaction(function () use ($reRegistration) {
            $applicant = $reRegistration->applicant;

            $user = User::create([
                'name' => $applicant->name,
                'username' => $reRegistration->nim,
                'email' => $applicant->email ?: $reRegistration->nim.'@mahasiswa.az-zahra.ac.id',
                'password' => Hash::make(Str::random(16)),
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]);

            if ($mahasiswaRole = Role::where('code', 'mahasiswa')->first()) {
                $user->roles()->attach($mahasiswaRole->id, ['unit_id' => null, 'assigned_at' => now()]);
            }

            $student = Student::create([
                'user_id' => $user->id,
                'applicant_id' => $applicant->id,
                'nim' => $reRegistration->nim,
                'study_program_id' => $applicant->study_program_id,
                'admission_year' => $applicant->admissionPeriod->academic_year,
                'student_status' => 'registrasi',
                'entry_path' => $applicant->admissionPath->name,
                'enrollment_date' => $reRegistration->registered_at?->toDateString() ?? now()->toDateString(),
            ]);

            $student->profile()->create([
                'gender' => $applicant->gender,
                'birth_place' => $applicant->birth_place,
                'birth_date' => $applicant->birth_date,
                'phone' => $applicant->phone,
                'email' => $applicant->email,
            ]);

            if ($applicant->address) {
                $student->addresses()->create([
                    'address_type' => 'domisili',
                    'address' => $applicant->address,
                ]);
            }

            if ($applicant->parent_name) {
                $student->families()->create([
                    'relation_type' => 'wali',
                    'name' => $applicant->parent_name,
                    'phone' => $applicant->parent_phone,
                ]);
            }

            $student->statusHistories()->create([
                'status' => 'registrasi',
                'notes' => 'Dibuat otomatis dari registrasi ulang PMB.',
                'changed_at' => now(),
            ]);

            $reRegistration->update(['student_id' => $student->id]);

            return $student;
        });
    }
}
