<?php
namespace App\Http\Controllers;

use App\Models\IjinSholat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IjinSholatController extends Controller
{
    /**
     * Get all ijin sholat data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $data = IjinSholat::get();

            return response()->json([
                'success' => true,
                'message' => 'Data ijin sholat retrieved successfully',
                'data'    => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store ijin sholat data.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nisn'       => 'required',
                'tanggal'    => 'required|date',
                'keterangan' => 'required|string',
                'alasan'     => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $data = IjinSholat::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Data ijin sholat stored successfully',
                'data'    => $data,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error storing data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
