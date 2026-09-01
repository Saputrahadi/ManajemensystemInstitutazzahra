<?php

namespace App\Livewire\Dosen\DosenWali;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Advisees extends Component
{
    public function render()
    {
        $advisees = Auth::user()->lecturer->advisees()->with('user', 'studyProgram')->get();

        return view('livewire.dosen.dosen-wali.advisees', ['advisees' => $advisees]);
    }
}
