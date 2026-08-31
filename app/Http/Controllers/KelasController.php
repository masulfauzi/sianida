<?php
namespace App\Http\Controllers;

use App\Modules\Kelas\Models\Kelas;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Kelas",
 *     description="Endpoint data kelas"
 * )
 */
class KelasController extends Controller
{
    /**
     * Display a listing of all kelas
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/kelas",
     *     tags={"Kelas"},
     *     summary="Ambil semua data kelas",
     *     description="Mengembalikan seluruh kelas terurut berdasarkan nama kelas.",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Data kelas berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Kelas retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="a1b2c3d4-e5f6-7890-abcd-ef1234567890"),
     *                     @OA\Property(property="kelas", type="string", example="XII RPL 1"),
     *                     @OA\Property(property="nama_pendek", type="string", nullable=true, example="XII RPL 1"),
     *                     @OA\Property(property="konsentrasi_keahlian", type="string", nullable=true, example="Rekayasa Perangkat Lunak"),
     *                     @OA\Property(property="id_tingkat", type="string", format="uuid", nullable=true),
     *                     @OA\Property(property="id_jurusan", type="string", format="uuid", nullable=true),
     *                     @OA\Property(property="id_ruang", type="string", format="uuid", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Gagal mengambil data kelas")
     * )
     */
    public function index()
    {
        try {
            $kelas = Kelas::orderBy('kelas')->get();

            return response()->json([
                'success' => true,
                'message' => 'Kelas retrieved successfully',
                'data'    => $kelas,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve kelas',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
