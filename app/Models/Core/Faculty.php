<?php

namespace App\Models\Core;

use App\Models\Core\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Data Fakultas — lihat blueprint #erd. Companion table 1:1 untuk baris
 * `organizational_units` bertipe "fakultas" (lihat Unit::faculty()).
 */
class Faculty extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'organizational_unit_id',
        'institution_id',
        'code',
        'name',
        'dean_user_id',
        'status',
    ];

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'organizational_unit_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function dean(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dean_user_id');
    }

    public function studyPrograms(): HasMany
    {
        return $this->hasMany(StudyProgram::class);
    }
}
