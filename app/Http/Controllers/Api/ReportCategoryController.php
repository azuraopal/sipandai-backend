<?php

namespace App\Http\Controllers\Api;

use App\Models\ReportCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class ReportCategoryController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('create', ReportCategory::class);

        $validator = Validator::make($request->all(), [
            'type_id' => 'required|integer|exists:report_types,id',
            'name' => 'required|string|max:100|unique:report_categories,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data' => null,
                'errors' => $validator->errors()
            ], 422);
        }

        $reportCategory = ReportCategory::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori laporan berhasil dibuat.',
            'data' => $reportCategory->load('reportType'),
            'errors' => null
        ], 201);
    }

    public function update(Request $request, ReportCategory $id)
    {
        $this->authorize('update', $id);

        $validator = Validator::make($request->all(), [
            'type_id' => 'sometimes|required|integer|exists:report_types,id',
            'name' => 'sometimes|required|string|max:100|unique:report_categories,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data' => null,
                'errors' => $validator->errors()
            ], 422);
        }

        $id->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori laporan berhasil diperbarui.',
            'data' => $id->fresh('reportType'),
            'errors' => null
        ]);
    }

    public function destroy(ReportCategory $id)
    {
        $this->authorize('destroy', $id);

        $id->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori laporan berhasil dihapus.',
            'error' => null,
        ]);
    }
}