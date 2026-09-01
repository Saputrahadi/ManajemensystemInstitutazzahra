<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Keuangan\FeeType;
use App\Models\Mahasiswa\Student;
use App\Services\Keuangan\InvoiceService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(InvoiceService::class)->createForStudent(
            Student::findOrFail($data['student_id']),
            FeeType::findOrFail($data['fee_type_id']),
            $data['academic_year'],
            $data['term'] ?? null,
            (float) $data['amount'],
            $data['due_date'],
        );
    }
}
