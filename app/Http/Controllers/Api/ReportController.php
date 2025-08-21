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
        $perPage = $request->query('per_page', 15);
        $search = $request->query('search');
        $status = $request->query('status');

        $reports = Report::query()
            ->with([
                'user:id,full_name,profile_picture_url,role',
                'reportType:id,name',
                'reportCategory:id,name',
                'district:code,name',
                'village:code,name',
                'attachments:report_id,file_url,file_type,purpose',
                'statusHistories.user:id,full_name'
            ])
            ->when($search, function ($query, $search) {
                return $query->where('title', 'LIKE', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                return $query->where('current_status', $status);
            })
            ->latest()
            ->paginate($perPage);

        $mappedItems = $reports->getCollection()->map(function ($report) {
            return [
                'id' => $report->id,
                'report_code' => $report->report_code,
                'title' => $report->title,
                'description' => $report->description,
                'address_detail' => $report->address_detail,
                'phone_number' => $report->phone_number,
                'coordinates' => $report->coordinates,
                'current_status' => optional($report->current_status)->label(),
                'created_at' => $report->created_at,
                'report_type' => optional($report->reportType)->name,
                'report_category' => optional($report->reportCategory)->name,
                'district' => optional($report->district)->name,
                'village' => optional($report->village)->name,
                'attachments' => $report->attachments->map(function ($attachment) {
                    return [
                        'file_url' => $attachment->file_url,
                        'file_type' => $attachment->file_type,
                        'purpose' => optional($attachment->purpose)->label(),
                    ];
                }),
                'status_histories' => $report->statusHistories->map(function ($history) {
                    return [
                        'status' => optional($history->status)->label(),
                        'notes' => $history->notes,
                        'created_at' => $history->created_at,
                        'user' => [
                            'full_name' => optional($history->user)->full_name,
                        ],
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar laporan berhasil diambil.',
            'data' => [
                'items' => $mappedItems,
                'meta' => [
                    'current_page' => $reports->currentPage(),
                    'last_page' => $reports->lastPage(),
                    'per_page' => $reports->perPage(),
                    'total' => $reports->total(),
                ]
            ],
            'errors' => null
        ]);
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

            $report->statusHistories()->create([
                'user_id' => $request->user()->id,
                'status' => $report->current_status,
                'description' => 'Laporan dibuat oleh pengguna.',
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
                'data' => $report->load('attachments', 'statusHistories'),
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

    public function show(Report $id)
    {
        $id->load([
            'user',
            'reportType',
            'reportCategory',
            'district',
            'village',
            'attachments',
            'statusHistories.user'
        ]);

        $mappedData = [
            'id' => $id->id,
            'report_code' => $id->report_code,
            'title' => $id->title,
            'description' => $id->description,
            'address_detail' => $id->address_detail,
            'phone_number' => $id->phone_number,
            'coordinates' => $id->coordinates,
            'current_status' => $id->current_status,
            'created_at' => $id->created_at,
            'report_type' => $id->reportType->name,
            'report_category' => $id->reportCategory->name,
            'district' => $id->district->name,
            'village' => $id->village->name,
            'attachments' => $id->attachments->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'file_url' => $attachment->file_url,
                    'file_type' => $attachment->file_type,
                    'purpose' => $attachment->purpose->label(),
                ];
            }),
            'status_histories' => $id->statusHistories->map(function ($history) {
                return [
                    'id' => $history->id,
                    'status' => $history->status->label(),
                    'description' => $history->description,
                    'created_at' => $history->created_at->format('Y-m-d H:i:s'),
                    'user' => [
                        'id' => $history->user->id,
                        'full_name' => $history->user->full_name,
                        'role' => $history->user->role,
                    ],
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Detail laporan berhasil diambil.',
            'data' => $mappedData,
            'errors' => null
        ]);
    }
}
