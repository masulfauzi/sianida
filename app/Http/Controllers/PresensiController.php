<?php
namespace App\Http\Controllers;

use App\Modules\PresensiHarian\Models\PresensiHarian;
use App\Modules\Presensi\Models\Presensi;
use App\Modules\Semester\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Sianida API",
 *     version="1.0.0",
 *     description="Dokumentasi API Sianida"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum token"
 * )
 * @OA\Tag(
 *     name="Presensi",
 *     description="Endpoint presensi harian dan presensi kartu (RFID)"
 * )
 */
class PresensiController extends Controller
{
    // TODO: ganti dengan credential asli
    private const KARTU_USERNAME = '4l4T';
    private const KARTU_PASSWORD = '4bs3n';

    /**
     * Store presensi harian data with image
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/api/presensi",
     *     tags={"Presensi"},
     *     summary="Simpan presensi harian siswa",
     *     description="Membuat data presensi harian untuk siswa pada tanggal hari ini, opsional disertai foto.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"siswaId"},
     *                 @OA\Property(property="siswaId", type="integer", example=1, description="ID siswa yang melakukan presensi"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Foto presensi (jpeg, png, jpg, gif, webp, heic, heif, maks 10MB)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Presensi harian berhasil disimpan",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Presensi harian saved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Siswa atau status kehadiran tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal atau siswa sudah presensi hari ini"),
     *     @OA\Response(response=500, description="Gagal menyimpan presensi harian")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'siswaId' => 'required',
                'image'   => 'nullable|mimes:jpeg,png,jpg,gif,webp,heic,heif|max:10240',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                if ($errors->has('image') && $request->hasFile('image')) {
                    $image = $request->file('image');
                    $errors->add(
                        'image',
                        'File yang diupload: ' . $image->getClientOriginalName() .
                        ' (format: ' . $image->getClientOriginalExtension() . ')'
                    );
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors'  => $errors,
                ], 422);
            }

            $siswaId = $request->input('siswaId');

            // Verify siswa exists
            $siswa = DB::table('siswa')
                ->where('id', $siswaId)
                ->first();

            if (! $siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa not found for this user',
                ], 404);
            }

            $data = [
                'id_siswa' => $siswaId,
                'tgl'      => date('Y-m-d'),
            ];

            // Get status kehadiran based on current time
            $statusPendek    = now()->format('H:i:s') < '07:00:00' ? 'H' : 'T';
            $statusKehadiran = DB::table('statuskehadiran')
                ->where('status_kehadiran_pendek', $statusPendek)
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
                $imageName       = time() . '_' . $siswaId . '.' . $image->extension();
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

    /**
     * @OA\Get(
     *     path="/api/presensi/{userId}/{currentmonth}/{currentyear}",
     *     tags={"Presensi"},
     *     summary="Ambil rekap presensi harian siswa per bulan",
     *     description="Mengembalikan status presensi siswa untuk setiap hari pada bulan dan tahun yang diminta.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="userId", in="path", required=true, description="ID siswa", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="currentmonth", in="path", required=true, description="Bulan (1-12)", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="currentyear", in="path", required=true, description="Tahun (contoh: 2026)", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Data presensi harian berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Presensi harian retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="tgl", type="string", format="date", example="2026-08-31"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(property="status", type="string", example="Tidak Hadir")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Siswa tidak ditemukan"),
     *     @OA\Response(response=500, description="Gagal mengambil data presensi harian")
     * )
     */
    public function index(Request $request, $siswaId, $currentmonth, $currentyear)
    {
        try {
            // Verify siswa exists
            $siswa = DB::table('siswa')
                ->where('id', $siswaId)
                ->first();

            if (! $siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa not found for this user',
                ], 404);
            }

            // Get presensi records for the month joined with statuskehadiran
            $presensiRecords = PresensiHarian::where('presensi_harian.id_siswa', $siswaId)
                ->whereMonth('presensi_harian.tgl', $currentmonth)
                ->whereYear('presensi_harian.tgl', $currentyear)
                ->join('statuskehadiran', 'presensi_harian.id_status_kehadiran', '=', 'statuskehadiran.id')
                ->get(['presensi_harian.tgl', 'presensi_harian.created_at', 'statuskehadiran.status_kehadiran'])
                ->keyBy('tgl');

            // Get the number of days in the current month
            $daysInMonth = \Carbon\Carbon::create($currentyear, $currentmonth, 1)->daysInMonth;

            // Create response for all days in the month
            $presensiHarian = collect();
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = \Carbon\Carbon::create($currentyear, $currentmonth, $day)->format('Y-m-d');

                if (isset($presensiRecords[$date])) {
                    $record = $presensiRecords[$date];

                    $presensiHarian->push([
                        'tgl'        => $date,
                        'created_at' => $record->created_at,
                        'status'     => $record->status_kehadiran,
                    ]);
                } else {
                    $presensiHarian->push([
                        'tgl'        => $date,
                        'created_at' => null,
                        'status'     => 'Tidak Hadir',
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

    /**
     * Update presensi data
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Put(
     *     path="/api/presensi/{id}",
     *     tags={"Presensi"},
     *     summary="Perbarui status presensi",
     *     description="Mengubah status kehadiran pada data presensi yang sudah ada.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="ID presensi", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="H", description="Kode status kehadiran pendek (mis. H, T)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Presensi berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Presensi updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Presensi tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal"),
     *     @OA\Response(response=500, description="Gagal memperbarui presensi")
     * )
     */
    public function update(Request $request, $id)
    {
        // dd($request->all(), $id);

        try {
            $presensi = Presensi::find($id);

            if (! $presensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Presensi not found',
                ], 404);
            }

            $data = $request->validate([
                'status' => 'required',
            ]);

            $statusKehadiran = DB::table('statuskehadiran')
                ->where('status_kehadiran_pendek', $data['status'])
                ->first();

            $presensi->id_statuskehadiran = $statusKehadiran->id;
            $presensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Presensi updated successfully',
                'data'    => $presensi,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update presensi',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Presensi menggunakan kartu (RFID)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *     path="/api/presensi-kartu",
     *     tags={"Presensi"},
     *     summary="Presensi menggunakan kartu (RFID)",
     *     description="Endpoint publik yang dipanggil alat RFID. Otentikasi memakai username/password tetap yang dikirim di body, bukan token Sanctum.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username","password","kode"},
     *             @OA\Property(property="username", type="string", example="4l4T"),
     *             @OA\Property(property="password", type="string", example="4bs3n"),
     *             @OA\Property(property="kode", type="string", description="UID kartu RFID siswa", example="04A2B3C4")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Presensi kartu berhasil disimpan",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Presensi kartu berhasil disimpan"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="nama_siswa", type="string", example="Budi Santoso"),
     *                 @OA\Property(property="waktu_presensi", type="string", format="date-time"),
     *                 @OA\Property(property="presensi", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Username atau password salah"),
     *     @OA\Response(response=404, description="Kartu tidak terdaftar atau status kehadiran tidak ditemukan"),
     *     @OA\Response(response=422, description="Validasi gagal atau siswa sudah presensi hari ini"),
     *     @OA\Response(response=500, description="Gagal menyimpan presensi kartu")
     * )
     */
    public function presensiKartu(Request $request)
    {
        

        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
                'kode'     => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            if ($request->input('username') !== self::KARTU_USERNAME || $request->input('password') !== self::KARTU_PASSWORD) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username atau password salah',
                ], 401);
            }

            $siswa = DB::table('siswa')
                ->where('uid', $request->input('kode'))
                ->first();

            if (! $siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu tidak terdaftar',
                ], 404);
            }

            $data = [
                'id_siswa' => $siswa->id,
                'tgl'      => date('Y-m-d'),
            ];

            // Get status kehadiran based on current time
            $statusPendek    = now()->format('H:i:s') < '07:00:00' ? 'H' : 'T';
            $statusKehadiran = DB::table('statuskehadiran')
                ->where('status_kehadiran_pendek', $statusPendek)
                ->first();

            if (! $statusKehadiran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status kehadiran H not found',
                ], 404);
            }

            $data['id_status_kehadiran'] = $statusKehadiran->id;

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
                'message' => 'Presensi kartu berhasil disimpan',
                'data'    => [
                    'nama_siswa'     => $siswa->nama_siswa,
                    'waktu_presensi' => $presensi->created_at,
                    'presensi'       => $presensi,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save presensi kartu',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display presensi harian for all siswa in a kelas on a given date
     *
     * @param Request $request
     * @param string $idKelas
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/presensi/kelas/{idKelas}",
     *     tags={"Presensi"},
     *     summary="Ambil presensi harian per kelas",
     *     description="Mengembalikan status presensi harian seluruh siswa pada kelas tertentu untuk satu tanggal (default hari ini), berdasarkan peserta didik semester aktif.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="idKelas", in="path", required=true, description="ID kelas", @OA\Schema(type="string", format="uuid")),
     *     @OA\Parameter(name="tgl", in="query", required=false, description="Tanggal presensi (default: hari ini)", @OA\Schema(type="string", format="date", example="2026-08-31")),
     *     @OA\Response(
     *         response=200,
     *         description="Data presensi harian per kelas berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Presensi harian per kelas retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id_kelas", type="string", format="uuid"),
     *                 @OA\Property(property="nama_kelas", type="string", example="XII RPL 1"),
     *                 @OA\Property(property="tgl", type="string", format="date", example="2026-08-31"),
     *                 @OA\Property(
     *                     property="siswa",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id_siswa", type="string", format="uuid"),
     *                         @OA\Property(property="nama_siswa", type="string", example="Budi Santoso"),
     *                         @OA\Property(property="nis", type="string", nullable=true, example="12345"),
     *                         @OA\Property(property="nisn", type="string", nullable=true, example="0012345678"),
     *                         @OA\Property(property="status_kehadiran", type="string", example="Belum Presensi"),
     *                         @OA\Property(property="waktu_presensi", type="string", format="date-time", nullable=true)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Kelas tidak ditemukan atau tidak ada semester aktif"),
     *     @OA\Response(response=500, description="Gagal mengambil data presensi harian per kelas")
     * )
     */
    public function perKelas(Request $request, $idKelas)
    {
        try {
            $kelas = DB::table('kelas')
                ->where('id', $idKelas)
                ->whereNull('deleted_at')
                ->first();

            if (! $kelas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelas not found',
                ], 404);
            }

            $semester = Semester::get_semester_aktif();

            if (! $semester) {
                return response()->json([
                    'success' => false,
                    'message' => 'Semester aktif not found',
                ], 404);
            }

            $tgl = $request->query('tgl', today()->format('Y-m-d'));

            $siswa = DB::table('pesertadidik as pd')
                ->join('siswa as s', 's.id', '=', 'pd.id_siswa')
                ->leftJoin('presensi_harian as ph', function ($join) use ($tgl) {
                    $join->on('ph.id_siswa', '=', 'pd.id_siswa')
                        ->whereDate('ph.tgl', $tgl)
                        ->whereNull('ph.deleted_at');
                })
                ->leftJoin('statuskehadiran as sk', 'ph.id_status_kehadiran', '=', 'sk.id')
                ->where('pd.id_kelas', $idKelas)
                ->where('pd.id_semester', $semester->id)
                ->whereNull('pd.deleted_at')
                ->orderBy('s.nama_siswa')
                ->select(
                    's.id as id_siswa',
                    's.nama_siswa',
                    's.nis',
                    's.nisn',
                    DB::raw('CASE WHEN ph.id IS NULL THEN \'Belum Presensi\' ELSE sk.status_kehadiran END as status_kehadiran'),
                    'ph.created_at as waktu_presensi'
                )
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Presensi harian per kelas retrieved successfully',
                'data'    => [
                    'id_kelas'   => $kelas->id,
                    'nama_kelas' => $kelas->kelas,
                    'tgl'        => $tgl,
                    'siswa'      => $siswa,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve presensi harian per kelas',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
