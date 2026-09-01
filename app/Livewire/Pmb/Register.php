<?php

namespace App\Livewire\Pmb;

use App\Models\Core\StudyProgram;
use App\Models\Pmb\AdmissionPath;
use App\Models\Pmb\AdmissionPeriod;
use App\Services\Pmb\ApplicantRegistrationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Form pendaftaran publik Calon Mahasiswa — awal alur PMB di blueprint
 * #intimahasiswa. Tidak butuh login; nomor pendaftaran diterbitkan begitu
 * form ini berhasil disubmit (lihat ApplicantRegistrationService).
 */
#[Layout('layouts.guest')]
class Register extends Component
{
    public ?int $admission_period_id = null;

    public ?int $admission_path_id = null;

    public ?int $study_program_id = null;

    public string $name = '';

    public string $nik = '';

    public string $gender = '';

    public string $birth_place = '';

    public string $birth_date = '';

    public string $address = '';

    public string $phone = '';

    public string $email = '';

    public string $parent_name = '';

    public string $parent_phone = '';

    public ?string $registrationNumber = null;

    protected function rules(): array
    {
        return [
            'admission_period_id' => ['required', 'exists:admission_periods,id'],
            'admission_path_id' => ['required', 'exists:admission_paths,id'],
            'study_program_id' => ['required', 'exists:study_programs,id'],
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:L,P'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'parent_phone' => ['nullable', 'string', 'max:30'],
        ];
    }

    public function register(ApplicantRegistrationService $service): void
    {
        $data = $this->validate();

        $applicant = $service->register($data);

        $this->registrationNumber = $applicant->registration_number;
    }

    public function render()
    {
        return view('livewire.pmb.register', [
            'periods' => AdmissionPeriod::where('status', 'aktif')->orderBy('name')->get(),
            'paths' => AdmissionPath::where('is_active', true)->orderBy('name')->get(),
            'studyPrograms' => StudyProgram::with('faculty')->where('status', 'aktif')->orderBy('name')->get(),
        ]);
    }
}
