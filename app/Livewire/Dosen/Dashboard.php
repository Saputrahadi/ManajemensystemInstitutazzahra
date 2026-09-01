<?php

namespace App\Livewire\Dosen;

use App\Models\Kelas\Schedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/** Dashboard portal self-service Dosen — blueprint Prioritas 4 ("Sistem Dosen": Akun Dosen → Dashboard → ..."). */
#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $lecturer = Auth::user()->lecturer;

        $classes = $lecturer->classes()->with('courseOffering.course')->get();

        $todaySchedules = Schedule::whereIn('class_id', $classes->pluck('id'))
            ->where('day_of_week', now()->dayOfWeekIso)
            ->with('room', 'courseClass.courseOffering.course')
            ->orderBy('start_time')
            ->get();

        return view('livewire.dosen.dashboard', [
            'lecturer' => $lecturer,
            'totalClasses' => $classes->count(),
            'totalAdvisees' => $lecturer->advisees()->count(),
            'todaySchedules' => $todaySchedules,
        ]);
    }
}
