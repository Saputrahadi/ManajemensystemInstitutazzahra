<?php

namespace App\Livewire\Dosen\Kelas;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public function render()
    {
        $classes = Auth::user()->lecturer->classes()
            ->with('courseOffering.course')
            ->withCount('schedules')
            ->get();

        return view('livewire.dosen.kelas.index', ['classes' => $classes]);
    }
}
