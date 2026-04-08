<?php
namespace App\Modules\Ujiansekolah\Controllers;

use App\Helpers\Logger;
use App\Http\Controllers\Controller;
use App\Modules\Guru\Models\Guru;
use App\Modules\Jurusan\Models\Jurusan;
use App\Modules\Log\Models\Log;
use App\Modules\Mapel\Models\Mapel;
use App\Modules\Soal\Models\Soal;
use App\Modules\Ujiansekolah\Models\Ujiansekolah;
use Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class UjiansekolahController extends Controller
{
    use Logger;
    protected $log;
    protected $title = "Ujiansekolah";

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    public function index(Request $request)
    {
        if (session('active_role')['id'] == '9ec7541e-5a5e-4a3a-a255-6ffb46895f46') {
            return redirect(route('ujiansekolah.guru.index'));
        }

        $query = Ujiansekolah::whereIdSemester(get_semester('active_semester_id'));
        if ($request->has('search')) {
            $search = $request->get('search');
            // $query->where('name', 'like', "%$search%");
        }
        $data['data'] = $query->paginate(20)->withQueryString();

        $this->log($request, 'melihat halaman manajemen data ' . $this->title);
        return view('Ujiansekolah::ujiansekolah', array_merge($data, ['title' => $this->title]));
    }

    public function index_guru(Request $request)
    {
        $query = Ujiansekolah::whereIdSemester(get_semester('active_semester_id'))->whereIdGuru(session('id_guru'));
        if ($request->has('search')) {
            $search = $request->get('search');
            // $query->where('name', 'like', "%$search%");
        }
        $data['data'] = $query->paginate(10)->withQueryString();

        $this->log($request, 'melihat halaman manajemen data ' . $this->title);
        return view('Ujiansekolah::ujiansekolah_kelengkapan', array_merge($data, ['title' => $this->title]));
    }

    public function upload(Request $request, $id_ujian)
    {
        $data['data']         = Ujiansekolah::find($id_ujian);
        $data['soal_utama']   = Soal::whereIdUjiansekolah($id_ujian)->whereIdJenissoal('c365b003-7203-4e5d-b215-1f934238db2f');
        $data['soal_susulan'] = Soal::whereIdUjiansekolah($id_ujian)->whereIdJenissoal('068aa935-e996-4f86-9689-3da4a9aee8f5');

        $this->log($request, 'melihat halaman upload data ' . $this->title);
        return view('Ujiansekolah::ujiansekolah_upload', array_merge($data, ['title' => $this->title]));
    }

    public function aksi_upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:10240',
        ]);

        $fileName = time() . '.' . $request->file->extension();

        $request->file->move(public_path('uploads/' . $request->get('jenis')), $fileName);

        $ujian = Ujiansekolah::find($request->get('id'));
        if ($request->get('jenis') == 'kisikisi') {
            $ujian->kisi_kisi = $fileName;
        } else if ($request->get('jenis') == 'norma') {
            $ujian->norma_penilaian = $fileName;
        } else if ($request->get('jenis') == 'soal') {
            $ujian->soal = $fileName;
        }
        $ujian->save();

        $text = 'mengupload ' . $this->title; //' baru '.$gurumapel->what;
        $this->log($request, $text);
        return redirect()->route('ujiansekolah.guru.upload.index', $request->get('id'))->with('message_success', 'Berkas berhasil diupload!');
    }

    public function upload_excel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx|max:10240',
        ]);

        // dd($request);

        $soal = $request->file('file');

        try {
            $spreadsheet  = IOFactory::load($soal->getRealPath());
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit    = $sheet->getHighestDataRow();
            $column_limit = $sheet->getHighestDataColumn();
            $row_range    = range(2, $row_limit);
            $column_range = range('F', $column_limit);
            $startcount   = 2;
            $data         = [];
            foreach ($row_range as $row) {
                $data[] = [
                    'id'              => Str::uuid(),
                    'id_ujiansekolah' => $request->input('id'),
                    'id_jenissoal'    => $request->input('id_jenissoal'),
                    'no_soal'         => $sheet->getCell('A' . $row)->getValue(),
                    'soal'            => $sheet->getCell('B' . $row)->getValue(),
                    'opsi_a'          => $sheet->getCell('C' . $row)->getValue(),
                    'opsi_b'          => $sheet->getCell('D' . $row)->getValue(),
                    'opsi_c'          => $sheet->getCell('E' . $row)->getValue(),
                    'opsi_d'          => $sheet->getCell('F' . $row)->getValue(),
                    'opsi_e'          => $sheet->getCell('G' . $row)->getValue(),
                    'kunci'           => $sheet->getCell('H' . $row)->getValue(),
                ];
                $startcount++;
            }

            // Soal::where(['id_ujiansekolah' => $request->input('id'), 'id_jenissoal' => $request->input('id_jenissoal')])->delete();
            Soal::where('id_ujiansekolah', $request->input('id'))->where('id_jenissoal', $request->input('id_jenissoal'))->forceDelete();
            Soal::insert($data);

            $text = 'mengupload soal' . $this->title; //' baru '.$gurumapel->what;
            $this->log($request, $text);
            return redirect()->route('ujiansekolah.guru.upload.index', $request->get('id'))->with('message_success', 'Soal berhasil diupload!');

        } catch (Exception $e) {
            $error_code = $e->errorInfo[1];
            return back()->withErrors('There was a problem uploading the data!');
        }

    }

    public function create(Request $request)
    {
        $ref_mapel   = Mapel::all()->sortBy('mapel')->pluck('mapel', 'id');
        $ref_guru    = Guru::all()->sortBy('nama')->pluck('nama', 'id');
        $ref_jurusan = Jurusan::all()->pluck('jurusan', 'id');

        $ref_guru->prepend('-PILIH SALAH SATU-', '');
        $ref_mapel->prepend('-PILIH SALAH SATU-', '');
        $ref_jurusan->prepend('-PILIH SALAH SATU-', '');

        $data['forms'] = [
            'id_guru'     => ['Guru', Form::select("id_guru", $ref_guru, null, ["class" => "form-control select2"])],
            'id_mapel'    => ['Mapel', Form::select("id_mapel", $ref_mapel, null, ["class" => "form-control select2"])],
            'id_jurusan'  => ['Jurusan', Form::select("id_jurusan", $ref_jurusan, null, ["class" => "form-control select2"])],
            'jml_soal'    => ['Jumlah Soal', Form::text("jml_soal", null, ["class" => "form-control"])],
            'id_semester' => ['', Form::hidden("id_semester", get_semester('active_semester_id'), null)],

        ];

        $this->log($request, 'membuka form tambah ' . $this->title);
        return view('Ujiansekolah::ujiansekolah_create', array_merge($data, ['title' => $this->title]));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'id_semester' => 'required',
            'id_mapel'    => 'required',
            'id_guru'     => 'required',
            'id_jurusan'  => 'required',
            'jml_soal'    => 'required|numeric',

        ]);

        $ujiansekolah                  = new Ujiansekolah();
        $ujiansekolah->id_semester     = $request->input("id_semester");
        $ujiansekolah->id_mapel        = $request->input("id_mapel");
        $ujiansekolah->id_guru         = $request->input("id_guru");
        $ujiansekolah->kisi_kisi       = $request->input("kisi_kisi");
        $ujiansekolah->norma_penilaian = $request->input("norma_penilaian");
        $ujiansekolah->id_jurusan      = $request->input("id_jurusan");
        $ujiansekolah->jml_soal        = $request->input("jml_soal");

        $ujiansekolah->created_by = Auth::id();
        $ujiansekolah->save();

        $text = 'membuat ' . $this->title; //' baru '.$ujiansekolah->what;
        $this->log($request, $text, ['ujiansekolah.id' => $ujiansekolah->id]);
        return redirect()->route('ujiansekolah.index')->with('message_success', 'Ujiansekolah berhasil ditambahkan!');
    }

    public function show(Request $request, Ujiansekolah $ujiansekolah)
    {
        $data['ujiansekolah'] = $ujiansekolah;

        $text = 'melihat detail ' . $this->title; //.' '.$ujiansekolah->what;
        $this->log($request, $text, ['ujiansekolah.id' => $ujiansekolah->id]);
        return view('Ujiansekolah::ujiansekolah_detail', array_merge($data, ['title' => $this->title]));
    }

    public function edit(Request $request, Ujiansekolah $ujiansekolah)
    {
        $data['ujiansekolah'] = $ujiansekolah;

        $ref_mapel   = Mapel::all()->sortBy('mapel')->pluck('mapel', 'id');
        $ref_guru    = Guru::all()->sortBy('nama')->pluck('nama', 'id');
        $ref_jurusan = Jurusan::all()->pluck('jurusan', 'id');

        $ref_guru->prepend('-PILIH SALAH SATU-', '');
        $ref_mapel->prepend('-PILIH SALAH SATU-', '');
        $ref_jurusan->prepend('-PILIH SALAH SATU-', '');

        $data['forms'] = [

            'id_guru'    => ['Guru', Form::select("id_guru", $ref_guru, $data['ujiansekolah']['id_guru'], ["class" => "form-control select2"])],
            'id_mapel'   => ['Mapel', Form::select("id_mapel", $ref_mapel, $data['ujiansekolah']['id_mapel'], ["class" => "form-control select2"])],
            'id_jurusan' => ['Jurusan', Form::select("id_jurusan", $ref_jurusan, $data['ujiansekolah']['id_jurusan'], ["class" => "form-control select2"])],
            'jml_soal'   => ['Jumlah Soal', Form::text("id_jurusan", $data['ujiansekolah']['jml_soal'], ["class" => "form-control"])],

        ];

        $text = 'membuka form edit ' . $this->title; //.' '.$ujiansekolah->what;
        $this->log($request, $text, ['ujiansekolah.id' => $ujiansekolah->id]);
        return view('Ujiansekolah::ujiansekolah_update', array_merge($data, ['title' => $this->title]));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'id_mapel'   => 'required',
            'id_guru'    => 'required',
            'id_jurusan' => 'required',

        ]);

        $ujiansekolah             = Ujiansekolah::find($id);
        $ujiansekolah->id_mapel   = $request->input("id_mapel");
        $ujiansekolah->id_guru    = $request->input("id_guru");
        $ujiansekolah->id_jurusan = $request->input("id_jurusan");
        $ujiansekolah->jml_soal   = $request->input("jml_soal");

        $ujiansekolah->updated_by = Auth::id();
        $ujiansekolah->save();

        $text = 'mengedit ' . $this->title; //.' '.$ujiansekolah->what;
        $this->log($request, $text, ['ujiansekolah.id' => $ujiansekolah->id]);
        return redirect()->route('ujiansekolah.index')->with('message_success', 'Ujiansekolah berhasil diubah!');
    }

    public function destroy(Request $request, $id)
    {
        $ujiansekolah             = Ujiansekolah::find($id);
        $ujiansekolah->deleted_by = Auth::id();
        $ujiansekolah->save();
        $ujiansekolah->delete();

        $text = 'menghapus ' . $this->title; //.' '.$ujiansekolah->what;
        $this->log($request, $text, ['ujiansekolah.id' => $ujiansekolah->id]);
        return back()->with('message_success', 'Ujiansekolah berhasil dihapus!');
    }

    public function export(Request $request, Ujiansekolah $ujiansekolah)
    {
        try {
            // Ambil semua data soal berdasarkan ujian sekolah
            $soalUtama = Soal::where('id_ujiansekolah', $ujiansekolah->id)
                ->where('id_jenissoal', 'c365b003-7203-4e5d-b215-1f934238db2f')
                ->orderBy('no_soal')
                ->get();

            $soalSusulan = Soal::where('id_ujiansekolah', $ujiansekolah->id)
                ->where('id_jenissoal', '068aa935-e996-4f86-9689-3da4a9aee8f5')
                ->orderBy('no_soal')
                ->get();

            // Buat spreadsheet baru
            $spreadsheet = new Spreadsheet();

            // ===== SHEET SOAL UTAMA =====
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Soal Utama');

            // Header
            $sheet->setCellValue('A1', 'No Soal');
            $sheet->setCellValue('B1', 'Soal');
            $sheet->setCellValue('C1', 'PilA');
            $sheet->setCellValue('D1', 'PilB');
            $sheet->setCellValue('E1', 'PilC');
            $sheet->setCellValue('F1', 'PilD');
            $sheet->setCellValue('G1', 'PilE');
            $sheet->setCellValue('H1', 'Jawaban');
            $sheet->setCellValue('I1', 'Jenis');
            $sheet->setCellValue('J1', 'file1');
            $sheet->setCellValue('K1', 'file2');
            $sheet->setCellValue('L1', 'fileA');
            $sheet->setCellValue('M1', 'fileB');
            $sheet->setCellValue('N1', 'fileC');
            $sheet->setCellValue('O1', 'fileD');
            $sheet->setCellValue('P1', 'fileE');

            // Style header
            $headerStyle = [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ];

            for ($col = 'A'; $col <= 'P'; $col++) {
                $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            }

            // Data soal utama
            $row = 2;
            foreach ($soalUtama as $soal) {
                $sheet->setCellValue('A' . $row, $soal->no_soal);
                $sheet->getStyle('A' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('B' . $row, $soal->soal);
                $sheet->setCellValue('C' . $row, $soal->opsi_a);
                $sheet->setCellValue('D' . $row, $soal->opsi_b);
                $sheet->setCellValue('E' . $row, $soal->opsi_c);
                $sheet->setCellValue('F' . $row, $soal->opsi_d);
                $sheet->setCellValue('G' . $row, $soal->opsi_e);
                $sheet->setCellValue('H' . $row, $soal->kunci);
                $sheet->setCellValue('I' . $row, 1);
                $sheet->setCellValue('J' . $row, $soal->gambar);
                $sheet->setCellValue('K' . $row, '');
                $sheet->setCellValue('L' . $row, $soal->gambar_a);
                $sheet->setCellValue('M' . $row, $soal->gambar_b);
                $sheet->setCellValue('N' . $row, $soal->gambar_c);
                $sheet->setCellValue('O' . $row, $soal->gambar_d);
                $sheet->setCellValue('P' . $row, $soal->gambar_e);

                // Border untuk data
                for ($col = 'A'; $col <= 'P'; $col++) {
                    $sheet->getStyle($col . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }

                $row++;
            }

            // Set width kolom
            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(35);
            $sheet->getColumnDimension('C')->setWidth(12);
            $sheet->getColumnDimension('D')->setWidth(12);
            $sheet->getColumnDimension('E')->setWidth(12);
            $sheet->getColumnDimension('F')->setWidth(12);
            $sheet->getColumnDimension('G')->setWidth(12);
            $sheet->getColumnDimension('H')->setWidth(12);
            $sheet->getColumnDimension('I')->setWidth(10);
            $sheet->getColumnDimension('J')->setWidth(12);
            $sheet->getColumnDimension('K')->setWidth(12);
            $sheet->getColumnDimension('L')->setWidth(12);
            $sheet->getColumnDimension('M')->setWidth(12);
            $sheet->getColumnDimension('N')->setWidth(12);
            $sheet->getColumnDimension('O')->setWidth(12);
            $sheet->getColumnDimension('P')->setWidth(12);

            // ===== SHEET SOAL SUSULAN (jika ada) =====
            if ($soalSusulan->count() > 0) {
                $sheetSusulan = $spreadsheet->createSheet();
                $sheetSusulan->setTitle('Soal Susulan');

                // Header
                $sheetSusulan->setCellValue('A1', 'No Soal');
                $sheetSusulan->setCellValue('B1', 'Soal');
                $sheetSusulan->setCellValue('C1', 'PilA');
                $sheetSusulan->setCellValue('D1', 'PilB');
                $sheetSusulan->setCellValue('E1', 'PilC');
                $sheetSusulan->setCellValue('F1', 'PilD');
                $sheetSusulan->setCellValue('G1', 'PilE');
                $sheetSusulan->setCellValue('H1', 'Jawaban');
                $sheetSusulan->setCellValue('I1', 'Jenis');
                $sheetSusulan->setCellValue('J1', 'file1');
                $sheetSusulan->setCellValue('K1', 'file2');
                $sheetSusulan->setCellValue('L1', 'fileA');
                $sheetSusulan->setCellValue('M1', 'fileB');
                $sheetSusulan->setCellValue('N1', 'fileC');
                $sheetSusulan->setCellValue('O1', 'fileD');
                $sheetSusulan->setCellValue('P1', 'fileE');

                // Style header
                for ($col = 'A'; $col <= 'P'; $col++) {
                    $sheetSusulan->getStyle($col . '1')->applyFromArray($headerStyle);
                }

                // Data soal susulan
                $row = 2;
                foreach ($soalSusulan as $soal) {
                    $sheetSusulan->setCellValue('A' . $row, $soal->no_soal);
                    $sheetSusulan->getStyle('A' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $sheetSusulan->setCellValue('B' . $row, $soal->soal);
                    $sheetSusulan->setCellValue('C' . $row, $soal->opsi_a);
                    $sheetSusulan->setCellValue('D' . $row, $soal->opsi_b);
                    $sheetSusulan->setCellValue('E' . $row, $soal->opsi_c);
                    $sheetSusulan->setCellValue('F' . $row, $soal->opsi_d);
                    $sheetSusulan->setCellValue('G' . $row, $soal->opsi_e);
                    $sheetSusulan->setCellValue('H' . $row, $soal->kunci);
                    $sheetSusulan->setCellValue('I' . $row, 1);
                    $sheetSusulan->setCellValue('J' . $row, $soal->gambar);
                    $sheetSusulan->setCellValue('K' . $row, '');
                    $sheetSusulan->setCellValue('L' . $row, $soal->gambar_a);
                    $sheetSusulan->setCellValue('M' . $row, $soal->gambar_b);
                    $sheetSusulan->setCellValue('N' . $row, $soal->gambar_c);
                    $sheetSusulan->setCellValue('O' . $row, $soal->gambar_d);
                    $sheetSusulan->setCellValue('P' . $row, $soal->gambar_e);

                    // Border untuk data
                    for ($col = 'A'; $col <= 'P'; $col++) {
                        $sheetSusulan->getStyle($col . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    }

                    $row++;
                }

                // Set width kolom
                $sheetSusulan->getColumnDimension('A')->setWidth(10);
                $sheetSusulan->getColumnDimension('B')->setWidth(35);
                $sheetSusulan->getColumnDimension('C')->setWidth(12);
                $sheetSusulan->getColumnDimension('D')->setWidth(12);
                $sheetSusulan->getColumnDimension('E')->setWidth(12);
                $sheetSusulan->getColumnDimension('F')->setWidth(12);
                $sheetSusulan->getColumnDimension('G')->setWidth(12);
                $sheetSusulan->getColumnDimension('H')->setWidth(12);
                $sheetSusulan->getColumnDimension('I')->setWidth(10);
                $sheetSusulan->getColumnDimension('J')->setWidth(12);
                $sheetSusulan->getColumnDimension('K')->setWidth(12);
                $sheetSusulan->getColumnDimension('L')->setWidth(12);
                $sheetSusulan->getColumnDimension('M')->setWidth(12);
                $sheetSusulan->getColumnDimension('N')->setWidth(12);
                $sheetSusulan->getColumnDimension('O')->setWidth(12);
                $sheetSusulan->getColumnDimension('P')->setWidth(12);
            }

            // Export Excel
            $filename = 'Soal_' . $ujiansekolah->mapel['mapel'] . '_' . now()->format('Y-m-d_His') . '.xls';
            $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');

            $this->log($request, 'mengexport soal ' . $this->title, ['ujiansekolah.id' => $ujiansekolah->id]);
            exit;

        } catch (\Exception $e) {
            return back()->with('message_error', 'Gagal mengexport soal: ' . $e->getMessage());
        }
    }

}
