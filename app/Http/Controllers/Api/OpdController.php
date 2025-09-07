<?php
namespace App\Http\Controllers\Api;

use App\Policies\OpdPolicy;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Validator;

#[OpdPolicy(Opd::class)]
class OpdController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Opd::class);
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
        $this->authorize('create', Opd::class);

        $user = $request->user();

        if ($user->opd_id) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat OPD. Akun anda sudah terhubung ke OPD lain.',
                'data' => null,
                'errors' => ['opd_id' => 'Satu pengguna hanya bisa terhubung dengan satu OPD.']
            ], 409);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:opds,name',
        ]);

        if( $validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal.',
                'data' => null,
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $opd = Opd::create($request->only('name'));
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
        $this->authorize('view', $opd);
        try {
            return response()->json([
                'success' => true,
                'message' => 'Detail OPD berhasil diambil.',
                'data' => [$opd],
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
        $this->authorize('update', $opd);
        try {
            $request->validate([
                'name' => 'required|string|max:100|unique:opds,name,' . $opd->id,
            ]);

            $opd->update($request->all());

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
        $this->authorize('delete', $opd);
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