<?php
namespace App\Http\Controllers;

use App\Modules\IjinKeluarKelas\Models\IjinKeluarKelas;
use App\Modules\Jampelajaran\Models\Jampelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IjinKeluarKelasController extends Controller
{
    /**
     * Display a listing of ijin keluar kelas by siswa
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $id_siswa = $request->input('id_siswa');

            if (! $id_siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'id_siswa parameter is required',
                ], 400);
            }

            $ijinKeluarKelas = IjinKeluarKelas::where('id_siswa', $id_siswa)
                ->join('guru', 'ijin_keluar_kelas.id_guru', '=', 'guru.id')
                ->join('jampelajaran as jam_keluar_table', 'ijin_keluar_kelas.jam_keluar', '=', 'jam_keluar_table.id')
                ->join('jampelajaran as jam_kembali_table', 'ijin_keluar_kelas.jam_kembali', '=', 'jam_kembali_table.id')
                ->select('ijin_keluar_kelas.*', 'guru.nama', 'jam_keluar_table.jam_pelajaran as jam_keluar_pelajaran', 'jam_kembali_table.jam_pelajaran as jam_kembali_pelajaran')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Ijin keluar kelas retrieved successfully',
                'data'    => $ijinKeluarKelas,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ijin keluar kelas',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store ijin keluar kelas data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_siswa'            => 'required',
                'id_guru'             => 'required',
                'id_jenis_ijin_kelas' => 'required',
                'keperluan'           => 'required|string',
                'jam_keluar'          => 'required',
                'jam_masuk'           => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Get id from jampelajaran table for jam_keluar
            $jamKeluar = Jampelajaran::where('jam_pelajaran', $request->input('jam_keluar'))->first();
            if (! $jamKeluar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jam pelajaran jam_keluar not found',
                ], 404);
            }

            // Get id from jampelajaran table for jam_masuk
            $jamMasuk = Jampelajaran::where('jam_pelajaran', $request->input('jam_masuk'))->first();
            if (! $jamMasuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jam pelajaran jam_masuk not found',
                ], 404);
            }

            $ijinKeluarKelas = IjinKeluarKelas::create([
                'id_siswa'             => $request->input('id_siswa'),
                'id_guru'              => $request->input('id_guru'),
                'id_jenis_ijin_keluar' => $request->input('id_jenis_ijin_kelas'),
                'tanggal'              => date('Y-m-d'),
                'jam_keluar'           => $jamKeluar->id,
                'jam_kembali'          => $jamMasuk->id,
                'keperluan'            => $request->input('keperluan'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ijin keluarkelassavedsuccessfully',
                'data'    => $ijinKeluarKelas,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed tosaveijinkeluarkelas',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display detail of a single ijin keluar kelas
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $ijinKeluarKelas = IjinKeluarKelas::where('ijin_keluar_kelas.id', $id)
                ->join('siswa', 'ijin_keluar_kelas.id_siswa', '=', 'siswa.id')
                ->join('guru', 'ijin_keluar_kelas.id_guru', '=', 'guru.id')
                ->join('jampelajaran as jam_keluar_table', 'ijin_keluar_kelas.jam_keluar', '=', 'jam_keluar_table.id')
                ->join('jampelajaran as jam_kembali_table', 'ijin_keluar_kelas.jam_kembali', '=', 'jam_kembali_table.id')
                ->join('jenis_ijin_keluar_kelas', 'ijin_keluar_kelas.id_jenis_ijin_keluar', '=', 'jenis_ijin_keluar_kelas.id')
                ->select(
                    'ijin_keluar_kelas.*',
                    'siswa.nama_siswa',
                    'siswa.nis',
                    'siswa.nisn',
                    'guru.nama as nama_guru',
                    'jam_keluar_table.jam_pelajaran as jam_keluar_pelajaran',
                    'jam_kembali_table.jam_pelajaran as jam_kembali_pelajaran',
                    'jenis_ijin_keluar_kelas.jenis_ijin_keluar_kelas'
                )
                ->first();

            if (! $ijinKeluarKelas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ijin keluar kelas not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ijin keluar kelas detail retrieved successfully',
                'data'    => $ijinKeluarKelas,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ijin keluar kelas detail',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve ijin keluar kelas by guru
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve(Request $request, $id)
    {
        try {
            $ijinKeluarKelas = IjinKeluarKelas::find($id);

            if (! $ijinKeluarKelas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ijin keluar kelas not found',
                ], 404);
            }

            $ijinKeluarKelas->is_valid_guru = '1';
            $ijinKeluarKelas->save();

            return response()->json([
                'success' => true,
                'message' => 'Ijin keluar kelas approved successfully',
                'data'    => $ijinKeluarKelas,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve ijin keluar kelas',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a listing of ijin keluar kelas by guru
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_guru(Request $request)
    {
        try {
            $id_guru = $request->input('id_guru');

            if (! $id_guru) {
                return response()->json([
                    'success' => false,
                    'message' => 'id_guru parameter is required',
                ], 400);
            }

            $ijinKeluarKelas = IjinKeluarKelas::where('id_guru', $id_guru)
                ->join('siswa', 'ijin_keluar_kelas.id_siswa', '=', 'siswa.id')
                ->join('jampelajaran as jam_keluar_table', 'ijin_keluar_kelas.jam_keluar', '=', 'jam_keluar_table.id')
                ->join('jampelajaran as jam_kembali_table', 'ijin_keluar_kelas.jam_kembali', '=', 'jam_kembali_table.id')
                ->join('jenis_ijin_keluar_kelas', 'ijin_keluar_kelas.id_jenis_ijin_keluar', '=', 'jenis_ijin_keluar_kelas.id')
                ->select('ijin_keluar_kelas.*', 'siswa.nama_siswa', 'jam_keluar_table.jam_pelajaran as jam_keluar_pelajaran', 'jam_kembali_table.jam_pelajaran as jam_kembali_pelajaran', 'jenis_ijin_keluar_kelas.jenis_ijin_keluar_kelas')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Ijin keluar kelas retrieved successfully',
                'data'    => $ijinKeluarKelas,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ijin keluar kelas',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
