<?php
namespace App\Http\Controllers;

use App\Models\PresensiSholat;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            $nisn  = $request->nisn;
            $bulan = $request->bulan;
            $tahun = $request->tahun ?? now()->year;

            $presensiRecords = PresensiSholat::where('nisn', $nisn)
                ->whereMonth('Waktu_Presensi', $bulan)
                ->whereYear('Waktu_Presensi', $tahun)
                ->get(['Waktu_Presensi', 'created_at'])
                ->keyBy('Waktu_Presensi');

            // Get the number of days in the selected month
            $daysInMonth = Carbon::create($tahun, $bulan, 1)->daysInMonth;

            // Create response for all days in the month
            $data = collect();
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::create($tahun, $bulan, $day)->format('Y-m-d');

                if (isset($presensiRecords[$date])) {
                    $record        = $presensiRecords[$date];
                    $createdAtTime = Carbon::parse($record->Waktu_Presensi)->format('H:i:s');
                    $status        = $createdAtTime < '07:00:00' ? 'Hadir' : 'Terlambat';

                    $data->push([
                        'tgl'        => $date,
                        'created_at' => $record->created_at,
                        'status'     => $status,
                    ]);
                } else {
                    $data->push([
                        'tgl'        => $date,
                        'created_at' => null,
                        'status'     => 'Tidak Hadir',
                    ]);
                }
            }

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
