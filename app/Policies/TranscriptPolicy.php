<?php

namespace App\Policies;

use App\Models\HasilAkademik\Transcript;
use App\Models\User;

class TranscriptPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('hasil_akademik.transcript.manage');
    }

    public function view(User $user, Transcript $transcript): bool
    {
        return $user->hasPermission('hasil_akademik.transcript.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('hasil_akademik.transcript.manage');
    }

    public function update(User $user, Transcript $transcript): bool
    {
        return $user->hasPermission('hasil_akademik.transcript.manage');
    }

    public function delete(User $user, Transcript $transcript): bool
    {
        return $user->hasPermission('hasil_akademik.transcript.manage');
    }
}
