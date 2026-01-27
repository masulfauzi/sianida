<?php
namespace App\Http\Controllers;

use App\Modules\Siswa\Models\Siswa;

class SiswaController extends Controller
{
    /**
     * Get siswa data by ID
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function siswa(Request $request)
    {
        try {
            $id = $request->input('id');

            if (! $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID is required',
                ], 400);
            }

            $siswa = Siswa::find($id);

            if (! $siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data siswa retrieved successfully',
                'data'    => $siswa,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve siswa data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
