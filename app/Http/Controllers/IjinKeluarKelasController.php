<?php
namespace App\Http\Controllers;

use App\Modules\IjinKeluarKelas\Models\IjinKeluarKelas;
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
                'siswa_id'   => 'required',
                'jenis_izin' => 'required',
                'jam_keluar' => 'required',
                'jam_masuk'  => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $ijinKeluarKelas = IjinKeluarKelas::create([
                'id_siswa'            => $request->input('siswa_id'),
                'id_jenis_ijin_kelas' => $request->input('jenis_ijin_kelas'),
                'tanggal'             => date('Y-m-d'),
                'jam_mulai'           => $request->input('jam_mulai'),
                'jam_selesai'         => $request->input('jam_selesai'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ijin keluar kelas saved successfully',
                'data'    => $ijinKeluarKelas,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save ijin keluar kelas',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
