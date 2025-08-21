<?php

namespace App\Http\Controllers\Api;

use App\Enums\AttachmentPurpose;
use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Str;
use Validator;

class ReportController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', null);
        $status = $request->query('status', null);

        $reports = Report::query()
            ->with([
                'user:id,full_name,role',
                'reportType:id,name',
                'district:code,name',
                'village:code,name',
                'attachments:report_id,file_url,file_type,purpose',
            ])

            ->when($search, function ($query, $search) {
                return $query->where('title', 'LIKE', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                return $query->where('current_status', $status);
            })

            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar laporan berhasil diambil.',
            'data' => [
                'items' => $reports->items(),
                'meta' => [
                    'current_page' => $reports->currentPage(),
                    'last_page' => $reports->lastPage(),
                    'per_page' => $reports->perPage(),
                    'total' => $reports->total(),
                ]
            ],
            'errors' => null
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_id' => 'required|integer|exists:report_types,id',
            'category_id' => 'required|integer|exists:report_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'district_id' => 'required|string|exists:districts,code',
            'village_id' => 'required|string|exists:villages,code',
            'address_detail' => 'required|string',
            'phone_number' => 'required|string|max:15',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'attachments' => 'sometimes|array',
            'attachments.*' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $lastReport = Report::withTrashed()->orderBy('created_at', 'desc')->first();
            $nextId = 1;
            if ($lastReport) {
                $lastId = (int) substr($lastReport->report_code, -5);
                $nextId = $lastId + 1;
            }

            $reportCode = 'RE-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            $validated = $validator->validated();
            $user = $request->user();

            $report = Report::create([
                'report_code' => $reportCode,
                'user_id' => $user->id,
                'type_id' => $validated['type_id'],
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'district_id' => $validated['district_id'],
                'village_id' => $validated['village_id'],
                'address_detail' => $validated['address_detail'],
                'phone_number' => $validated['phone_number'],
                'coordinates' => DB::raw("ST_PointFromText('POINT({$validated['longitude']} {$validated['latitude']})', 4326)"),
                'current_status' => ReportStatus::PENDING_VERIFICATION->value,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('report_attachments', 'public');
                    $report->attachments()->create([
                        'file_url' => Storage::url($path),
                        'file_type' => $file->getClientMimeType(),
                        'purpose' => AttachmentPurpose::INITIAL_EVIDENCE->value,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dibuat.',
                'data' => $report->load('attachments'),
                'errors' => null
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server.',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Report $report)
    {
        //
    }
}
