<?php
namespace App\Http\Controllers;

use App\Modules\IjinKeluarKelas\Models\IjinKeluarKelas;
use App\Modules\Jampelajaran\Models\Jampelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IjinKeluarKelasController extends Controller
{
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
            if (!$jamKeluar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jam pelajaran jam_keluar not found',
                ], 404);
            }

            // Get id from jampelajaran table for jam_masuk
            $jamMasuk = Jampelajaran::where('jam_pelajaran', $request->input('jam_masuk'))->first();
            if (!$jamMasuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jam pelajaran jam_masuk not found',
                ], 404);
            }

            $ijinKeluarKelas = IjinKeluarKelas::create([
                'id_siswa'            => $request->input('id_siswa'),
                'id_guru'             => $request->input('id_guru'),
                'id_jenis_ijin_kelas' => $request->input('id_jenis_ijin_kelas'),
                'tanggal'             => date('Y-m-d'),
                'jam_keluar'          => $jamKeluar->id,
                'jam_masuk'           => $jamMasuk->id,
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
