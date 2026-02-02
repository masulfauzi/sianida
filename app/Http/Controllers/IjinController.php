<?php
namespace App\Http\Controllers;

use App\Modules\Ijin\Models\Ijin;
use App\Modules\JenisIjin\Models\JenisIjin;
use App\Modules\StatusIjin\Models\StatusIjin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IjinController extends Controller
{
    public function index(Request $request)
    {
        $id_siswa = $request->input('siswa_id');

        if (! $id_siswa) {
            return response()->json([
                'success' => false,
                'message' => 'siswa_id parameter diperlukan!',
            ], 400);
        }

        $ijin = Ijin::where('id_siswa', $id_siswa)
            ->join('jenis_ijin', 'ijin.id_jenis_ijin', '=', 'jenis_ijin.id')
            ->join('status_ijin', 'ijin.id_status_ijin', '=', 'status_ijin.id')
            ->select(
                'jenis_ijin.jenis_ijin',
                'ijin.tgl_mulai as start_date',
                'ijin.tgl_selesai as end_date',
                'ijin.lama_ijin',
                'status_ijin.status_ijin',
                'ijin.created_at'
            )
            ->orderBy('ijin.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $ijin,
        ], 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'type'       => 'required',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
            'surat'      => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png',
            'siswa_id'   => 'required',
        ]);

        // Get id_siswa from request
        $id_siswa = $request->input('siswa_id');

        // Get default id_status_ijin where status_ijin = 'Menunggu'
        $statusIjin = StatusIjin::where('status_ijin', 'Menunggu')->first();

        if (! $statusIjin) {
            return response()->json([
                'success' => false,
                'message' => 'Status ijin Menunggu tidak ditemukan!',
            ], 404);
        }

        // Get id_jenis_ijin where jenis_ijin = type from request
        $jenisIjin = JenisIjin::where('jenis_ijin', $request->input('type'))->first();

        if (! $jenisIjin) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis ijin tidak ditemukan!',
            ], 404);
        }

        $ijin                 = new Ijin();
        $ijin->id_jenis_ijin  = $jenisIjin->id;
        $ijin->tgl_mulai      = $request->input("start_date");
        $ijin->tgl_selesai    = $request->input("end_date");
        $ijin->id_siswa       = $id_siswa;
        $ijin->id_status_ijin = $statusIjin->id;
        $ijin->lama_ijin      = (strtotime($request->input("end_date")) - strtotime($request->input("start_date"))) / (60 * 60 * 24) + 1;

        // Handle file upload
        if ($request->hasFile('surat')) {
            $file     = $request->file('surat');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('surat_ijin'), $fileName);
            $ijin->surat = $fileName;
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
