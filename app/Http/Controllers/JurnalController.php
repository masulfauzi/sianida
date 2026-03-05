<?php
namespace App\Http\Controllers;

use App\Modules\Jurnal\Models\Jurnal;
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
                ->select('a.tgl_pembelajaran', 'c.mapel', 'd.kelas')
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
                'catatan'          => 'required',
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
                unset($data['jam_mulai']);
                $data['id_jam_mulai'] = $jamMulai->id;
            }

            // Get jam_selesai id from jampelajaran
            $jamSelesai = DB::table('jampelajaran')->where('jam_pelajaran', $request->input('jam_selesai'))->first();
            if ($jamSelesai) {
                unset($data['jam_selesai']);
                $data['id_jam_selesai'] = $jamSelesai->id;
            }

            $jurnal = Jurnal::create($data);

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
}
