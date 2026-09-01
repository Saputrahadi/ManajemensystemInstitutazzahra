<?php

namespace Tests\Feature\Core;

use App\Models\Core\AuditLog;
use App\Models\Core\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Definition of Done Fase 0: "setiap perubahan data tercatat di Audit Log".
 */
class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_unit_writes_an_audit_log_entry(): void
    {
        $unit = Unit::create(['code' => 'unit_baru', 'name' => 'Unit Baru', 'type' => 'unit']);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'created',
            'auditable_type' => Unit::class,
            'auditable_id' => $unit->id,
        ]);
    }

    public function test_updating_a_unit_records_before_and_after(): void
    {
        $unit = Unit::create(['code' => 'unit_lama', 'name' => 'Nama Lama', 'type' => 'unit']);

        $unit->update(['name' => 'Nama Baru']);

        $log = AuditLog::query()
            ->where('auditable_type', Unit::class)
            ->where('auditable_id', $unit->id)
            ->where('action', 'updated')
            ->latest('created_at')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame('Nama Lama', $log->before['name']);
        $this->assertSame('Nama Baru', $log->after['name']);
    }

    public function test_deleting_a_unit_writes_an_audit_log_entry(): void
    {
        $unit = Unit::create(['code' => 'unit_hapus', 'name' => 'Unit Dihapus', 'type' => 'unit']);
        $unitId = $unit->id;

        $unit->delete();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'deleted',
            'auditable_type' => Unit::class,
            'auditable_id' => $unitId,
        ]);
    }
}
