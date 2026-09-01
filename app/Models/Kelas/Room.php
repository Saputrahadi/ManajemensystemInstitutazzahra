<?php

namespace App\Models\Kelas;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use Auditable;

    protected $fillable = ['code', 'name', 'building', 'capacity', 'status'];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
