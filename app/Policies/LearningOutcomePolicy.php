<?php

namespace App\Policies;

use App\Models\Kurikulum\LearningOutcome;
use App\Models\User;

class LearningOutcomePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('kurikulum.outcome.view');
    }

    public function view(User $user, LearningOutcome $learningOutcome): bool
    {
        return $user->hasPermission('kurikulum.outcome.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('kurikulum.outcome.manage');
    }

    public function update(User $user, LearningOutcome $learningOutcome): bool
    {
        return $user->hasPermission('kurikulum.outcome.manage');
    }

    public function delete(User $user, LearningOutcome $learningOutcome): bool
    {
        return $user->hasPermission('kurikulum.outcome.manage');
    }
}
