<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VillageController extends Controller
{
    private function authorizeRole(array $roles)
    {
        $user = Auth::user();
        $userRole = $user->role instanceof UserRole ? $user->role->value : $user->role;

        if (! in_array($userRole, $roles, true)) {
            abort(response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk aksi ini.',
                'data' => null,
                'errors' => ['Unauthorized'],
            ], 403));
        }
    }

    public function index($code, Request $request)
    {
        $district = District::where('code', $code)->firstOrFail();

        $perPage = $request->get('per_page', 10);

        $villages = Village::where('district_code', $district->code)
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'List villages berhasil diambil',
            'data' => [
                'items' => $villages->items(),
                'meta' => [
                    'current_page' => $villages->currentPage(),
                    'last_page' => $villages->lastPage(),
                    'per_page' => $villages->perPage(),
                    'total' => $villages->total(),
                ]
            ],
            'errors' => null
        ], 200);
    }

    public function store(Request $request, $code)
    {
        $this->authorizeRole([UserRole::CITY_ADMIN->value, UserRole::DISTRICT_ADMIN->value]);

        $district = District::where('code', $code)->firstOrFail();

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
        $this->authorizeRole([UserRole::CITY_ADMIN->value, UserRole::DISTRICT_ADMIN->value]);

        $district = District::where('code', $code)->firstOrFail();
        $village = Village::where('code', $villageCode)
            ->where('district_code', $district->code)
            ->firstOrFail();

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
        $this->authorizeRole([UserRole::CITY_ADMIN->value, UserRole::DISTRICT_ADMIN->value]);

        $district = District::where('code', $code)->firstOrFail();

        $village = Village::where('code', $villageCode)
            ->where('district_code', $district->code)
            ->firstOrFail();

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