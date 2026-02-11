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
                ->select(
                    'siswa.id as id_siswa',
                    'siswa.nama_siswa',
                    'siswa.nis',
                    'siswa.foto',
                    'siswa.nisn',
                    'siswa.nik',
                    'siswa.no_hp',
                    'kelas.kelas',
                    'users.email'
                )
                ->join('pesertadidik', 'siswa.id', '=', 'pesertadidik.id_siswa')
                ->join('kelas', 'pesertadidik.id_kelas', '=', 'kelas.id')
                ->join('users', 'siswa.nik', '=', 'users.identitas')
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

    /**
     * Upload profile photo for siswa
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFotoProfil(Request $request)
    {
        try {
            $siswaId = $request->input('siswaId');

            if (! $siswaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa ID is required',
                ], 400);
            }

            if (! $request->hasFile('foto')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Photo file is required',
                ], 400);
            }

            $siswa = DB::table('siswa')->where('id', $siswaId)->first();

            if (! $siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa not found',
                ], 404);
            }

            // Delete old photo if exists
            if ($siswa->foto && file_exists(public_path('foto_profil/' . $siswa->foto))) {
                unlink(public_path('foto_profil/' . $siswa->foto));
            }

            $file     = $request->file('foto');
            $fileName = $siswaId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('foto_profil'), $fileName);

            DB::table('siswa')
                ->where('id', $siswaId)
                ->update(['foto' => $fileName]);

            return response()->json([
                'success' => true,
                'message' => 'Profile photo uploaded successfully',
                'data'    => ['foto' => $fileName],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload profile photo',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
