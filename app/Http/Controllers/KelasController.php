<?php
namespace App\Http\Controllers;

use App\Modules\Kelas\Models\Kelas;

class KelasController extends Controller
{
    /**
     * Display a listing of all kelas
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $kelas = Kelas::orderBy('kelas')->get();

            return response()->json([
                'success' => true,
                'message' => 'Kelas retrieved successfully',
                'data'    => $kelas,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve kelas',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
