<?php
namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Policies\OpdPolicy;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

#[OpdPolicy(Opd::class)]
class OpdController extends Controller
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

    public function index()
    {
        try {
            $opds = Opd::paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Daftar OPD berhasil diambil.',
                'data' => [
                    'items' => $opds->items(),
                    'meta' => [
                        'current_page' => $opds->currentPage(),
                        'last_page' => $opds->lastPage(),
                        'per_page' => $opds->perPage(),
                        'total' => $opds->total(),
                    ]
                ],
                'errors' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data OPD.',
                'data' => null,
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $this->authorizeRole([UserRole::CITY_ADMIN->value]);

        $user = $request->user();

        if ($user->opd_id) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat OPD. Akun anda sudah terhubung ke OPD lain.',
                'data' => null,
                'errors' => ['opd_id' => 'Satu pengguna hanya bisa terhubung dengan satu OPD.']
            ], 409);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:opds,name',
        ]);

        try {
            $opd = Opd::create($validated);
            $user->update(['opd_id' => $opd->id]);

            return response()->json([
                'success' => true,
                'message' => 'OPD berhasil dibuat.',
                'data' => $opd,
                'errors' => null
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid.',
                'data' => null,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat OPD.',
                'data' => null,
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show(Opd $opd)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Detail OPD berhasil diambil.',
                'data' => $opd,
                'errors' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail OPD.',
                'data' => null,
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, Opd $opd)
    {
        $this->authorizeRole([UserRole::CITY_ADMIN->value]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:opds,name,' . $opd->id,
            ]);

            $opd->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'OPD berhasil diperbarui.',
                'data' => $opd,
                'errors' => null
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid.',
                'data' => null,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui OPD.',
                'data' => null,
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy(Opd $opd)
    {
        $this->authorizeRole([UserRole::CITY_ADMIN->value]);

        try {
            $opd->delete();

            return response()->json([
                'success' => true,
                'message' => 'OPD berhasil dihapus.',
                'data' => null,
                'errors' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus OPD.',
                'data' => null,
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}