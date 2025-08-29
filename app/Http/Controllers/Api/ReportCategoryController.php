<?php

namespace App\Http\Controllers\Api;

use App\Models\ReportType;
use App\Models\ReportCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReportCategoryController extends Controller
{
    public function store(Request $request, $id)
    {
        $type = ReportType::findOrFail($id);
        $this->authorize('create', ReportCategory::class);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:report_categories,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = $type->categories()->create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori laporan berhasil dibuat.',
            'data' => $category,
        ], 201);
    }

    public function update(Request $request, $id, ReportCategory $category)
    {
        $type = ReportType::findOrFail($id);
        $this->authorize('update', $category);

        if ($category->type_id !== $type->id) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak termasuk dalam jenis laporan ini.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100|unique:report_categories,name,' . $category->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $category->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori laporan berhasil diperbarui.',
            'data' => $category,
        ]);
    }

    public function destroy($id, ReportCategory $category)
    {
        $type = ReportType::findOrFail($id);
        $this->authorize('delete', $category);

        if ($category->type_id !== $type->id) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak termasuk dalam jenis laporan ini.',
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori laporan berhasil dihapus.',
        ]);
    }
}
