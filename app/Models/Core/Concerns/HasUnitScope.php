<?php

namespace App\Models\Core\Concerns;

use App\Models\Core\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait data-scoping — tempel pada model yang punya kolom unit_id (langsung
 * atau lewat relasi) dan yang aksesnya harus dibatasi per-Unit, sesuai contoh
 * blueprint: "Kaprodi Prodi A tidak bisa lihat data Prodi B".
 *
 * Pemakaian: Model::query()->visibleTo($user) — otomatis:
 *  - lolos semua baris jika user punya role institut-wide (Super Admin, Rektor, dst)
 *  - dibatasi ke unit_id yang di-scope ke user (+ seluruh unit turunan) jika tidak
 */
trait HasUnitScope
{
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasInstitutionWideRole()) {
            return $query;
        }

        $unitIds = $user->scopedUnitIds();

        // Sertakan unit turunan tiap unit yang di-scope (mis. Dekan atas Fakultas
        // otomatis mencakup semua Prodi di bawahnya).
        $allUnitIds = collect($unitIds)
            ->flatMap(function (int $unitId) {
                $unit = Unit::find($unitId);

                return $unit ? [$unitId, ...$unit->descendants()] : [$unitId];
            })
            ->unique()
            ->values()
            ->all();

        return $query->whereIn($this->unitScopeColumn(), $allUnitIds);
    }

    /** Override di model jika nama kolomnya bukan 'unit_id'. */
    protected function unitScopeColumn(): string
    {
        return 'unit_id';
    }
}
