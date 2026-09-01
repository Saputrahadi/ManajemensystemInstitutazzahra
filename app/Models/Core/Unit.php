<?php

namespace App\Models\Core;

use App\Models\Core\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Unit Organisasi — lihat blueprint #unit & #erd. Tree self-referencing; type
 * adalah string bebas (institut, fakultas, prodi, biro, ukm, dst.) supaya unit
 * baru bisa ditambah lewat data, bukan lewat migrasi/kode baru.
 *
 * Nama tabelnya `organizational_units` (sesuai ERD blueprint), tapi nama class
 * tetap `Unit` — dipakai luas di seluruh RBAC (HasUnitScope, dst.), mengubah
 * nama class akan jauh lebih mahal daripada manfaatnya dibanding rename tabel.
 * Baris institut/fakultas/prodi punya detail tambahan di tabel pendamping
 * (institution()/faculty()/studyProgram() di bawah).
 */
class Unit extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'organizational_units';

    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'type',
        'description',
        'order_index',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Unit::class, 'parent_id')->orderBy('order_index');
    }

    /** Seluruh keturunan (rekursif) — dipakai untuk data-scoping berjenjang, mis. Dekan atas semua Prodi di Fakultasnya. */
    public function descendants(): array
    {
        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->descendants());
        }

        return $ids;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function roleAssignments(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')->withPivot(['role_id']);
    }

    public function institution(): HasOne
    {
        return $this->hasOne(Institution::class);
    }

    public function faculty(): HasOne
    {
        return $this->hasOne(Faculty::class);
    }

    public function studyProgram(): HasOne
    {
        return $this->hasOne(StudyProgram::class);
    }
}
