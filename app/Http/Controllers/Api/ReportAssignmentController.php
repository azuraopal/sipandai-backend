<?php

namespace App\Http\Controllers\Api;

use App\Models\ReportAssignment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReportAssignmentController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_id' => 'required|uuid|exists:reports,id',
            'assigned_to_user_id' => 'required|uuid|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data' => null,
                'errors' => $validator->errors()
            ], 422);
        }

        $assignment = ReportAssignment::create([
            'report_id' => $request->report_id,
            'assigned_to_user_id' => $request->assigned_to_user_id,
            'assigned_by_user_id' => $request->user()->id,
        ]);

        $assignment->load([
            'report:id,title,user_id',
            'report.user:id,full_name,role',
            'assignedTo:id,full_name,role',
            'assignedBy:id,full_name,role'
        ]);

        if ($assignment->assignedTo) {
            $assignment->assignedTo->makeHidden(['role', 'role_label']);
        }
        if ($assignment->assignedBy) {
            $assignment->assignedBy->makeHidden(['role', 'role_label']);
        }
        if ($assignment->report && $assignment->report->user) {
            $assignment->report->user->makeHidden(['role', 'role_label']);
        }

        if ($assignment->report) {
            $assignment->report->makeHidden(['user_id']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Penugasan laporan berhasil dibuat.',
            'data' => $assignment,
            'errors' => null
        ], 201);
    }


    public function update(Request $request, ReportAssignment $reportAssignment)
    {
        //
    }

}
