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
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $request->bulan, now()->year);

            // Create array of days
            $allDays = collect(range(1, $daysInMonth))->map(function ($day) {
                return ['day' => $day, 'count' => 0];
            })->keyBy('day');

            // Get actual presensi data grouped by day
            $presensiData = PresensiSholat::where('nisn', $request->nisn)
                ->where('jenis_presensi', 'Sholat Dzuhur')
                ->whereMonth('Waktu_Presensi', $request->bulan)
                ->selectRaw('DAY(Waktu_Presensi) as day, COUNT(*) as count')
                ->groupBy(DB::raw('DAY(Waktu_Presensi)'))
                ->get()
                ->keyBy('day');
            
            // Merge data - show all days with presensi count or 0 if not present
            $data = $allDays->map(function ($item, $day) use ($presensiData) {
                return $presensiData->has($day) ? $presensiData[$day] : $item;
            })->values();

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
