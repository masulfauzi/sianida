<?php
namespace App\Http\Controllers;

use App\Modules\Jurnal\Models\Jurnal;
use App\Modules\Pesertadidik\Models\Pesertadidik;
use App\Modules\Presensi\Models\Presensi;
use App\Modules\Statuskehadiran\Models\Statuskehadiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JurnalController extends Controller
{
    /**
     * Get jurnal data by id_guru
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $id_guru = $request->input('id_guru');

            $perPage = $request->input('per_page', 10);

            $jurnal = DB::table('jurnal as a')
                ->select('a.tgl_pembelajaran', 'c.mapel', 'd.kelas', 'a.id')
                ->join('mapel as c', 'a.id_mapel', '=', 'c.id')
                ->join('kelas as d', 'a.id_kelas', '=', 'd.id')
                ->where('a.id_guru', $id_guru)
                ->whereNull('a.deleted_at')
                ->orderBy('a.tgl_pembelajaran', 'DESC')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Jurnal retrieved successfully',
                'data'    => $jurnal,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve jurnal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {

        // dd($request->all());

        try {
            $data = $request->validate([
                'id_guru'          => 'required',
                'hari'             => 'required',
                'id_kelas'         => 'required',
                'jam_mulai'        => 'required',
                'jam_selesai'      => 'required',
                'id_mapel'         => 'required',
                'tgl_pembelajaran' => 'required|date',
                'materi'           => 'required',
            ]);

            // Get hari id from urutan
            $hari = DB::table('hari')->where('urutan', $request->input('hari'))->first();
            if ($hari) {
                unset($data['hari']);
                $data['id_hari'] = $hari->id;
            }

            // Get jam_mulai id from jampelajaran
            $jamMulai = DB::table('jampelajaran')->where('jam_pelajaran', $request->input('jam_mulai'))->first();
            if ($jamMulai) {
                // unset($data['jam_mulai']);
                $data['jam_mulai'] = $jamMulai->id;
            }

            // Get jam_selesai id from jampelajaran
            $jamSelesai = DB::table('jampelajaran')->where('jam_pelajaran', $request->input('jam_selesai'))->first();
            if ($jamSelesai) {
                // unset($data['jam_selesai']);
                $data['jam_selesai'] = $jamSelesai->id;
            }

            $pesertadidik    = Pesertadidik::where('id_kelas', $request->input('id_kelas'))->get();
            $statuskehadiran = Statuskehadiran::where('status_kehadiran_pendekat', 'H')->first();

            $jurnal = Jurnal::create($data);

            foreach ($pesertadidik as $pd) {
                Presensi::create([
                    'id_jurnal'          => $jurnal->id,
                    'id_pesertadidik'    => $pd->id,
                    'id_statuskehadiran' => $statuskehadiran->id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Jurnal created successfully',
                'data'    => $jurnal,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create jurnal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get jurnal detail by id
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $jurnal = DB::table('jurnal as a')
                ->select('a.*', 'c.mapel', 'd.kelas', 'e.jam_pelajaran as jam_mulai', 'f.jam_pelajaran as jam_selesai')
                ->join('mapel as c', 'a.id_mapel', '=', 'c.id')
                ->join('kelas as d', 'a.id_kelas', '=', 'd.id')
                ->join('jampelajaran as e', 'a.jam_mulai', '=', 'e.id')
                ->join('jampelajaran as f', 'a.jam_selesai', '=', 'f.id')
                ->where('a.id', $id)
                ->whereNull('a.deleted_at')
                ->first();

            $presensi = Presensi::where('id_jurnal', $id)
                ->join('pesertadidik', 'presensi.id_pesertadidik', '=', 'pesertadidik.id')
                ->join('siswa', 'pesertadidik.id_siswa', '=', 'siswa.id')
                ->join('statuskehadiran', 'presensi.id_statuskehadiran', '=', 'statuskehadiran.id')
                ->select('siswa.nama as nama_siswa', 'statuskehadiran.status_kehadiran_pendekat')
                ->get();

            if (! $jurnal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jurnal not found',
                ], 404);
            }

            return response()->json([
                'success'  => true,
                'message'  => 'Jurnal detail retrieved successfully',
                'data'     => $jurnal,
                'presensi' => $presensi,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve jurnal detail',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
