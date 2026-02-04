<?php
namespace App\Http\Controllers;

use App\Modules\JenisIjinKeluarKelas\Models\JenisIjinKeluarKelas;
use Illuminate\Http\Request;

class JenisIjinKelasController extends Controller
{
    /**
     * Display a listing of jenis ijin kelas
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $jenisIjinKelas = JenisIjinKeluarKelas::all();

            return response()->json([
                'success' => true,
                'message' => 'Jenis ijin kelas retrieved successfully',
                'data'    => $jenisIjinKelas,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve jenis ijin kelas',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
