<?php

namespace App\Models\Core;

use App\Models\Core\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Program Studi — lihat blueprint #erd. Companion table 1:1 untuk baris
 * `organizational_units` bertipe "prodi" (lihat Unit::studyProgram()).
 */
class StudyProgram extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'organizational_unit_id',
        'faculty_id',
        'code',
        'name',
        'degree_level',
        'head_user_id',
        'accreditation',
        'status',
    ];

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'organizational_unit_id');
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }
}
