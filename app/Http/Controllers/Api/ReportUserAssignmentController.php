<?php

namespace App\Http\Controllers\Api;

use App\Enums\ActionReport;
use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\ReportStatusHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReportUserAssignmentController extends Controller
{
    public function handle(Request $request)
    {
        if (!$request->wantsJson()) {
            $request->headers->set('Accept', 'application/json');
        }
        $action = $request->input('action');

        return match ($action) {
            'DISPOSITION_REPORT' => $this->dispositionReport($request),
            'VERIFY_INITIAL' => $this->verifyInitial($request),
            'REQUEST_FURTHER_REVIEW' => $this->requestFurtherReview($request),
            'APPROVE_DISTRICT' => $this->approveDistrict($request),
            'REJECT_DISTRICT' => $this->rejectDistrict($request),
            'ASSIGN_FIELD_OFFICER' => $this->assignFieldOfficer($request),
            'SUBMIT_FIELD_RESULT' => $this->submitFieldResult($request),
            'REQUEST_REVISION' => $this->requestRevision($request),
            'COMPLETE_REPORT' => $this->completeReport($request),
            default => response()->json([
                'message' => 'Invalid action'
            ], 400),
        };
    }
    private function dispositionReport(Request $request)
    {
        $this->authorizeRole('DISTRICT_ADMIN');

        $validated = $request->validate([
            'report_id' => 'required|uuid|exists:reports,id',
            'opd_id' => 'required|uuid|exists:users,opd_id',
            'notes' => 'required|string',
        ]);

        $report = Report::findOrFail($validated['report_id']);

        $report->update([
            'current_opd_id' => $validated['opd_id'],
            'status' => ReportStatus::PENDING_VERIFICATION->value,
        ]);

        $this->createHistory(
            $report,
            ActionReport::DISPOSITION_REPORT->value,
            $validated['notes'],
            null,
            ReportStatus::PENDING_VERIFICATION->value,
        );

        return response()->json([
            'message' => 'Laporan telah diserahkan ke OPD',
        ]);
    }

    private function createHistory(
        Report $report,
        string $action,
        string $notes,
        array $attachments = null,
        ?string $status = null
    ) {
        return ReportStatusHistory::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(),
            'status' => $status ?? $report->current_status,
            'action' => $action,
            'notes' => $notes,
            'attachments' => $attachments,
        ]);
    }


    private function verifyInitial(Request $request)
    {
        $this->authorizeRole('OPD_ADMIN');

        $validated = $request->validate([
            'report_id' => 'required|uuid|exists:reports,id',
            'notes' => 'required|string',
        ]);

        $report = Report::findOrFail($validated['report_id']);
        $report->update(['status' => 'INITIAL_VERIFIED']);

        $this->createHistory($report, 'VERIFY_INITIAL', $validated['notes']);

        return response()->json([
            'message' => 'Report initial verification done'
        ]);
    }

    private function requestFurtherReview(Request $request)
    {
        $this->authorizeRole('OPD_ADMIN');

        $validated = $request->validate([
            'report_id' => 'required|uuid|exists:reports,id',
            'notes' => 'required|string',
        ]);

        $report = Report::findOrFail($validated['report_id']);
        $report->update([
            'status' => 'NEEDS_REVIEW'
        ]);

        $this->createHistory($report, 'REQUEST_FURTHER_REVIEW', $validated['notes']);

        return response()->json([
            'message' => 'Further review requested'
        ]);
    }

    private function approveDistrict(Request $request)
    {
        $this->authorizeRole('DISTRICT_ADMIN');

        $validated = $request->validate([
            'report_id' => 'required|uuid|exists:reports,id',
            'notes' => 'required|string',
        ]);

        $report = Report::findOrFail($validated['report_id']);
        $report->update(['status' => 'APPROVED_BY_DISTRICT']);

        $this->createHistory($report, 'APPROVE_DISTRICT', $validated['notes']);

        return response()->json([
            'message' => 'Report approved by district'
        ]);
    }

    private function rejectDistrict(Request $request)
    {
        $this->authorizeRole('DISTRICT_ADMIN');

        $validated = $request->validate([
            'report_id' => 'required|uuid|exists:reports,id',
            'notes' => 'required|string',
        ]);

        $report = Report::findOrFail($validated['report_id']);
        $report->update([
            'status' => 'REJECTED'
        ]);

        $this->createHistory($report, 'REJECT_DISTRICT', $validated['notes']);

        return response()->json([
            'message' => 'Report rejected by district'
        ]);
    }

    private function assignFieldOfficer(Request $request)
    {
        $this->authorizeRole('OPD_ADMIN');

        $validated = $request->validate([
            'report_id' => 'required|uuid|exists:reports,id',
            'officer_id' => 'required|uuid|exists:users,id',
            'notes' => 'required|string',
        ]);

        $officer = User::findOrFail($validated['officer_id']);
        if ($officer->role !== 'FIELD_OFFICER') {
            throw ValidationException::withMessages([
                'officer_id' => 'User is not a field officer'
            ]);
        }

        $report = Report::findOrFail($validated['report_id']);
        $report->update([
            'current_officer_id' => $validated['officer_id'],
            'status' => 'IN_PROGRESS',
        ]);

        $this->createHistory($report, 'ASSIGN_FIELD_OFFICER', $validated['notes']);

        return response()->json([
            'message' => 'Field officer assigned'
        ]);
    }

    private function submitFieldResult(Request $request)
    {
        $this->authorizeRole('FIELD_OFFICER');

        $validated = $request->validate([
            'report_id' => 'required|uuid|exists:reports,id',
            'attachments' => 'required|array|min:1',
            'attachments.*' => 'string',
            'notes' => 'required|string',
        ]);

        $report = Report::findOrFail($validated['report_id']);
        $report->update([
            'status' => 'FIELD_COMPLETED'
        ]);

        $this->createHistory($report, 'SUBMIT_FIELD_RESULT', $validated['notes'], $validated['attachments']);

        return response()->json([
            'message' => 'Field result submitted'
        ]);
    }

    private function requestRevision(Request $request)
    {
        $this->authorizeRole('QC_OFFICER');

        $validated = $request->validate([
            'report_id' => 'required|uuid|exists:reports,id',
            'notes' => 'required|string',
        ]);

        $report = Report::findOrFail($validated['report_id']);
        $report->update([
            'status' => 'NEEDS_REVISION'
        ]);

        $this->createHistory($report, 'REQUEST_REVISION', $validated['notes']);

        return response()->json([
            'message' => 'Revision requested'
        ]);
    }

    private function completeReport(Request $request)
    {
        $this->authorizeRole('QC_OFFICER');

        $validated = $request->validate([
            'report_id' => 'required|uuid|exists:reports,id',
            'notes' => 'required|string',
        ]);
        $report = Report::findOrFail($validated['report_id']);
        $report->update([
            'status' => 'COMPLETED'
        ]);

        $this->createHistory($report, 'COMPLETE_REPORT', $validated['notes']);

        return response()->json([
            'message' => 'Report completed'
        ]);
    }

    protected function authorizeRole($roles)
    {
        $user = auth()->user();

        $userRole = $user->role->value;

        $roles = (array) $roles;

        if (!in_array($userRole, $roles, true)) {
            abort(403, 'Unauthorized: role tidak sesuai');
        }
    }

}
