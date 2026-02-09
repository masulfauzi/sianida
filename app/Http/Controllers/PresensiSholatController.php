<?php
namespace App\Http\Controllers;

use App\Models\IjinSholat;
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
                ->get(['Waktu_Presensi', 'nisn'])
                ->map(function ($record) {
                    $record->date = Carbon::parse($record->Waktu_Presensi)->format('Y-m-d');
                    return $record;
                })
                ->keyBy('date');

            // Get ijin sholat records for the same nisn
            $ijinRecords = IjinSholat::where('nisn', $nisn)->get()->keyBy('tanggal_ijin');

            // Get the number of days in the selected month
            $daysInMonth = Carbon::create($tahun, $bulan, 1)->daysInMonth;

            // Create response for all days in the month
            $data = collect();
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::create($tahun, $bulan, $day)->format('Y-m-d');

                if (isset($presensiRecords[$date])) {
                    $record = $presensiRecords[$date];
                    $status = 'Hadir';
                    // $ijin   = isset($ijinRecords[$date]) ? $ijinRecords[$date] : null;

                    $data->push([
                        'tgl'        => $date,
                        'created_at' => $record->Waktu_Presensi,
                        'status'     => $status,
                        // 'ijin'       => $ijin,
                    ]);
                } else {
                    $ijin   = isset($ijinRecords[$date]) ? $ijinRecords[$date] : null;
                    $status = $ijin ? 'Ijin' : 'Tidak Hadir';

                    $data->push([
                        'tgl'        => $date,
                        'created_at' => null,
                        'status'     => $status,
                        // 'ijin'       => $ijin,
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
