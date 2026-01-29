<?php
namespace App\Http\Controllers;

use App\Modules\PresensiHarian\Models\PresensiHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PresensiController extends Controller
{
    /**
     * Store presensi harian data with image
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'userId' => 'required',
                'image'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $userId = $request->input('userId');

            // Get siswa data from siswa table based on userId
            $siswa = DB::table('siswa')
                ->join('users', 'siswa.nik', '=', 'users.identitas')
                ->where('users.id', $userId)
                ->select('siswa.id')
                ->first();

            if (! $siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa not found for this user',
                ], 404);
            }

            $data = [
                'id_siswa' => $siswa->id,
                'tgl'      => date('Y-m-d'),
            ];

            // Get status kehadiran ID where status_kehadiran_pendek is 'H'
            $statusKehadiran = DB::table('statuskehadiran')
                ->where('status_kehadiran_pendek', 'H')
                ->first();

            if (! $statusKehadiran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status kehadiran H not found',
                ], 404);
            }

            $data['id_status_kehadiran'] = $statusKehadiran->id;

            if ($request->hasFile('image')) {
                $image           = $request->file('image');
                $imageName       = time() . '_' . $image->getClientOriginalName();
                $destinationPath = public_path('presensi_harian');
                $image->move($destinationPath, $imageName);
                $data['gambar'] = $imageName;
            }

            // Check if student already has presensi today
            $existingPresensi = PresensiHarian::where('id_siswa', $data['id_siswa'])
                ->where('tgl', $data['tgl'])
                ->first();

            if ($existingPresensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sudah melakukan presensi',
                ], 422);
            }

            $presensi = PresensiHarian::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Presensi harian saved successfully',
                'data'    => $presensi,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save presensi harian',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request, $userId, $currentmonth)
    {
        try {
            // Get siswa data from siswa table based on userId
            $siswa = DB::table('siswa')
                ->join('users', 'siswa.nik', '=', 'users.identitas')
                ->where('users.id', $userId)
                ->select('siswa.id')
                ->first();

            if (!$siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa not found for this user',
                ], 404);
            }

            // Get presensi records for the month
            $presensiRecords = PresensiHarian::where('id_siswa', $siswa->id)
                ->whereMonth('tgl', $currentmonth)
                ->whereYear('tgl', date('Y'))
                ->get(['tgl', 'created_at'])
                ->keyBy('tgl');

            // Get the number of days in the current month
            $year = date('Y');
            $daysInMonth = \Carbon\Carbon::create($year, $currentmonth, 1)->daysInMonth;

            // Create response for all days in the month
            $presensiHarian = collect();
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = \Carbon\Carbon::create($year, $currentmonth, $day)->format('Y-m-d');

                if (isset($presensiRecords[$date])) {
                    $record = $presensiRecords[$date];
                    $createdAtTime = \Carbon\Carbon::parse($record->created_at)->format('H:i:s');
                    $status = $createdAtTime < '07:00:00' ? 'Hadir' : 'Terlambat';

                    $presensiHarian->push([
                        'tgl' => $date,
                        'created_at' => $record->created_at,
                        'status' => $status,
                    ]);
                } else {
                    $presensiHarian->push([
                        'tgl' => $date,
                        'created_at' => null,
                        'status' => 'Tidak Hadir',
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Presensi harian retrieved successfully',
                'data'    => $presensiHarian,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve presensi harian',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
