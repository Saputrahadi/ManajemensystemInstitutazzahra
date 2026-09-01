<?php

namespace App\Models;

use App\Models\Core\AuditLog;
use App\Models\Core\Concerns\Auditable;
use App\Models\Core\Concerns\HasUnitScope;
use App\Models\Core\Document;
use App\Models\Core\Role;
use App\Models\Core\Unit;
use App\Models\Dosen\Lecturer;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use Auditable, HasApiTokens, HasFactory, HasUnitScope, Notifiable, SoftDeletes;

    /** Kolom yang tidak pernah masuk ke before/after Audit Log (data sensitif). */
    protected array $auditExcept = ['password', 'remember_token', 'last_login_at', 'last_login_ip'];

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'unit_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- Relasi Core System -------------------------------------------------

    /** Unit utama/home unit pengguna. */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /** Semua role yang dimiliki (lintas unit), lengkap dengan pivot scope. */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot(['unit_id', 'assigned_at', 'assigned_by'])
            ->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /** Data Dosen kalau user ini punya akun dosen (lihat portal self-service Prioritas 4). */
    public function lecturer(): HasOne
    {
        return $this->hasOne(Lecturer::class);
    }

    // --- RBAC helpers --------------------------------------------------------

    /**
     * Cek apakah user punya role tertentu — opsional dibatasi ke unit tertentu.
     * unit_id null saat memeriksa berarti "role ini berlaku di unit manapun yang ditugaskan".
     */
    public function hasRole(string $roleCode, ?int $unitId = null): bool
    {
        return $this->roles()
            ->where('code', $roleCode)
            ->when($unitId !== null, fn ($q) => $q->wherePivot('unit_id', $unitId))
            ->exists();
    }

    /** Cek apakah user punya permission tertentu, lewat role manapun yang dimiliki. */
    public function hasPermission(string $permissionCode): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn ($q) => $q->where('code', $permissionCode))
            ->exists();
    }

    /**
     * Daftar unit_id tempat user memiliki role dengan scope eksplisit.
     * Dipakai untuk data-scoping query (mis. Kaprodi hanya lihat mahasiswa di
     * unit-unit ini). Baris dengan unit_id null (role institut-wide) tidak masuk —
     * cek hasRole($role, null)/isSuperScope() secara terpisah untuk itu.
     */
    public function scopedUnitIds(): array
    {
        return $this->roles()
            ->wherePivotNotNull('unit_id')
            ->get()
            ->pluck('pivot.unit_id')
            ->unique()
            ->values()
            ->all();
    }

    /** True jika user punya minimal satu role tanpa unit scope (berlaku institut-wide). */
    public function hasInstitutionWideRole(): bool
    {
        return $this->roles()->wherePivotNull('unit_id')->exists();
    }
}
