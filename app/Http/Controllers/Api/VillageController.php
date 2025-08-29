<?php

namespace App\Http\Controllers\Api;

use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VillageController extends Controller
{
    public function store(Request $request, $code)
    {
        $district = District::where('code', $code)->firstOrFail();
        $this->authorize('create', [Village::class, $district]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:villages,name',
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

            $lastVillage = Village::where('district_code', $district->code)
                ->orderBy('code', 'desc')
                ->first();

            if ($lastVillage) {
                $lastSub = (int) substr($lastVillage->code, -2);
                $nextSub = str_pad($lastSub + 1, 2, '0', STR_PAD_LEFT);
            } else {
                $nextSub = '01';
            }

            $villageCode = $district->code . '.' . $nextSub;

            $village = $district->villages()->create([
                'code' => $villageCode,
                'district_code' => $district->code,
                'name' => $request->name,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Village berhasil ditambahkan ke District',
                'data' => $village,
                'errors' => null
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan village.',
                'data' => null,
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $code, $villageCode)
    {
        $district = District::findOrFail($code);
        $village = Village::where('code', $villageCode)
            ->where('district_code', $district->code)
            ->firstOrFail();

        // $this->authorize('update', $code);

        if ($village->district_code !== $district->code) {
            return response()->json([
                'success' => false,
                'message' => 'Desa tidak sesuai dengan district.',
                'data' => null,
                'errors' => null,
            ], 400);
        }


        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
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
            'data' => $village->fresh('district'),
            'errors' => null,
        ]);
    }

    public function destroy($code, $villageCode)
    {
        $district = District::where('code', $code)->firstOrFail();

        $village = Village::where('code', $villageCode)
            ->where('district_code', $district->code)
            ->firstOrFail();

        $this->authorize('destroy', $village);

        if ($village->district_code !== $district->code) {
            return response()->json([
                'success' => false,
                'message' => 'Desa tidak sesuai dengan district.',
                'data' => null,
                'errors' => null,
            ], 400);
        }

        $village->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelurahan berhasil dihapus.',
            'data' => null,
            'errors' => null,
        ]);
    }
}