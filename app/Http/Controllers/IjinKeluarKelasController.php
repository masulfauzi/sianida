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
}
