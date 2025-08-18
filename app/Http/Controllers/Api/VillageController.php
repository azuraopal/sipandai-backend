<?php

namespace App\Http\Controllers\Api;

use App\Models\Village;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VillageController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Village::class);

        $villages = Village::query()
            ->with('district')
            ->when($request->district_code, function ($query, $districtCode) {
                return $query->where('district_code', $districtCode);
            })
            ->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Daftar kelurahan berhasil diambil.',
            'data' => $villages,
            'errors' => null,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Village::class);

        $validator = Validator::make($request->all(), [
            'district_code' => 'required|string|max:2|exists:districts,code',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $district_code = $request->district_code;

        $lastVillage = Village::where('district_code', $request->district_code)
            ->orderBy('code', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastVillage) {
            $codeParts = explode('.', $lastVillage->code);
            if (isset($codeParts[1])) {
                $nextNumber = (int) $codeParts[1] + 1;
            }
        }

        $newSubCode = str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
        $newCode = $district_code . '.' . $newSubCode;

        $village = Village::create([
            'code' => $newCode,
            'district_code' => $request->district_code,
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kelurahan berhasil ditambahkan.',
            'data' => $village->load('district'),
            'errors' => null,
        ], 201);
    }

    public function show(Village $village)
    {
        $this->authorize('view', $village);

        return response()->json([
            'success' => true,
            'message' => 'Detail kelurahan berhasil diambil.',
            'data' => $village->load('district'),
            'errors' => null,
        ]);
    }

    public function update(Request $request, Village $village)
    {
        $this->authorize('update', $village);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
        ]);

        if($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $village->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kelurahan berhasil diperbarui.',
            'data' => $village->load('district'),
            'errors' => null,
        ]);
    }

    public function destroy(Village $village)
    {
        $this->authorize('destroy', $village);

        $village->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelurahan berhasil dihapus.',
            'data' => null,
            'errors' => null,
        ]);
    }
}
