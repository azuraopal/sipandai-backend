<?php

namespace App\Http\Controllers\Api;

use App\Models\ReportType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReportTypeController extends Controller
{
    public function index()
    {
        $types = ReportType::with('categories')->get();

        return response()->json([
            'success' => true,
            'message' => 'List report types with categories',
            'data'    => $types
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', ReportType::class);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:report_types,name',
            'description' => 'nullable|string',
            'image_url' => 'sometimes|required|image|mimes:jpg,jpeg,png|max:2048',
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

        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store('report_types', 'public');
            $validatedData['image_url'] = Storage::url($path);
        }

        $reportType = ReportType::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Jenis laporan berhasil dibuat.',
            'data' => $reportType,
            'errors' => null
        ], 201);
    }

    public function update(Request $request, ReportType $id)
    {
        $this->authorize('update', $id);

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('report_types')->ignore($id->id)],
            'description' => 'sometimes|required|string',
            'image_url' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
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

        if ($request->hasFile('image_url')) {
            if ($id->image_url) {
                $oldPath = str_replace('/storage', '', parse_url($id->image_url, PHP_URL_PATH));
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image_url')->store('report_types', 'public');
            $validatedData['image_url'] = Storage::url($path);
        }

        $id->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Jenis laporan berhasil diperbarui.',
            'data' => $id,
            'errors' => null
        ]);
    }

    public function destroy(ReportType $id)
    {
        $this->authorize('destroy', $id);

        if ($id->image_url) {
            $oldPath = str_replace('/storage', '', parse_url($id->image_url, PHP_URL_PATH));
            Storage::disk('public')->delete($oldPath);
        }

        $id->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jenis laporan berhasil dihapus.',
        ]);
    }
}