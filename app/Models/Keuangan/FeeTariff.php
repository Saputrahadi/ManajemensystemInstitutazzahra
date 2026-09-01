<?php

namespace App\Models\Keuangan;

use App\Models\Core\Concerns\Auditable;
use App\Models\Core\StudyProgram;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Master Tarif — nominal per Jenis Tagihan, opsional dipersempit per Prodi + tahun akademik. */
class FeeTariff extends Model
{
    use Auditable;

    protected $fillable = ['fee_type_id', 'study_program_id', 'academic_year', 'amount'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }
}
