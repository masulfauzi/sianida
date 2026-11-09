<?php
namespace App\Modules\UjianSoal\Controllers;

use App\Helpers\Logger;
use App\Http\Controllers\Controller;
use App\Modules\Log\Models\Log;
use App\Modules\Mapel\Models\Mapel;
use App\Modules\UjianSoal\Models\UjianBankSoal;
use App\Modules\UjianSoal\Models\UjianSoal;
use Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UjianBankSoalController extends Controller
{
    use Logger;
    protected $log;
    protected $title = "Bank Soal Ujian";

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    /**
     * Bank soal hanya boleh dilihat/diubah oleh guru pemiliknya.
     */
    private function authorizeOwner(UjianBankSoal $bankSoal)
    {
        abort_unless($bankSoal->id_guru === session('id_guru'), 403, 'Anda tidak berhak mengakses bank soal ini.');
    }

    public function index(Request $request)
    {
        $query = UjianBankSoal::where('id_guru', session('id_guru'));

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('nama_bank_soal', 'like', "%$search%");
        }

        $data['data'] = $query->with('mapel')->orderByDesc('created_at')->paginate(10)->withQueryString();

        $this->log($request, 'melihat halaman manajemen data ' . $this->title);
        return view('UjianSoal::ujian_bank_soal', array_merge($data, ['title' => $this->title]));
    }

    public function create(Request $request)
    {
        $ref_mapel = Mapel::all()->sortBy('mapel')->pluck('mapel', 'id');

        $data['forms'] = [
            'nama_bank_soal' => ['Nama Bank Soal', Form::text("nama_bank_soal", old("nama_bank_soal"), ["class" => "form-control", "placeholder" => "mis. PAS Matematika X Ganjil 2026"])],
            'id_mapel'       => ['Mapel', Form::select("id_mapel", $ref_mapel, old("id_mapel"), ["class" => "form-control select2"])],
            'deskripsi'      => ['Deskripsi', Form::textarea("deskripsi", old("deskripsi"), ["class" => "form-control"])],
        ];

        $this->log($request, 'membuka form tambah ' . $this->title);
        return view('UjianSoal::ujian_bank_soal_create', array_merge($data, ['title' => $this->title]));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nama_bank_soal' => 'required|string|max:255',
            'id_mapel'       => 'nullable|exists:mapel,id',
            'deskripsi'      => 'nullable|string',
        ]);

        $bankSoal                 = new UjianBankSoal();
        $bankSoal->nama_bank_soal = $request->input('nama_bank_soal');
        $bankSoal->id_mapel       = $request->input('id_mapel');
        $bankSoal->deskripsi      = $request->input('deskripsi');
        $bankSoal->id_guru        = session('id_guru');
        $bankSoal->status         = 'draft';

        $bankSoal->created_by = Auth::id();
        $bankSoal->save();

        $text = 'membuat ' . $this->title;
        $this->log($request, $text, ['ujian_bank_soal.id' => $bankSoal->id]);
        return redirect()->route('ujiansoal.index')->with('message_success', 'Bank soal berhasil ditambahkan!');
    }

    public function edit(Request $request, UjianBankSoal $bankSoal)
    {
        $this->authorizeOwner($bankSoal);

        $ref_mapel = Mapel::all()->sortBy('mapel')->pluck('mapel', 'id');

        $data['bankSoal'] = $bankSoal;
        $data['forms']    = [
            'nama_bank_soal' => ['Nama Bank Soal', Form::text("nama_bank_soal", $bankSoal->nama_bank_soal, ["class" => "form-control"])],
            'id_mapel'       => ['Mapel', Form::select("id_mapel", $ref_mapel, $bankSoal->id_mapel, ["class" => "form-control select2"])],
            'deskripsi'      => ['Deskripsi', Form::textarea("deskripsi", $bankSoal->deskripsi, ["class" => "form-control"])],
        ];

        $text = 'membuka form edit ' . $this->title;
        $this->log($request, $text, ['ujian_bank_soal.id' => $bankSoal->id]);
        return view('UjianSoal::ujian_bank_soal_update', array_merge($data, ['title' => $this->title]));
    }

    public function update(Request $request, UjianBankSoal $bankSoal)
    {
        $this->authorizeOwner($bankSoal);

        $this->validate($request, [
            'nama_bank_soal' => 'required|string|max:255',
            'id_mapel'       => 'nullable|exists:mapel,id',
            'deskripsi'      => 'nullable|string',
        ]);

        $bankSoal->nama_bank_soal = $request->input('nama_bank_soal');
        $bankSoal->id_mapel       = $request->input('id_mapel');
        $bankSoal->deskripsi      = $request->input('deskripsi');

        $bankSoal->updated_by = Auth::id();
        $bankSoal->save();

        $text = 'mengedit ' . $this->title;
        $this->log($request, $text, ['ujian_bank_soal.id' => $bankSoal->id]);
        return redirect()->route('ujiansoal.index')->with('message_success', 'Bank soal berhasil diubah!');
    }

    public function destroy(Request $request, UjianBankSoal $bankSoal)
    {
        $this->authorizeOwner($bankSoal);

        $bankSoal->deleted_by = Auth::id();
        $bankSoal->save();
        $bankSoal->delete();

        $text = 'menghapus ' . $this->title;
        $this->log($request, $text, ['ujian_bank_soal.id' => $bankSoal->id]);
        return back()->with('message_success', 'Bank soal berhasil dihapus!');
    }

    /**
     * Jadikan bank soal final - hanya yang final boleh diekspor.
     * Sengaja tidak ada tombol balik ke draft dari sini.
     */
    public function setFinal(Request $request, UjianBankSoal $bankSoal)
    {
        $this->authorizeOwner($bankSoal);

        if ($bankSoal->soal()->count() === 0) {
            return back()->with('message_error', 'Bank soal belum punya soal, tidak bisa dijadikan final.');
        }

        $bankSoal->status     = 'final';
        $bankSoal->updated_by = Auth::id();
        $bankSoal->save();

        $text = 'menjadikan final ' . $this->title;
        $this->log($request, $text, ['ujian_bank_soal.id' => $bankSoal->id]);
        return back()->with('message_success', 'Bank soal dijadikan final dan siap diekspor.');
    }

    /**
     * Export bank soal ke .xlsx, layout 16 kolom (A-P) sesuai integrasi_soal.md bagian 7.1.
     * Kolom dibaca berdasarkan POSISI oleh aplikasi ujian, bukan nama header - jangan ubah urutan.
     */
    public function export(Request $request, UjianBankSoal $bankSoal)
    {
        $this->authorizeOwner($bankSoal);
        abort_unless($bankSoal->status === 'final', 422, 'Bank soal belum final, tidak bisa diekspor.');

        $soal = $bankSoal->soal; // relasi soal() sudah orderBy no_soal

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            'no_soal', 'soal', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e', 'kunci',
            'jenis', 'gambar_soal', '(tidak digunakan)',
            'gambar_a', 'gambar_b', 'gambar_c', 'gambar_d', 'gambar_e',
        ], null, 'A1');

        $row = 2;
        foreach ($soal as $s) {
            $bs = $s->jenis === UjianSoal::JENIS_BS;

            $sheet->fromArray([
                $s->no_soal,                      // A
                $s->soal,                         // B
                $s->opsi_a ?? '',                 // C
                $s->opsi_b ?? '',                 // D
                $s->opsi_c ?? '',                 // E
                $bs ? '' : ($s->opsi_d ?? ''),    // F
                $bs ? '' : ($s->opsi_e ?? ''),    // G
                $s->kunci,                        // H
                $s->jenis,                        // I
                $s->gambar_soal ?? '',            // J
                '',                               // K (tidak dipakai, kolom tetap ada)
                $s->gambar_a ?? '',               // L
                $s->gambar_b ?? '',               // M
                $s->gambar_c ?? '',               // N
                $bs ? '' : ($s->gambar_d ?? ''),  // O
                $bs ? '' : ($s->gambar_e ?? ''),  // P
            ], null, 'A' . $row);

            $row++;
        }

        $lastRow = $row - 1;
        if ($lastRow >= 2) {
            // Kunci & jenis dipaksa teks agar tidak dikonversi Excel.
            $sheet->getStyle("H2:H{$lastRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $sheet->getStyle("I2:I{$lastRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        }

        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $text = 'mengekspor ' . $this->title;
        $this->log($request, $text, ['ujian_bank_soal.id' => $bankSoal->id]);

        $nama   = Str::slug($bankSoal->nama_bank_soal, '_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nama . '"');
        $writer->save('php://output');
    }
}
