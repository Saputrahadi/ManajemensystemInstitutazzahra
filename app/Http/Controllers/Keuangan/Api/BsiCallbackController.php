<?php

namespace App\Http\Controllers\Keuangan\Api;

use App\Http\Controllers\Controller;
use App\Services\Keuangan\BsiCallbackService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/** Webhook inbound BSI — lihat App\Http\Middleware\VerifyBsiCallbackToken untuk autentikasinya. */
class BsiCallbackController extends Controller
{
    public function __invoke(Request $request, BsiCallbackService $service)
    {
        try {
            $validated = $request->validate([
                'virtual_account_number' => ['required', 'string'],
                'amount' => ['required', 'numeric'],
                'paid_at' => ['nullable', 'date'],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Payload tidak valid', 'errors' => $e->errors()], 422);
        }

        $callback = $service->handle($validated);

        return response()->json([
            'success' => $callback->processed,
            'data' => [
                'callback_id' => $callback->id,
                'processed' => $callback->processed,
                'error_message' => $callback->error_message,
            ],
        ]);
    }
}
