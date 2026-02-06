<?php
namespace App\Http\Controllers;

use App\Models\PresensiSholat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresensiSholatController extends Controller
{
    /**
     * Get all presensi data with limit 10.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $data = PresensiSholat::where('nisn', $request->nisn)
                ->where('jenis_presensi', 'Sholat Dzuhur')
                ->whereMonth('Waktu_Presensi', $request->bulan)
                ->selectRaw('DAY(Waktu_Presensi) as day, COUNT(*) as count')
                ->groupBy(DB::raw('DAY(Waktu_Presensi)'))
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data presensi sholat retrieved successfully',
                'data'    => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
