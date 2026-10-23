<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperationalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OperationalRequestReviewController extends Controller
{
    public function approve(Request $request, OperationalRequest $operationalRequest)
    {
        return $this->review($request, $operationalRequest, 'approved');
    }

    public function reject(Request $request, OperationalRequest $operationalRequest)
    {
        return $this->review($request, $operationalRequest, 'rejected');
    }

    private function review(
        Request $request,
        OperationalRequest $operationalRequest,
        string $status
    ) {
        $validated = $request->validate([
            'admin_response' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use (
            $request,
            $operationalRequest,
            $status,
            $validated
        ) {
            $record = OperationalRequest::whereKey($operationalRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($record->status !== 'pending') {
                throw ValidationException::withMessages([
                    'request' => 'This request has already been reviewed.',
                ]);
            }

            $record->update([
                'status' => $status,
                'admin_response' => $validated['admin_response'] ?? null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.index', ['tab' => 'requests'])
            ->with('success', 'Operational request '.$status.' successfully.');
    }
}
