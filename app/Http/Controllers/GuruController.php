<?php
namespace App\Http\Controllers;

use App\Modules\Guru\Models\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    /**
     * Display a listing of gurus
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $guru = Guru::select('nama', 'id as id_guru')
                ->where('is_aktif', '1')
                ->orderBy('nama')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Guru retrieved successfully',
                'data'    => $guru,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve guru',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get guru profile by id_guru
     *
     * @param Request $request
     * @param string $id_guru
     * @return \Illuminate\Http\JsonResponse
     */
    public function profil(Request $request)
    {
        try {
            $id_guru = $request->input('id_guru');
            $guru    = Guru::where('id', $id_guru)->first();

            if (! $guru) {
                return response()->json([
                    'success' => false,
                    'message' => 'Guru not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Guru profile retrieved successfully',
                'data'    => $guru,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve guru profile',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
