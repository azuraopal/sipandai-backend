<?php

namespace App\Http\Controllers\Api;

use App\Enums\ActionReport;
use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\ReportOpdAssignment;
use App\Models\ReportStatusHistory;
use App\Models\ReportUserAssignment;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Str;

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

        $opdAdminUser = User::where('opd_id', $validated['opd_id'])
            ->where('role', 'OPD_ADMIN')
            ->firstOrFail();

        $report->update([
            'current_opd_id' => $opdAdminUser->id,
            'status' => ReportStatus::PENDING_VERIFICATION->value,
        ]);

        $this->createHistory(
            $report,
            ActionReport::DISPOSITION_REPORT->value,
            $validated['notes'],
            null,
            ReportStatus::PENDING_VERIFICATION->value,
        );

        ReportOpdAssignment::create([
            'report_id' => $report->id,
            'opd_id' => $validated['opd_id'],
            'assigned_by' => Auth::id(),
            'assigned_at' => now(),
        ]);

        return response()->json([
            'message' => 'Laporan telah diserahkan ke OPD',
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

        $lastStatus = $this->getLatestStatus($report->id);

        if ($lastStatus !== ReportStatus::PENDING_VERIFICATION) {
            throw ValidationException::withMessages([
                'report_id' => ['Laporan hanya bisa diverifikasi jika status terakhir masih PENDING_VERIFICATION.'],
            ]);
        }

        if (
            !ReportOpdAssignment::where('report_id', $report->id)
                ->where('opd_id', Auth::user()->opd->id)
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'report_id' => ['Laporan tidak berada di OPD Anda.'],
            ]);
        }

        $report->update([
            'current_status' => ReportStatus::APPROVED->value,
        ]);

        $this->createHistory(
            $report,
            ActionReport::VERIFY_INITIAL->value,
            $validated['notes'],
            null,
            ReportStatus::APPROVED->value
        );

        return response()->json([
            'message' => 'Laporan verifikasi awal telah dilakukan dan disetujui'
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
            'officer_id' => [
                'required',
                'uuid',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'FIELD_OFFICER');
                }),
            ],
            'notes' => 'required|string',
        ]);

        $report = Report::findOrFail($validated['report_id']);

        $lastStatus = $this->getLatestStatus($report->id);

        if ($lastStatus === ReportStatus::PENDING_VERIFICATION) {
            throw ValidationException::withMessages([
                'report_id' => ['Laporan masih menunggu verifikasi, tidak bisa assign petugas lapangan.'],
            ]);
        }

        $assignment = ReportOpdAssignment::where('report_id', $report->id)
            ->where('opd_id', Auth::user()->opd->id)
            ->whereNull('ended_at')
            ->first();

        if (!$assignment) {
            throw ValidationException::withMessages([
                'report_id' => ['Laporan tidak berada di OPD Anda.'],
            ]);
        }

        $assignment->update([
            'ended_at' => now(),
        ]);

        $report->update([
            'current_officer_id' => $validated['officer_id'],
            'current_status' => ReportStatus::IN_PROGRESS->value,
        ]);

        ReportUserAssignment::create([
            'report_id' => $report->id,
            'officer_id' => $validated['officer_id'],
            'assigned_by' => Auth::id(),
            'assigned_at' => now(),
        ]);

        $this->createHistory(
            $report,
            ActionReport::ASSIGN_FIELD_OFFICER->value,
            $validated['notes'],
            null,
            ReportStatus::IN_PROGRESS->value
        );

        return response()->json([
            'message' => 'Petugas lapangan berhasil ditugaskan',
        ]);
    }

    private function submitFieldResult(Request $request)
    {
        $this->authorizeRole('FIELD_OFFICER');

        $validated = $request->validate([
            'report_id' => 'required|uuid|exists:reports,id',
            'attachments' => 'required',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes' => 'required|string',
        ]);

        $report = Report::findOrFail($validated['report_id']);

        $lastStatus = $this->getLatestStatus($report->id);

        if ($lastStatus !== ReportStatus::IN_PROGRESS) {
            throw ValidationException::withMessages([
                'report_id' => ['Field result hanya bisa disubmit jika status laporan sedang IN_PROGRESS.'],
            ]);
        }

        $report->update([
            'current_status' => ReportStatus::PENDING_QA_REVIEW->value,
        ]);

        ReportUserAssignment::where('report_id', $report->id)
            ->whereNull('ended_at')
            ->where('officer_id', Auth::id())
            ->update([
                'ended_at' => now(),
            ]);

        $files = $request->allFiles()['attachments'] ?? [];

        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            $path = $file->store('report_attachments', 'public');

            DB::table('report_attachments')->insert([
                'id' => Str::uuid()->toString(),
                'report_id' => $report->id,
                'purpose' => 'COMPLETION_PROFF',
                'file_url' => '/storage/' . $path,
                'file_type' => $file->getMimeType(),
                'created_at' => now(),
            ]);
        }

        $this->createHistory(
            $report,
            ActionReport::SUBMIT_FIELD_RESULT->value,
            $validated['notes'],
            null,
            ReportStatus::PENDING_QA_REVIEW->value
        );

        return response()->json([
            'success' => true,
            'message' => 'Field result submitted, attachments saved, and assignment closed',
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

        if ($report->current_status !== ReportStatus::PENDING_QA_REVIEW) {
            throw ValidationException::withMessages([
                'report_id' => [
                    'Report hanya bisa di-complete jika status laporan sedang PENDING_QA_REVIEW.'
                ],
            ]);
        }

        $oldStatus = $report->current_status->value;
        $newStatus = ReportStatus::COMPLETED->value;
        $update_at = now()->toDateTimeString();

        $report->update([
            'current_status' => $newStatus,
        ]);

        if ($report->user_id) {
            $userName = optional($report->user)->full_name;
        } else {
            $userName = 'Pelapor';
        }

        $this->createHistory(
            $report,
            ActionReport::COMPLETE_REPORT->value,
            $validated['notes'],
            null,
            $newStatus
        );

        try {
            $whatsappService = new WhatsAppService();

            $oldStatusLabel = ReportStatus::tryFrom($oldStatus)?->label() ?? $oldStatus;
            $newStatusLabel = ReportStatus::tryFrom($newStatus)?->label() ?? $newStatus;

            $trackingUrl = "https://sipandai.ashtrath.me/report/{$report->report_code}";

            $message = <<<EOT
            *[SIPANDAI] Pembaruan Status Laporan*

            Halo {$userName} 👋,
            Status laporan {$report->report_code} — "{$report->title}" berubah:
            {$oldStatusLabel} ➜ {$newStatusLabel} ({$update_at}).

            Catatan:
            > {$validated['notes']}

            Lihat detail & tindak lanjut:
            {$trackingUrl}

            Kode Laporan: *{$report->report_code}* . Simpan untuk cek status.
            EOT;

            if (!empty($report->phone_number)) {
                $whatsappService->sendMessage($report->phone_number, $message);
            }

        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi WhatsApp ke {$report->phone_number}: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'Report completed & notification sent'
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

    protected function authorizeRole(string|array $roles)
    {
        $user = auth()->user();

        $userRole = $user->role instanceof \BackedEnum
            ? $user->role->value
            : $user->role;

        if (is_string($roles)) {
            $roles = [$roles];
        }

        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized: role tidak sesuai');
        }
    }

    private function getLatestStatus(string $reportId): ?ReportStatus
    {
        $status = ReportStatusHistory::where('report_id', $reportId)
            ->orderByDesc('created_at')
            ->value('status');

        if ($status === null) {
            return null;
        }
        if ($status instanceof ReportStatus) {
            return $status;
        }

        return ReportStatus::from((string) $status);
    }
}
