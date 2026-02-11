<?php
namespace App\Http\Controllers;

use App\Modules\Semester\Models\Semester;
use App\Modules\Siswa\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class SiswaController extends Controller
{
    /**
     * Get siswa data by siswa ID
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function siswa(Request $request)
    {
        try {
            $siswaId = $request->input('siswaId');

            $semesterAktif = Semester::get_semester_aktif();

            if (! $siswaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa ID is required',
                ], 400);
            }

            $siswa = DB::table('siswa')
                ->join('pesertadidik', 'siswa.id', '=', 'pesertadidik.id_siswa')
                ->join('kelas', 'pesertadidik.id_kelas', '=', 'kelas.id')
                ->where('siswa.id', $siswaId)
                ->where('pesertadidik.id_semester', $semesterAktif->id)
                ->first();

            if (! $siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa not found for this user',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data siswa retrieved successfully',
                'data'    => $siswa,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve siswa data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
