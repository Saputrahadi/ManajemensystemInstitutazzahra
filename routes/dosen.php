<?php

use App\Livewire\Dosen\Dashboard;
use App\Livewire\Dosen\DosenWali\Advisees;
use App\Livewire\Dosen\DosenWali\Bimbingan;
use App\Livewire\Dosen\Jadwal;
use App\Livewire\Dosen\Kelas\Index as KelasIndex;
use App\Livewire\Dosen\Kelas\Show as KelasShow;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dosen — portal self-service (blueprint Prioritas 4: "Sistem Dosen")
|--------------------------------------------------------------------------
| Bukan panel Filament — ini Livewire murni untuk dosen login sendiri,
| digate middleware `lecturer` (role dosen + baris Lecturer ada), beda
| dari middleware `admin`/`permission:` yang menggerbangi /admin.
*/

Route::middleware(['auth', 'lecturer'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/jadwal', Jadwal::class)->name('jadwal');
    Route::get('/kelas', KelasIndex::class)->name('kelas.index');
    Route::get('/kelas/{class}', KelasShow::class)->name('kelas.show');
    Route::get('/wali', Advisees::class)->name('wali.index');
    Route::get('/wali/{student}', Bimbingan::class)->name('wali.show');
});
