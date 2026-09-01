<?php

namespace App\Livewire\Dosen\Kelas;

use App\Models\Kelas\CourseClass;
use App\Models\Krs\KrsDetail;
use App\Models\Nilai\Grade;
use App\Models\Nilai\GradeComponent;
use App\Models\Nilai\GradeFinalization;
use App\Models\Perkuliahan\Assignment;
use App\Models\Perkuliahan\AssignmentSubmission;
use App\Models\Perkuliahan\Attendance;
use App\Models\Perkuliahan\ClassSession;
use App\Models\Perkuliahan\TeachingMaterial;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Halaman "Kelola Kelas" self-service Dosen — satu halaman, empat tab
 * (Presensi, Materi, Tugas, Nilai), meniru pola relation-manager-sebagai-tab
 * yang sudah dipakai di seluruh Filament resource sesi ini, tapi di sini
 * murni Livewire (bukan admin panel) supaya dosen bisa akses tanpa lewat /admin.
 */
#[Layout('components.layouts.app')]
class Show extends Component
{
    use WithFileUploads;

    public CourseClass $class;

    public string $activeTab = 'presensi';

    // --- Presensi ---
    public ?int $selectedSessionId = null;

    public int $meetingNumber = 1;

    public string $topic = '';

    public string $sessionDate = '';

    public array $attendanceStatuses = [];

    // --- Materi ---
    public ?int $materialSessionId = null;

    public string $materialTitle = '';

    public string $materialDescription = '';

    public $materialFile = null;

    // --- Tugas ---
    public string $assignmentTitle = '';

    public string $assignmentDescription = '';

    public string $assignmentDueDate = '';

    public int $maxScore = 100;

    public ?int $selectedAssignmentId = null;

    public array $submissionScores = [];

    public array $submissionFeedback = [];

    // --- Nilai ---
    public string $componentName = '';

    public int $componentWeight = 0;

    public ?int $selectedComponentId = null;

    public array $gradeScores = [];

    public function mount(CourseClass $class): void
    {
        $lecturer = Auth::user()->lecturer;

        abort_unless($class->lecturers()->where('lecturer_id', $lecturer->id)->exists(), 403, 'Anda bukan pengampu kelas ini.');

        $this->class = $class;
        $this->sessionDate = now()->toDateString();
    }

    public function getRosterProperty()
    {
        return KrsDetail::where('class_id', $this->class->id)
            ->with('krs.student.user')
            ->get()
            ->pluck('krs.student')
            ->filter()
            ->unique('id')
            ->values();
    }

    // --- Presensi ---

    public function createSession(): void
    {
        $this->validate([
            'meetingNumber' => ['required', 'integer', 'min:1'],
            'sessionDate' => ['required', 'date'],
        ]);

        $session = $this->class->sessions()->create([
            'meeting_number' => $this->meetingNumber,
            'topic' => $this->topic,
            'session_date' => $this->sessionDate,
            'status' => 'selesai',
        ]);

        $this->selectSession($session->id);
        $this->topic = '';
    }

    public function selectSession(int $sessionId): void
    {
        $this->selectedSessionId = $sessionId;

        $existing = Attendance::where('class_session_id', $sessionId)->pluck('status', 'student_id');

        $this->attendanceStatuses = $this->roster
            ->mapWithKeys(fn ($student) => [$student->id => $existing[$student->id] ?? 'hadir'])
            ->all();
    }

    public function saveAttendance(): void
    {
        foreach ($this->attendanceStatuses as $studentId => $status) {
            Attendance::updateOrCreate(
                ['class_session_id' => $this->selectedSessionId, 'student_id' => $studentId],
                ['status' => $status, 'recorded_at' => now()],
            );
        }

        session()->flash('status', 'Presensi tersimpan.');
    }

    // --- Materi ---

    public function addMaterial(): void
    {
        $this->validate([
            'materialSessionId' => ['required', 'exists:class_sessions,id'],
            'materialTitle' => ['required', 'string'],
            'materialFile' => ['nullable', 'file', 'max:10240'],
        ]);

        $path = $this->materialFile?->store('teaching-materials', 'local');

        TeachingMaterial::create([
            'class_session_id' => $this->materialSessionId,
            'title' => $this->materialTitle,
            'description' => $this->materialDescription,
            'file_path' => $path,
        ]);

        $this->reset(['materialTitle', 'materialDescription', 'materialFile']);
        session()->flash('status', 'Materi ditambahkan.');
    }

    // --- Tugas ---

    public function addAssignment(): void
    {
        $this->validate([
            'assignmentTitle' => ['required', 'string'],
            'maxScore' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $this->class->assignments()->create([
            'title' => $this->assignmentTitle,
            'description' => $this->assignmentDescription,
            'due_date' => $this->assignmentDueDate ?: null,
            'max_score' => $this->maxScore,
        ]);

        $this->reset(['assignmentTitle', 'assignmentDescription', 'assignmentDueDate']);
        $this->maxScore = 100;
        session()->flash('status', 'Tugas dibuat.');
    }

    public function selectAssignment(int $assignmentId): void
    {
        $this->selectedAssignmentId = $assignmentId;

        $submissions = AssignmentSubmission::where('assignment_id', $assignmentId)->get();
        $this->submissionScores = $submissions->pluck('score', 'student_id')->all();
        $this->submissionFeedback = $submissions->pluck('feedback', 'student_id')->all();
    }

    public function saveSubmissionGrade(int $studentId): void
    {
        AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $this->selectedAssignmentId, 'student_id' => $studentId],
            [
                'score' => $this->submissionScores[$studentId] ?? null,
                'feedback' => $this->submissionFeedback[$studentId] ?? null,
            ],
        );

        session()->flash('status', 'Nilai tugas tersimpan.');
    }

    // --- Nilai ---

    public function addComponent(): void
    {
        $this->validate([
            'componentName' => ['required', 'string'],
            'componentWeight' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $this->class->gradeComponents()->create([
            'name' => $this->componentName,
            'weight' => $this->componentWeight,
        ]);

        $this->reset(['componentName', 'componentWeight']);
        session()->flash('status', 'Komponen nilai ditambahkan.');
    }

    public function selectComponent(int $componentId): void
    {
        $this->selectedComponentId = $componentId;
        $this->gradeScores = Grade::where('grade_component_id', $componentId)->pluck('score', 'student_id')->all();
    }

    public function saveGrades(): void
    {
        foreach ($this->gradeScores as $studentId => $score) {
            if ($score === null || $score === '') {
                continue;
            }

            Grade::updateOrCreate(
                ['grade_component_id' => $this->selectedComponentId, 'student_id' => $studentId],
                ['score' => $score],
            );
        }

        session()->flash('status', 'Nilai tersimpan.');
    }

    public function finalizeGrade(int $studentId): void
    {
        $score = GradeFinalization::computeWeightedScore($this->class->id, $studentId);
        [$letter, $point] = GradeFinalization::scoreToScale($score);

        GradeFinalization::updateOrCreate(
            ['class_id' => $this->class->id, 'student_id' => $studentId],
            [
                'final_score' => (string) $score,
                'letter_grade' => $letter,
                'grade_point' => $point,
                'finalized_by' => Auth::id(),
                'finalized_at' => now(),
            ],
        );

        session()->flash('status', 'Nilai akhir difinalisasi.');
    }

    public function render()
    {
        return view('livewire.dosen.kelas.show', [
            'sessions' => $this->class->sessions()->orderByDesc('meeting_number')->get(),
            'materials' => TeachingMaterial::whereIn('class_session_id', $this->class->sessions()->pluck('id'))->with('classSession')->latest()->get(),
            'assignments' => $this->class->assignments()->latest()->get(),
            'gradeComponents' => $this->class->gradeComponents()->get(),
            'finalizations' => GradeFinalization::where('class_id', $this->class->id)->pluck('final_score', 'student_id'),
        ]);
    }
}
