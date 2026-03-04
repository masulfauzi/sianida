<?php
namespace App\Http\Controllers;

use App\Modules\Mapel\Models\Mapel;

class MapelController extends Controller
{
    /**
     * Display a listing of all mapel
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $mapel = Mapel::orderBy('mapel')->get();

            return response()->json([
                'success' => true,
                'message' => 'Mapel retrieved successfully',
                'data'    => $mapel,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve mapel',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
