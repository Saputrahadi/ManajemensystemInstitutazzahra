<?php

namespace App\Livewire\Dosen\DosenWali;

use App\Models\Mahasiswa\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Bimbingan extends Component
{
    public Student $student;

    public string $advisingDate = '';

    public string $topic = '';

    public string $notes = '';

    public function mount(Student $student): void
    {
        $lecturer = Auth::user()->lecturer;

        abort_unless($student->advisor_id === $lecturer->id, 403, 'Mahasiswa ini bukan bimbingan Anda.');

        $this->student = $student;
        $this->advisingDate = now()->toDateString();
    }

    public function save(): void
    {
        $this->validate([
            'advisingDate' => ['required', 'date'],
            'topic' => ['required', 'string'],
        ]);

        $this->student->advisings()->create([
            'lecturer_id' => Auth::user()->lecturer->id,
            'advising_date' => $this->advisingDate,
            'topic' => $this->topic,
            'notes' => $this->notes,
        ]);

        $this->reset(['topic', 'notes']);
        $this->advisingDate = now()->toDateString();

        session()->flash('status', 'Catatan bimbingan tersimpan.');
    }

    public function render()
    {
        return view('livewire.dosen.dosen-wali.bimbingan', [
            'history' => $this->student->advisings()->get(),
        ]);
    }
}
