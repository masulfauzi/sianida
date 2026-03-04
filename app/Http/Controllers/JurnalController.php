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

        dd($request->all());

        try {
            $data = $request->validate([
                'id_guru'          => 'required',
                'id_mapel'         => 'required',
                'id_kelas'         => 'required',
                'tgl_pembelajaran' => 'required|date',
            ]);

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
