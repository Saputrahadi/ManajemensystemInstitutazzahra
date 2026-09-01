<?php

namespace App\Livewire\Dosen;

use App\Models\Kelas\Schedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Jadwal extends Component
{
    private const DAYS = [
        1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
        5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu',
    ];

    public function render()
    {
        $lecturer = Auth::user()->lecturer;

        $schedules = Schedule::whereIn('class_id', $lecturer->classes()->pluck('classes.id'))
            ->with('room', 'courseClass.courseOffering.course')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        return view('livewire.dosen.jadwal', [
            'schedulesByDay' => $schedules,
            'days' => self::DAYS,
        ]);
    }
}
