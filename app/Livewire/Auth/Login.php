<?php

namespace App\Livewire\Auth;

use App\Models\Core\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Login Core System (bukan panel Filament — ini untuk pengguna non-admin,
 * mis. Dosen/Mahasiswa, begitu portal masing-masing dibangun di Fase 2+).
 * Rate limiting mengikuti prinsip Keamanan (#keamanan) — bukan opsional.
 */
#[Layout('layouts.guest')]
class Login extends Component
{
    public string $login = ''; // email atau username

    public string $password = '';

    public bool $remember = false;

    protected function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->validate();

        $throttleKey = Str::lower($this->login).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('login', "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.");

            return;
        }

        $field = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! Auth::attempt([$field => $this->login, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($throttleKey, 60);
            $this->addError('login', 'Kredensial tidak sesuai.');

            return;
        }

        RateLimiter::clear($throttleKey);
        $user = Auth::user();

        if ($user->status !== 'aktif') {
            Auth::logout();
            $this->addError('login', 'Akun Anda berstatus '.$user->status.'. Hubungi Admin.');

            return;
        }

        $user->forceFill(['last_login_at' => now(), 'last_login_ip' => request()->ip()])->saveQuietly();

        AuditLog::create([
            'user_id' => $user->id,
            'role_snapshot' => $user->roles()->pluck('code')->join(', '),
            'action' => 'login',
            'description' => "{$user->name} login ke sistem",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
