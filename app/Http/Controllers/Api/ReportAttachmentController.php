<?php

namespace App\Http\Controllers\Api;

use App\Enums\AttachmentPurpose;
use App\Models\Report;
use App\Models\ReportAttachment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use Validator;

class ReportAttachmentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_id' => 'required|uuid|exists:reports,id',
            'file_url' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'purpose' => 'required|in:' . implode(',', array_keys(AttachmentPurpose::cases())),
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $report = Report::findOrFail($request->report_id);

        $this->authorize('create', [ReportAttachment::class, $report]);

        $file = $request->file('attachment');
        $path = $file->store('report_attachments', 'public');

        $attachment = $report->attachments()->create([
            'purpose' => $request->purpose,
            'file_url' => Storage::url($path),
            'file_type' => $file->getClientMimeType(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lampiran berhasil diunggah.',
            'data' => $attachment,
            'errors' => null
        ], 201);

    }

    public function destroy(ReportAttachment $reportAttachment)
    {
        $filePath = str_replace('/storage', '', parse_url($reportAttachment->file_url, PHP_URL_PATH));
        Storage::disk('public')->delete($filePath);

        $reportAttachment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lampiran berhasil dihapus.',
        ]);
    }
}
