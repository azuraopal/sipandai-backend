<?php

namespace App\Http\Controllers\Api;

use App\Models\Village;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VillageController extends Controller
{

    public function update(Request $request, Village $id)
    {
        $this->authorize('update', $id);

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

        $id->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kelurahan berhasil diperbarui.',
            'data' => $id->fresh('district'),
            'errors' => null,
        ]);
    }

    public function destroy(Village $id)
    {
        $this->authorize('destroy', $id);

        $id->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelurahan berhasil dihapus.',
            'data' => null,
            'errors' => null,
        ]);
    }
}
