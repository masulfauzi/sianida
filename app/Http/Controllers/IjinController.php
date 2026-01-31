<?php
namespace App\Http\Controllers;

use App\Modules\Ijin\Models\Ijin;
use App\Modules\Siswa\Models\Siswa;
use App\Modules\StatusIjin\Models\StatusIjin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IjinController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'type'       => 'required',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
            'surat'      => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png',
            'user_id'    => 'required',
        ]);

        // Get id_siswa from user_id
        $siswaData = Siswa::get_siswa_by_id_user($request->input('user_id'));

        if (! $siswaData || $siswaData->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan!',
            ], 404);
        }

        // Get default id_status_ijin where status_ijin = 'Menunggu'
        $statusIjin = StatusIjin::where('status_ijin', 'Menunggu')->first();

        if (! $statusIjin) {
            return response()->json([
                'success' => false,
                'message' => 'Status ijin Menunggu tidak ditemukan!',
            ], 404);
        }

        $id_siswa = $siswaData[0]->id_siswa;

        $ijin                 = new Ijin();
        $ijin->type           = $request->input("type");
        $ijin->tgl_mulai      = $request->input("start_date");
        $ijin->tgl_selesai    = $request->input("end_date");
        $ijin->id_siswa       = $id_siswa;
        $ijin->id_status_ijin = $statusIjin->id;
        $ijin->lama_ijin      = (strtotime($request->input("end_date")) - strtotime($request->input("start_date"))) / (60 * 60 * 24) + 1;

        // Handle file upload
        if ($request->hasFile('surat')) {
            $file        = $request->file('surat');
            $fileName    = time() . '_' . $file->getClientOriginalName();
            $filePath    = $file->storeAs('ijin_files', $fileName, 'public');
            $ijin->surat = $filePath;
        }

        $ijin->created_by = Auth::id();
        $ijin->save();

        return response()->json([
            'success' => true,
            'message' => 'Ijin berhasil ditambahkan!',
            'data'    => $ijin,
        ], 201);
    }
}
