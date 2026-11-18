<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Simpan / hapus token perangkat FCM aplikasi Skanida Student.
 * Kontrak dengan aplikasi: lihat issue-push-notification.md.
 */
class FcmTokenController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'    => 'required|string|max:255',
            'platform' => 'nullable|in:android,ios',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = $request->user();

        // Pemetaan user -> siswa, sama seperti AuthController::login()
        $idSiswa = DB::table('siswa')
            ->where('nik', $user->identitas)
            ->whereNull('deleted_at')
            ->value('id');

        // Upsert berdasarkan token. Jika token ini sebelumnya milik user lain
        // (HP dipakai bergantian), pemiliknya dipindahkan ke user sekarang.
        FcmToken::updateOrCreate(
            ['token' => $request->input('token')],
            [
                'user_id'  => $user->id,
                'id_siswa' => $idSiswa,
                'platform' => $request->input('platform'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Token tersimpan',
        ], 200);
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        FcmToken::where('token', $request->input('token'))
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['success' => true], 200);
    }
}
