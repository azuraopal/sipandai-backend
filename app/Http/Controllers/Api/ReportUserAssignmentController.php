<?php

namespace App\Http\Controllers\Api;

use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\ReportAssignment;
use App\Models\ReportUserAssignment;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReportUserAssignmentController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_id' => 'required|uuid|exists:reports,id',
            'officer_id' => [
                'required',
                'uuid',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', UserRole::FIELD_OFFICER->value);
                }),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data' => null,
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        $admin = $request->user();
        $officer = User::find($validatedData['officer_id']);

        $this->authorize('create', [ReportUserAssignment::class, $officer]);

        try {
            if ($admin->role->value === UserRole::OPD_ADMIN->value && $admin->opd_id !== $officer->opd_id) {
                return response()->json([
                    'message' => 'Anda tidak memiliki izin untuk menugaskan laporan kepada petugas dari OPD lain.',
                    'data' => null,
                ], 403);
            }

            $assignment = DB::transaction(function () use ($request, $admin) {
                $assignment = ReportUserAssignment::create([
                    'report_id' => $request->report_id,
                    'officer_id' => $request->officer_id,
                    'assigned_by' => $admin->id,
                ]);

                $assignment->report()->update([
                    'current_status' => ReportStatus::IN_PROGRESS,
                    'current_officer_id' => $request->officer_id,
                ]);

                $assignment->report->statusHistories()->create([
                    'user_id' => $admin->id,
                    'status' => ReportStatus::IN_PROGRESS,
                    'notes' => 'Laporan ditugaskan kepada petugas lapangan.',
                ]);

                return $assignment;
            });

            $assignment->load([
                'report:id,title',
                'officer:id,full_name,opd_id,role',
                'assignedBy:id,full_name,opd_id,role',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Penugasan laporan berhasil dibuat.',
                'data' => $assignment,
                'errors' => null
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat penugasan laporan.',
                'data' => null,
                'errors' => $e->getMessage()
            ], 500);
        }

    }

    public function endAssignment(ReportUserAssignment $id)
    {
        $this->authorize('end', $id);

        $id->update([
            'ended_at' => now(),
        ]);

        $id->report()->update([
            'current_status' => ReportStatus::PENDING_QA_REVIEW
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Penugasan laporan berhasil diselesaikan.',
            'data' => $id,
        ]);
    }


    public function update(Request $request, ReportUserAssignment $reportAssignment)
    {
        //
    }

}