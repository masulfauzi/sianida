<?php
namespace App\Http\Controllers;

use App\Modules\Siswa\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class SiswaController extends Controller
{
    /**
     * Get siswa data by user ID with user information
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function siswa(Request $request)
    {
        try {
            $userId = $request->input('userId');

            if (! $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required',
                ], 400);
            }

            $siswa = DB::table('siswa')
                ->where('siswa.user_id', $userId)
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
