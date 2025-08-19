<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\District;
use App\Policies\DistrictPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
#[DistrictPolicy(District::class)]

class DistrictController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', District::class);

        $perPage = $request->get('per_page', 10);
        $districts = District::paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'List districts berhasil diambil',
            'data' => [
                'items' => $districts->items(),
                'meta' => [
                    'current_page' => $districts->currentPage(),
                    'last_page' => $districts->lastPage(),
                    'per_page' => $districts->perPage(),
                    'total' => $districts->total(),
                ]
            ],
            'errors' => null
        ], 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', District::class);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:districts,name',
            'villages' => 'required|array|min:1',
            'villages.*.name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'data' => null,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $lastDistrict = District::query()->orderBy('code', 'desc')->first();
            $nextCodeNumber = $lastDistrict ? ((int) $lastDistrict->code + 1) : 1;
            $districtCode = str_pad($nextCodeNumber, 2, '0', STR_PAD_LEFT);

            $district = District::create([
                'code' => $districtCode,
                'name' => $request->name,
            ]);

            $villagesData = [];
            foreach ($request->villages as $index => $village) {
                $subCode = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                $villagesData[] = [
                    'code' => $districtCode . '.' . $subCode,
                    'district_code' => $districtCode,
                    'name' => $village['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $district->villages()->createMany($villagesData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kecamatan beserta desa berhasil ditambahkan.',
                'data' => $district->load('villages'),
                'errors' => null
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'data' => null,
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function show(District $id)
    {
        $this->authorize('view', $id);

        return response()->json([
            'success' => true,
            'message' => 'Detail district berhasil diambil',
            'data' => $id->load('villages'),
            'errors' => null
        ], 200);
    }

    public function update(Request $request, District $district)
    {
        $this->authorize('update', $district);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'data' => null,
                'errors' => $validator->errors()
            ], 422);
        }

        $district->update([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'District berhasil diupdate',
            'data' => [$district],
            'errors' => null
        ], 200);
    }

    public function destroy(District $id)
    {
        $this->authorize('destroy', $id);
        $id->delete();

        return response()->json([
            'success' => true,
            'message' => 'District berhasil dihapus',
            'data' => null,
            'errors' => null,
        ], 200);
    }
}