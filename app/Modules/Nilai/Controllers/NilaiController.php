<?php
namespace App\Modules\Nilai\Controllers;

use App\Helpers\Logger;
use App\Http\Controllers\Controller;
use App\Modules\Jurusan\Models\Jurusan;
use App\Modules\Kelas\Models\Kelas;
use App\Modules\Konfirmasinilai\Models\Konfirmasinilai;
use App\Modules\Log\Models\Log;
use App\Modules\Mapel\Models\Mapel;
use App\Modules\Nilai\Models\Nilai;
use App\Modules\Pesertadidik\Models\Pesertadidik;
use App\Modules\Semester\Models\Semester;
use App\Modules\Siswa\Models\Siswa;
use Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class NilaiController extends Controller
{
    use Logger;
    protected $log;
    protected $title = "Nilai";

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    public function index(Request $request)
    {

        // dd(session('active_role')['id']);

        if (session('active_role')['id'] == 'ce70ee2f-b43b-432b-b71c-30d073a4ba23') {
            return redirect()->route('nilai.siswa.index');
        }

        $data['selected'] = [
            'id_semester' => $request->input('id_semester'),
            'id_jurusan'  => $request->input('id_jurusan'),
        ];

        $data['semester'] = Semester::orderBy('urutan')->get()->pluck('semester', 'id');
        $data['semester']->prepend('-PILIH SALAH SATU-', '');

        // dd($data['semester']);
        $data['jurusan'] = Jurusan::all()->pluck('jurusan', 'id');
        $data['jurusan']->prepend('-PILIH SALAH SATU-', '');

        $siswa = Siswa::select('siswa.*', 'n.peringkat_final')
            ->join('pesertadidik as p', 'siswa.id', '=', 'p.id_siswa')
            ->join('kelas as k', 'p.id_kelas', '=', 'k.id')
            ->join('tingkat as t', 'k.id_tingkat', '=', 't.id')
            ->join('snbp as n', 'n.id_siswa', '=', 'siswa.id')
            ->where('p.id_semester', session('active_semester')['id'])
            ->where('k.id_jurusan', $request->input('id_jurusan'))
            ->where('t.tingkat', 'XII')
        // ->orderBy('n.peringkat_final')
        //->orderBy('n.rata_rata', 'DESC')
        // order by nama_siswa
            ->orderBy('siswa.nama_siswa')
            ->get();

        //dd($siswa);

        $id_siswa = Siswa::select('siswa.id')
            ->join('pesertadidik as p', 'siswa.id', '=', 'p.id_siswa')
            ->join('kelas as k', 'p.id_kelas', '=', 'k.id')
            ->join('tingkat as t', 'k.id_tingkat', '=', 't.id')
            ->where('p.id_semester', session('active_semester')['id'])
            ->where('k.id_jurusan', $request->input('id_jurusan'))
            ->where('t.tingkat', 'XII')
            ->get();

        //dd($id_siswa);

        $mapel = Nilai::whereIn('id_siswa', $id_siswa)
            ->join('mapel as m', 'nilai.id_mapel', '=', 'm.id')
            ->where('nilai.id_semester', $request->input('id_semester'))
            ->orderBy('m.urutan');

        $nilai = clone $mapel;

        $nilai = $nilai->get();

        $mapel = $mapel->groupBy('m.id')->get();
        //dd($mapel);

        $data['mapel'] = $mapel;
        $data['siswa'] = $siswa;
        $data['nilai'] = $nilai;

        $this->log($request, 'melihat halaman manajemen data ' . $this->title);
        return view('Nilai::nilai_filter', array_merge($data, ['title' => $this->title]));
    }

    public function index_siswa(Request $request)
    {
        // dd(session('id_siswa'));

        $data['ref_semester'] = Semester::orderBy('urutan')->get()->pluck('semester', 'id');
        $data['ref_semester']->prepend('-PILIH SALAH SATU-', '');

        $semester = $request->input('id_semester');

        if ($semester == null) {
            $query              = Nilai::query()->whereIdSiswa('xxxxxxx');
            $data['semester']   = null;
            $data['konfirmasi'] = null;
        } else {
            // dd($semester);
            $id_siswa           = session('id_siswa');
            $data['semester']   = Semester::find($semester);
            $query              = Nilai::query()->whereIdSemester($semester)->whereIdSiswa($id_siswa)->get();
            $data['konfirmasi'] = Konfirmasinilai::whereIdSiswa($id_siswa)->whereIdSemester($semester)->first();
            // dd($data['konfirmasi']);
        }

        $data['id_semester']     = $semester;
        $data['batas_pengisian'] = "2026-01-12 23:59:59";
        $data['batas_waktu']     = strtotime($data['batas_pengisian']);
        $data['waktu_sekarang']  = time();

        // dd($data['batas_waktu']);

        $data['data'] = $query;

        $this->log($request, 'melihat halaman manajemen data ' . $this->title);
        return view('Nilai::nilai_siswa', array_merge($data, ['title' => $this->title]));
    }

    public function create(Request $request)
    {
        $ref_semester = Semester::all()->sortBy('urutan')->pluck('semester', 'id');
        $ref_semester->prepend('-PILIH SALAH SATU-', '');
        // $ref_siswa = Siswa::all()->pluck('nama_siswa','id');
        $data['ref_mapel'] = Mapel::all()->pluck('mapel', 'id');
        $data['ref_mapel']->prepend('-PILIH SALAH SATU-', '');

        $data['forms'] = [
            'id_semester' => ['Semester', Form::select("id_semester", $ref_semester, null, ["class" => "form-control select2"])],
            // 'id_siswa' => ['Siswa', Form::select("id_siswa", $ref_siswa, null, ["class" => "form-control select2"]) ],
            // 'id_mapel' => ['Mapel', Form::select("id_mapel", $ref_mapel, null, ["class" => "form-control select2"]) ],
            // 'nilai' => ['Nilai', Form::text("nilai", old("nilai"), ["class" => "form-control","placeholder" => ""]) ],
            'file'        => ['File', Form::file("file", ["class" => "form-control", "placeholder" => ""])],

        ];

        $this->log($request, 'membuka form tambah ' . $this->title);
        return view('Nilai::nilai_create', array_merge($data, ['title' => $this->title]));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'id_semester' => 'required',
            // 'id_siswa' => 'required',
            // 'id_mapel' => 'required',
            // 'nilai' => 'required',
            'file'        => 'required|mimes:xls,xlsx',

        ]);

        $file      = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        // dd($extension);

        $reader = null;
        if ($extension === 'xls') {
            $reader = IOFactory::createReader('Xls');
        } elseif ($extension === 'xlsx') {
            $reader = IOFactory::createReader('Xlsx');
        }

        if ($reader === null) {
            // Handle unsupported file extension
        }

        $spreadsheet = $reader->load($file);

        $worksheet = $spreadsheet->getActiveSheet();
        $data      = $worksheet->toArray();

        $jml_baris = count($data);
        $mapel     = $data[0];
        $id_mapel  = [];

        // dd($jml_baris);

        for ($i = 3; $i < count($mapel); $i++) {

            $cek_mapel = Mapel::whereMapel($mapel[$i])->first();

            if ($cek_mapel) {
                $id_mapel[$i] = $cek_mapel->id;
            } else {
                // Handle the case where the subject is not found
                // For example, you might want to log an error or skip this subject
                dd("Mapel not found: " . $mapel[$i]);
            }

        }

        // dd($id_mapel);

        // echo "<pre>";

        for ($i = 1; $i < $jml_baris; $i++) {
            $jml_kolom = count($data[$i]);

            $siswa = Siswa::whereNisn($data[$i][2])->first();

            if ($siswa) {
                for ($j = 3; $j < $jml_kolom; $j++) {
                    $cek_nilai = Nilai::query()->whereIdSemester($request->input("id_semester"))
                        ->whereIdSiswa($siswa->id)
                        ->whereIdMapel($id_mapel[$j])
                        ->first();

                    if ($cek_nilai) {
                        $nilai        = Nilai::find($cek_nilai->id);
                        $nilai->nilai = $data[$i][$j];

                        $nilai->updated_by = Auth::id();
                        $nilai->save();
                    } else {
                        $nilai              = new Nilai();
                        $nilai->id_semester = $request->input("id_semester");
                        $nilai->id_siswa    = $siswa->id;
                        $nilai->id_mapel    = $id_mapel[$j];
                        $nilai->nilai       = $data[$i][$j];

                        $nilai->created_by = Auth::id();
                        $nilai->save();
                    }

                    // dd($nilai);
                }
            }

            // dd($data[$i]);

        }

        // $nilai = new Nilai();
        // $nilai->id_semester = $request->input("id_semester");
        // $nilai->id_siswa = $request->input("id_siswa");
        // $nilai->id_mapel = $request->input("id_mapel");
        // $nilai->nilai = $request->input("nilai");

        // $nilai->created_by = Auth::id();
        // $nilai->save();

        $text = 'membuat ' . $this->title; //' baru '.$nilai->what;
        $this->log($request, $text, ['nilai.id' => $nilai->id]);
        return redirect()->route('nilai.index')->with('message_success', 'Nilai berhasil ditambahkan!');
    }

    public function show(Request $request, Nilai $nilai)
    {
        $data['nilai'] = $nilai;

        $text = 'melihat detail ' . $this->title; //.' '.$nilai->what;
        $this->log($request, $text, ['nilai.id' => $nilai->id]);
        return view('Nilai::nilai_detail', array_merge($data, ['title' => $this->title]));
    }

    public function edit(Request $request, Nilai $nilai)
    {
        $data['nilai'] = $nilai;

        // $ref_semester = Semester::all()->pluck('semester','id');
        // $ref_siswa = Siswa::all()->pluck('nama_siswa','id');
        // $ref_mapel = Mapel::all()->pluck('mapel','id');

        $data['forms'] = [
            // 'id_semester' => ['Semester', Form::select("id_semester", $ref_semester, null, ["class" => "form-control select2"]) ],
            'siswa'    => ['Siswa', Form::text("id_siswa", $nilai->siswa->nama_siswa, ["class" => "form-control", "disabled"])],
            'id_mapel' => ['Mapel', Form::text("id_mapel", $nilai->mapel->mapel, ["class" => "form-control", "disabled"])],
            'nilai'    => ['Nilai', Form::text("nilai", $nilai->nilai, ["class" => "form-control", "placeholder" => "", "id" => "nilai"])],
            'id_siswa' => [null, Form::hidden("id_siswa", $nilai->id_siswa)],

        ];

        $text = 'membuka form edit ' . $this->title; //.' '.$nilai->what;
        $this->log($request, $text, ['nilai.id' => $nilai->id]);
        return view('Nilai::nilai_update', array_merge($data, ['title' => $this->title]));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            // 'id_semester' => 'required',
            'id_siswa' => 'required',
            // 'id_mapel' => 'required',
            'nilai'    => 'required',

        ]);

        $nilai = Nilai::find($id);
        // $nilai->id_semester = $request->input("id_semester");
        // $nilai->id_siswa = $request->input("id_siswa");
        // $nilai->id_mapel = $request->input("id_mapel");
        $nilai->nilai = $request->input("nilai");

        $nilai->updated_by = Auth::id();
        $nilai->save();

        $text = 'mengedit ' . $this->title; //.' '.$nilai->what;
        $this->log($request, $text, ['nilai.id' => $nilai->id]);
        return redirect()->route('siswa.detail.index', $request->input('id_siswa') . '?tab=transkrip')->with('message_success', 'Nilai berhasil diubah!');
    }

    public function destroy(Request $request, $id)
    {
        $nilai             = Nilai::find($id);
        $nilai->deleted_by = Auth::id();
        $nilai->save();
        $nilai->delete();

        $text = 'menghapus ' . $this->title; //.' '.$nilai->what;
        $this->log($request, $text, ['nilai.id' => $nilai->id]);
        return back()->with('message_success', 'Nilai berhasil dihapus!');
    }

    public function verif_nilai(Request $request)
    {
        $data['kelas'] = Kelas::orderBy('kelas')->paginate(12)->withQueryString();

        $this->log($request, 'melihat halaman manajemen data ' . $this->title);
        return view('Nilai::verif_daftar_kelas', array_merge($data, ['title' => $this->title]));
    }

    public function daftar_siswa(Request $request, $id_kelas)
    {
        $data['siswa'] = Pesertadidik::join('siswa as s', 'pesertadidik.id_siswa', '=', 's.id')
            ->where('pesertadidik.id_kelas', '=', $id_kelas)
            ->where('pesertadidik.id_semester', '=', session('active_semester')['id'])
            ->orderBy('s.nama_siswa')
            ->get();

        // session()->set('id_kelas', $id_kelas);
        session(['id_kelas' => $id_kelas]);

        // dd(session('id_kelas'));

        // dd($data['siswa']);

        $this->log($request, 'melihat halaman manajemen data ' . $this->title);
        return view('Nilai::verif_daftar_siswa', array_merge($data, ['title' => $this->title]));
    }

    public function daftar_nilai(Request $request, $id_siswa)
    {
        $data['semester']   = Nilai::whereIdSiswa($id_siswa)->groupBy('id_semester')->get();
        $data['nilai']      = Nilai::whereIdSiswa($id_siswa)->get();
        $data['konfirmasi'] = Konfirmasinilai::where('id_siswa', '=', $id_siswa)->get();
        $data['siswa']      = Siswa::find($id_siswa);

        // dd($data['konfirmasi']);

        $this->log($request, 'melihat halaman manajemen data ' . $this->title);
        return view('Nilai::verif_daftar_nilai', array_merge($data, ['title' => $this->title]));
    }

    public function simpan_verif(Request $request)
    {
        $jumlah_data = count($request->id_nilai);

        for ($i = 0; $i < $jumlah_data; $i++) {
            $nilai = Nilai::find($request->id_nilai[$i]);

            $nilai->nilai = $request->nilai[$i];
            $nilai->save();

            // dd($nilai);
        }

        $konfirmasi = Konfirmasinilai::find($request->id_konfirmasi);

        $konfirmasi->is_verif = 1;
        $konfirmasi->save();

        // dd($jumlah_data);

        $text = 'memverifikasi nilai ' . $this->title; //.' '.$nilai->what;
        $this->log($request, $text, ['nilai.id' => $nilai->id]);
        return back()->with('message_success', 'Nilai berhasil diverifikasi!');
    }

    public function leger_nilai(Request $request)
    {
        $data['semester'] = Semester::orderBy('urutan')->get()->pluck('semester', 'id');
        $data['semester']->prepend('-PILIH SALAH SATU-', '');

        $data['kelas'] = Kelas::orderBy('kelas')->get()->pluck('kelas', 'id');
        $data['kelas']->prepend('-PILIH SALAH SATU-', '');

        $this->log($request, 'membuka form leger ' . $this->title);
        return view('Nilai::leger_nilai', array_merge($data, ['title' => 'Leger ' . $this->title]));
    }

    public function export_leger(Request $request)
    {
        $this->validate($request, [
            'id_semester' => 'required',
            'id_kelas'    => 'required',
        ]);

        $id_semester = $request->input('id_semester');
        $id_kelas    = $request->input('id_kelas');

        $data          = $this->buildLegerData($id_semester, $id_kelas);
        $data['title'] = 'Export Leger ' . $this->title;

        $semester      = $data['semester'];
        $kelas         = $data['kelas'];
        $semesterLabel = $semester ? $semester->semester : '-';
        $kelasLabel    = $kelas ? $kelas->kelas : '-';
        $text          = 'export leger nilai untuk semester ' . $semesterLabel . ' kelas ' . $kelasLabel;
        $this->log($request, $text);

        // MVP: return response sementara berisi data filter terpilih + jumlah data
        return view('Nilai::export_leger', $data);
    }

    public function export_leger_excel(Request $request)
    {
        $this->validate($request, [
            'id_semester' => 'required',
            'id_kelas'    => 'required',
        ]);

        $id_semester = $request->input('id_semester');
        $id_kelas    = $request->input('id_kelas');

        $data = $this->buildLegerData($id_semester, $id_kelas);

        $templatePath = public_path('template/template-leger.xlsx');
        $spreadsheet  = IOFactory::load($templatePath);
        $sheet        = $spreadsheet->getActiveSheet();

        $semesterLabel  = $data['semester']->semester ?? '';
        $tahunPelajaran = preg_match('/^(Ganjil|Genap)\s+(.+)$/i', $semesterLabel, $matches)
            ? ($matches[2] . ' ' . ucfirst(strtolower($matches[1])))
            : $semesterLabel;

        $sheet->setCellValue('C3', ': ' . ($data['kelas']->kelas ?? ''));
        $sheet->setCellValue('C4', ': ' . $tahunPelajaran);

        $mapelGroups = $data['nilai']->groupBy('id_mapel');
        $mapelList   = $mapelGroups->map(function ($group) {
            return $group->first();
        })->values();

        $headerRow     = 7;
        $dataRowStart  = 8;
        $mapelStartCol = 4; // D

        $templateMapelCols = 3;
        $missingMapelCols  = $templateMapelCols - $mapelList->count();
        if ($missingMapelCols > 0) {
            $removeStartIndex = $mapelStartCol + $mapelList->count();
            $removeStartCol   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($removeStartIndex);
            $sheet->removeColumn($removeStartCol, $missingMapelCols);
        }

        foreach ($mapelList as $index => $mapel) {
            $colIndex = $mapelStartCol + $index;
            $cell     = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . $headerRow;
            $sheet->setCellValue($cell, $mapel->mapel);
        }

        $nilaiIndex = [];
        foreach ($data['nilai'] as $item) {
            $nilaiIndex[$item->id_siswa][$item->id_mapel] = $item->nilai;
        }

        foreach ($data['siswa'] as $rowIndex => $item_siswa) {
            $row = $dataRowStart + $rowIndex;
            $sheet->setCellValue('A' . $row, $rowIndex + 1);
            $sheet->setCellValue('B' . $row, strtoupper($item_siswa->nama_siswa));
            $sheet->setCellValueExplicit(
                'C' . $row,
                $item_siswa->nisn,
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
            );

            foreach ($mapelList as $mapelIndex => $mapel) {
                $colIndex   = $mapelStartCol + $mapelIndex;
                $cell       = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . $row;
                $nilaiValue = $nilaiIndex[$item_siswa->id_siswa][$mapel->id_mapel] ?? '';
                $sheet->setCellValue($cell, $nilaiValue);
            }
        }

        $totalSiswa   = $data['siswa']->count();
        $lastRow      = $totalSiswa > 0 ? ($dataRowStart + $totalSiswa - 1) : $headerRow;
        $lastColIndex = $mapelList->count() > 0 ? ($mapelStartCol + $mapelList->count() - 1) : $mapelStartCol - 1;
        $lastColIndex = max($lastColIndex, 3);
        $lastCol      = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex);

        $tableRange = 'A' . $headerRow . ':' . $lastCol . $lastRow;
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        if ($lastColIndex >= $mapelStartCol) {
            $mapelHeaderRange = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mapelStartCol)
                . $headerRow . ':' . $lastCol . $headerRow;
            $nilaiRange = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($mapelStartCol)
                . $dataRowStart . ':' . $lastCol . $lastRow;

            $sheet->getStyle($mapelHeaderRange)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);

            $sheet->getStyle($nilaiRange)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        if ($lastRow >= $dataRowStart) {
            $noRange   = 'A' . $dataRowStart . ':A' . $lastRow;
            $nisnRange = 'C' . $dataRowStart . ':C' . $lastRow;
            $sheet->getStyle($noRange)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
            $sheet->getStyle($nisnRange)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        for ($colIndex = 1; $colIndex <= $lastColIndex; $colIndex++) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $semesterLabel = $data['semester']->semester ?? 'semester';
        $kelasLabel    = $data['kelas']->kelas ?? 'kelas';
        $safeSemester  = preg_replace('/[\\\\\/]+/', '-', $semesterLabel);
        $safeKelas     = preg_replace('/[\\\\\/]+/', '-', $kelasLabel);
        $safeSemester  = preg_replace('/\s+/', '_', trim($safeSemester));
        $safeKelas     = preg_replace('/\s+/', '_', trim($safeKelas));
        $filename      = 'leger_' . $safeSemester . '_' . $safeKelas . '.xlsx';

        $text = 'export leger excel untuk semester ' . $semesterLabel . ' kelas ' . $kelasLabel;
        $this->log($request, $text);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function buildLegerData($id_semester, $id_kelas)
    {
        $siswa = Pesertadidik::select('pesertadidik.*', 's.nama_siswa', 's.nisn')
            ->join('siswa as s', 'pesertadidik.id_siswa', '=', 's.id')
            ->where('pesertadidik.id_semester', session('active_semester')['id'])
            ->where('pesertadidik.id_kelas', $id_kelas)
            ->orderBy('s.nama_siswa')
            ->get();

        $nilai = Nilai::whereIn('id_siswa', $siswa->pluck('id_siswa'))
            ->where('id_semester', $id_semester)
            ->join('mapel as m', 'nilai.id_mapel', '=', 'm.id')
            ->orderBy('m.urutan')
            ->get();

        return [
            'siswa'    => $siswa,
            'nilai'    => $nilai,
            'semester' => Semester::find($id_semester),
            'kelas'    => Kelas::find($id_kelas),
        ];
    }
}
