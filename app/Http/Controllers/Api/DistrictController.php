<?php

namespace App\Http\Controllers\Api;

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
            'name' => 'required|string|unique:districts|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'data' => null,
                'errors' => $validator->errors()
            ], 422);
        }

        $lastDistrict = District::query()->orderBy('code', 'DESC')->first();

        if ($lastDistrict) {
            $nextCodeNumber = (int) $lastDistrict->code + 1;
        } else {
            $nextCodeNumber = 1;
        }

        $code = str_pad($nextCodeNumber, 2, '0', STR_PAD_LEFT);

        $district = District::create([
            'code' => $code,
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'District berhasil ditambahkan',
            'data' => $district,
            'errors' => null
        ], 201);
    }

    public function show(District $district)
    {
        $this->authorize('view', $district);

        return response()->json([
            'success' => true,
            'message' => 'Detail district berhasil diambil',
            'data' => [$district],
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

    public function destroy(District $district)
    {
        $this->authorize('delete', $district);
        $district->delete();

        return response()->json([
            'success' => true,
            'message' => 'District berhasil dihapus',
            'data' => null,
            'errors' => null,
        ], 200);
    }
}