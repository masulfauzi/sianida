<?php
namespace App\Http\Controllers;

use App\Modules\Siswa\Models\Siswa;

class SiswaController extends Controller
{
    /**
     * Get siswa data by user ID with user information
     *
     * @param Request $request
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function siswa(Request $request, $userId)
    {
        try {
            if (! $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required',
                ], 400);
            }

            $siswa = DB::table('siswa')
                ->join('users', 'siswa.nik', '=', 'users.identitas')
                ->where('users.id', $userId)
                ->select('siswa.*', 'users.name', 'users.email', 'users.username')
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
