<?php
namespace App\Http\Controllers;

use App\Modules\Guru\Models\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    /**
     * Display a listing of gurus
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $guru = Guru::all();

            return response()->json([
                'success' => true,
                'message' => 'Guru retrieved successfully',
                'data'    => $guru,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve guru',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
