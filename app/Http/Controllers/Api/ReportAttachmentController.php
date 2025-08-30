<?php

namespace App\Http\Controllers\Api;

use App\Enums\AttachmentPurpose;
use App\Models\Report;
use App\Models\ReportAttachment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReportAttachmentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_id' => 'required|uuid|exists:reports,id',
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // File input
            'purpose' => 'required|in:' . implode(',', array_column(AttachmentPurpose::cases(), 'value')), // Perbaikan di sini
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $report = Report::findOrFail($request->report_id);

        $this->authorize('create', [ReportAttachment::class, $report]);

        $file = $request->file('attachment');
        $path = $file->store('report_attachments', 'public');

        $validPurpose = $request->purpose;
        if (!in_array($validPurpose, array_column(AttachmentPurpose::cases(), 'value'))) {
            $validPurpose = AttachmentPurpose::INITIAL_EVIDENCE->value;
        }

        $attachment = $report->attachments()->create([
            'purpose' => $validPurpose,
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
        $this->authorize('delete', $reportAttachment);

        $filePath = str_replace('/storage/', '', $reportAttachment->file_url);
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        $reportAttachment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lampiran berhasil dihapus.',
        ]);
    }
}