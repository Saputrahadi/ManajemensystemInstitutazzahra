<?php

use App\Livewire\Pmb\Register;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PMB — web routes
|--------------------------------------------------------------------------
| Pendaftaran publik, tidak butuh login — lihat blueprint #intimahasiswa.
*/

Route::get('/pmb/daftar', Register::class)->name('pmb.register');
