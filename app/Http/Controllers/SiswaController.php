<?php
namespace App\Http\Controllers;

use App\Modules\Siswa\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class SiswaController extends Controller
{
    /**
     * Get siswa data by siswa ID
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function siswa(Request $request)
    {
        try {
            $siswaId = $request->input('siswaId');

            if (! $siswaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa ID is required',
                ], 400);
            }

            $siswa = DB::table('siswa')
                ->where('siswa.id', $siswaId)
                ->first();

            if (! $siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa not found for this user',
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
