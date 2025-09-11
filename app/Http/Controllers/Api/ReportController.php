<?php

namespace App\Http\Controllers\Api;

use App\Enums\AttachmentPurpose;
use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Str;
use Validator;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search');
        $status = $request->query('status');

        $reports = Report::query()
            ->with([
                'user:id,full_name,email,profile_picture_url',
                'reportType:id,name',
                'reportCategory:id,name',
                'district:code,name',
                'village:code,name',
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
                'user' => [
                    'id' => $report->user->id,
                    'full_name' => $report->user->full_name,
                    'email' => $report->user->email,
                    'profile_picture_url' => $report->user->profile_picture_url,
                ],
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

    public function requestOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $phoneNumber = $request->phone_number;
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        try {
            $whatsappService = new WhatsAppService();
            $message = "Kode OTP Anda untuk mengirim laporan SIPANDAI Anda adalah: {$code}. Kode ini valid selama 5 menit.";

            $isSend = $whatsappService->sendMessage($phoneNumber, $message);

            if (!$isSend) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim OTP. Silakan coba lagi.',
                ]);
            }

            DB::transaction(function () use ($phoneNumber, $code) {
                DB::table('submit_report_tokens')->updateOrInsert(
                    ['phone_number' => $phoneNumber],
                    [
                        'token_hash' => Hash::make($code),
                        'created_at' => now(),
                    ]
                );
            });

        } catch (\Exception $e) {
            Log::error("Gagal mengirim OTP: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim OTP.',
                'errors' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP telah dikirim ke nomor telepon Anda.',
            'errors' => null
        ]);
    }


    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:15',
            'token' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $tokenData = DB::table('submit_report_tokens')
            ->where('phone_number', $request->phone_number)
            ->first();

        if (!$tokenData || !Hash::check($request->token, $tokenData->token_hash) || Carbon::parse($tokenData->created_at)->addMinutes(5)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak valid atau telah kedaluwarsa.',
            ], 422);
        }

        DB::table('submit_report_tokens')
            ->where('phone_number', $request->phone_number)
            ->update([
                'token_hash' => null,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Nomor telepon berhasil diverifikasi.',
        ]);
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $phoneNumber = $request->phone_number;

        $tokenData = DB::table('submit_report_tokens')
            ->where('phone_number', $phoneNumber)
            ->first();

        if ($tokenData && !Carbon::parse($tokenData->created_at)->addMinutes(5)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'OTP sebelumnya masih aktif. Silakan gunakan kode yang sudah dikirim atau tunggu hingga kedaluwarsa.',
            ], 422);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        try {
            $whatsappService = new WhatsAppService();
            $message = "Kode OTP baru Anda untuk verifikasi SIPANDAI adalah: {$code}. Kode ini valid selama 5 menit.";

            $isSend = $whatsappService->sendMessage($phoneNumber, $message);

            if (!$isSend) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim OTP. Silakan coba lagi.',
                ]);
            }

            DB::transaction(function () use ($phoneNumber, $code) {
                DB::table('submit_report_tokens')->updateOrInsert(
                    ['phone_number' => $phoneNumber],
                    [
                        'token_hash' => Hash::make($code),
                        'created_at' => now(),
                    ]
                );
            });

        } catch (\Exception $e) {
            Log::error("Gagal mengirim ulang OTP: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim OTP ulang.',
                'errors' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP baru telah dikirim ke nomor telepon Anda.',
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
            'phone_number' => 'required|string|max:20',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'attachments' => 'required|min:1',
            'attachments.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();

        if ($user) {
            $this->authorize('create', Report::class);
        } else {
            $phoneNumber = $request->phone_number;
            $token = DB::table('submit_report_tokens')
                ->where('phone_number', $phoneNumber)
                ->first();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor telepon anda belum pernah Request OTP. Silakan request OTP terlebih dahulu.',
                    'requires_otp' => true,
                ], 401);
            }

            if (!is_null($token->token_hash)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor telepon anda belum terverifikasi. Silakan verifikasi nomor telepon anda terlebih dahulu.',
                    'requires_otp' => true,
                ], 401);
            }

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

            $report = Report::create([
                'report_code' => $reportCode,
                'user_id' => $user?->id,
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
                'user_id' => $user?->id,
                'status' => $report->current_status,
                'notes' => 'Laporan dibuat oleh pengguna.',
            ]);

            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');

                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $file) {
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
                'data' => [
                    'id' => $report->id,
                    'report_code' => $report->report_code,
                    'title' => $report->title,
                    'description' => $report->description,
                    'address_detail' => $report->address_detail,
                    'phone_number' => $report->phone_number,
                    'coordinates' => $report->coordinates,
                    'current_status' => $report->current_status,
                    'created_at' => $report->created_at,
                    'report_type' => $report->reportType?->name,
                    'report_category' => $report->reportCategory?->name,
                    'district' => $report->district?->name,
                    'village' => $report->village?->name,
                    'user' => [
                        'id' => $report->user?->id,
                        'full_name' => $report->user?->full_name,
                        'email' => $report->user?->email,
                        'profile_picture_url' => $report->user?->profile_picture_url,
                    ],
                ],
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
            'user:id,full_name,email,profile_picture_url',
            'reportType',
            'reportCategory',
            'district',
            'village',
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
            'user' => [
                'id' => $id->user->id,
                'full_name' => $id->user->full_name,
                'email' => $id->user->email,
                'profile_picture_url' => $id->user->profile_picture_url,
            ],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Detail laporan berhasil diambil.',
            'data' => $mappedData,
            'errors' => null
        ]);
    }

    public function attachments($id)
    {
        $report = Report::with('attachments')->findOrFail($id);

        $mappedAttachments = $report->attachments->map(function ($attachment) {
            return [
                'id' => $attachment->id,
                'file_url' => $attachment->file_url,
                'file_type' => $attachment->file_type,
                'purpose' => $attachment->purpose->label(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar lampiran berhasil diambil.',
            'data' => $mappedAttachments,
            'errors' => null
        ]);
    }

    public function statusHistories($id)
    {
        $report = Report::with('statusHistories.user')->findOrFail($id);

        $mappedHistories = $report->statusHistories->map(function ($history) {
            return [
                'id' => $history->id,
                'status' => $history->status->label(),
                'notes' => $history->notes,
                'created_at' => $history->created_at->format('Y-m-d H:i:s'),
                'user' => [
                    'id' => $history->user?->id,
                    'full_name' => $history->user?->full_name,
                    'role' => $history->user?->role,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Riwayat status berhasil diambil.',
            'data' => $mappedHistories,
            'errors' => null
        ]);
    }

}
