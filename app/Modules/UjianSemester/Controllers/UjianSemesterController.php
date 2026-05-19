<?php
namespace App\Modules\UjianSemester\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\UjianSemester\Models\UjianSemester;
use App\Modules\Semester\Models\Semester;
use App\Modules\Mapel\Models\Mapel;
use App\Modules\Guru\Models\Guru;
use App\Modules\Jurusan\Models\Jurusan;
use App\Modules\Tingkat\Models\Tingkat;

use App\Http\Controllers\Controller;
use App\Modules\SoalSemester\Models\SoalSemester;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UjianSemesterController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Ujian Semester";

	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$admin = [
			'bf1548f3-295c-4d73-809d-66ab7c240091',
			'1fe8326c-22c4-4732-9c12-f7b83a16b842'
		];

		// dd(session('active_role')['id']);

		if (in_array(session('active_role')['id'], $admin)) {
			return redirect()->route('ujiansemester.admin.index');
		}

		$query = UjianSemester::query()->whereIdGuru(session('id_guru'))->where('id_semester', get_semester('active_semester_id'));
		if ($request->has('search')) {
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data ' . $this->title);
		return view('UjianSemester::ujiansemester', array_merge($data, ['title' => $this->title]));
	}

	public function index_admin(Request $request)
	{
		$query = UjianSemester::query()->where('id_semester', get_semester('active_semester_id'));
		if ($request->has('search')) {
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data ' . $this->title);
		return view('UjianSemester::ujiansemester_admin', array_merge($data, ['title' => $this->title]));
	}

	public function export(Request $request, $id_ujian)
	{
		$soal = SoalSemester::whereIdUjiansemester($id_ujian)->orderBy('no_soal')->get();
		$ujian = UjianSemester::find($id_ujian);

		$this->log($request, 'mengeeksport soal ' . $this->title);



		$spreadsheet = new Spreadsheet();
		$styleArray = array(
			'font' => array(
				'name' => 'Times New Roman'
			)
		);
		$spreadsheet->getDefaultStyle()->applyFromArray($styleArray);

		$activeWorksheet = $spreadsheet->getActiveSheet();

		$activeWorksheet->getCell('A1')->setValue('No Soal');
		$activeWorksheet->getCell('B1')->setValue('Soal');
		$activeWorksheet->getCell('C1')->setValue('PilA');
		$activeWorksheet->getCell('D1')->setValue('PilB');
		$activeWorksheet->getCell('E1')->setValue('PilC');
		$activeWorksheet->getCell('F1')->setValue('PilD');
		$activeWorksheet->getCell('G1')->setValue('PilE');
		$activeWorksheet->getCell('H1')->setValue('Jawaban');
		$activeWorksheet->getCell('I1')->setValue('Jenis');
		$activeWorksheet->getCell('J1')->setValue('file1');
		$activeWorksheet->getCell('K1')->setValue('file2');
		$activeWorksheet->getCell('L1')->setValue('fileA');
		$activeWorksheet->getCell('M1')->setValue('fileB');
		$activeWorksheet->getCell('N1')->setValue('fileC');
		$activeWorksheet->getCell('O1')->setValue('fileD');
		$activeWorksheet->getCell('P1')->setValue('fileE');

		$baris = 2;
		foreach ($soal as $item) {
			$activeWorksheet->getCell('A' . $baris)->setValue($item->no_soal);
			$activeWorksheet->getCell('B' . $baris)->setValue($item->soal);
			$activeWorksheet->getCell('C' . $baris)->setValue($item->opsi_a);
			$activeWorksheet->getCell('D' . $baris)->setValue($item->opsi_b);
			$activeWorksheet->getCell('E' . $baris)->setValue($item->opsi_c);
			$activeWorksheet->getCell('F' . $baris)->setValue($item->opsi_d);
			$activeWorksheet->getCell('G' . $baris)->setValue($item->opsi_e);
			$activeWorksheet->getCell('H' . $baris)->setValue(strtoupper($item->kunci));
			$activeWorksheet->getCell('I' . $baris)->setValue("1");
			$activeWorksheet->getCell('J' . $baris)->setValue($item->gambar);
			$activeWorksheet->getCell('K' . $baris)->setValue('');
			$activeWorksheet->getCell('L' . $baris)->setValue($item->gambar_a);
			$activeWorksheet->getCell('M' . $baris)->setValue($item->gambar_b);
			$activeWorksheet->getCell('N' . $baris)->setValue($item->gambar_c);
			$activeWorksheet->getCell('O' . $baris)->setValue($item->gambar_d);
			$activeWorksheet->getCell('P' . $baris)->setValue($item->gambar_e);

			$baris++;
		}


		$writer = new Xls($spreadsheet);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . urlencode('Template Soal ' . $ujian->mapel->mapel . ' Kelas ' . $ujian->tingkat->tingkat . '.xls') . '"');
		$writer->save('php://output');

	}

	public function upload(Request $request, string $id_ujian)
	{
		$data['data'] = UjianSemester::find($id_ujian);
		$data['soal'] = SoalSemester::where('id_ujiansemester', $id_ujian)->get();

		$this->log($request, 'melihat halaman upload ujian semester ' . $this->title);
		return view('UjianSemester::ujiansemester_upload', array_merge($data, ['title' => $this->title]));
	}

	public function aksi_upload(Request $request)
	{
		$request->validate([
			'file' => 'required|mimes:pdf,doc,docx,xls,xlsx|max:10240'
		]);

		$fileName = time() . '.' . $request->file->extension();

		$request->file->move(public_path('uploads/' . $request->get('jenis')), $fileName);

		$ujian = UjianSemester::find($request->get('id'));
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
		return redirect()->route('ujiansemester.upload.index', $request->get('id'))->with('message_success', 'Berkas berhasil diupload!');
	}

	public function create(Request $request)
	{
		$ref_mapel = Mapel::all()->sortBy('mapel')->pluck('mapel', 'id');
		$ref_mapel->prepend('-PILIH SALAH SATU-', '');
		$ref_jurusan = Jurusan::all()->sortBy('jurusan')->pluck('jurusan', 'id');
		$ref_jurusan->prepend('-PILIH SALAH SATU-', '');
		$ref_tingkat = Tingkat::all()->sortBy('tingkat')->pluck('tingkat', 'id');
		$ref_tingkat->prepend('PILIH SALAH SATU-', '');

		$data['forms'] = array(
			'id_mapel' => ['Mapel', Form::select("id_mapel", $ref_mapel, null, ["class" => "form-control select2"])],
			'id_jurusan' => ['Jurusan', Form::select("id_jurusan", $ref_jurusan, null, ["class" => "form-control select2"])],
			'id_tingkat' => ['Tingkat', Form::select("id_tingkat", $ref_tingkat, null, ["class" => "form-control select2"])],
			// 'kisi_kisi' => ['Kisi Kisi', Form::text("kisi_kisi", old("kisi_kisi"), ["class" => "form-control","placeholder" => ""]) ],
			// 'norma_penilaian' => ['Norma Penilaian', Form::text("norma_penilaian", old("norma_penilaian"), ["class" => "form-control","placeholder" => ""]) ],
			'jml_soal' => ['Jml Soal', Form::number("jml_soal", old("jml_soal"), ["class" => "form-control", "placeholder" => ""])],
			'id_semester' => ['', Form::hidden("id_semester", get_semester('active_semester_id'))],
			'id_guru' => ['', Form::hidden("id_guru", session('id_guru'))],

		);

		$this->log($request, 'membuka form tambah ' . $this->title);
		return view('UjianSemester::ujiansemester_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_semester' => 'required',
			'id_mapel' => 'required',
			'id_guru' => 'required',
			'id_jurusan' => 'required',
			'id_tingkat' => 'required',
			// 'kisi_kisi' => 'required',
			// 'norma_penilaian' => 'required',
			'jml_soal' => 'required',

		]);

		$ujiansemester = new UjianSemester();
		$ujiansemester->id_semester = $request->input("id_semester");
		$ujiansemester->id_mapel = $request->input("id_mapel");
		$ujiansemester->id_guru = $request->input("id_guru");
		$ujiansemester->id_jurusan = $request->input("id_jurusan");
		$ujiansemester->id_tingkat = $request->input("id_tingkat");
		$ujiansemester->kisi_kisi = $request->input("kisi_kisi");
		$ujiansemester->norma_penilaian = $request->input("norma_penilaian");
		$ujiansemester->jml_soal = $request->input("jml_soal");

		$ujiansemester->created_by = Auth::id();
		$ujiansemester->save();

		$text = 'membuat ' . $this->title; //' baru '.$ujiansemester->what;
		$this->log($request, $text, ['ujiansemester.id' => $ujiansemester->id]);
		return redirect()->route('ujiansemester.index')->with('message_success', 'Ujian Semester berhasil ditambahkan!');
	}

	public function show(Request $request, UjianSemester $ujiansemester)
	{
		$data['ujiansemester'] = $ujiansemester;

		$text = 'melihat detail ' . $this->title;//.' '.$ujiansemester->what;
		$this->log($request, $text, ['ujiansemester.id' => $ujiansemester->id]);
		return view('UjianSemester::ujiansemester_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, UjianSemester $ujiansemester)
	{
		$data['ujiansemester'] = $ujiansemester;

		$ref_semester = Semester::all()->pluck('semester', 'id');
		$ref_mapel = Mapel::all()->pluck('mapel', 'id');
		$ref_guru = Guru::all()->pluck('nama', 'id');
		$ref_jurusan = Jurusan::all()->pluck('jurusan', 'id');
		$ref_tingkat = Tingkat::all()->pluck('tingkat', 'id');

		$data['forms'] = array(
			'id_semester' => ['Semester', Form::select("id_semester", $ref_semester, null, ["class" => "form-control select2"])],
			'id_mapel' => ['Mapel', Form::select("id_mapel", $ref_mapel, null, ["class" => "form-control select2"])],
			'id_guru' => ['Guru', Form::select("id_guru", $ref_guru, null, ["class" => "form-control select2"])],
			'id_jurusan' => ['Jurusan', Form::select("id_jurusan", $ref_jurusan, null, ["class" => "form-control select2"])],
			'id_tingkat' => ['Tingkat', Form::select("id_tingkat", $ref_tingkat, null, ["class" => "form-control select2"])],
			'kisi_kisi' => ['Kisi Kisi', Form::text("kisi_kisi", $ujiansemester->kisi_kisi, ["class" => "form-control", "placeholder" => "", "id" => "kisi_kisi"])],
			'norma_penilaian' => ['Norma Penilaian', Form::text("norma_penilaian", $ujiansemester->norma_penilaian, ["class" => "form-control", "placeholder" => "", "id" => "norma_penilaian"])],
			'jml_soal' => ['Jml Soal', Form::text("jml_soal", $ujiansemester->jml_soal, ["class" => "form-control", "placeholder" => "", "id" => "jml_soal"])],

		);

		$text = 'membuka form edit ' . $this->title;//.' '.$ujiansemester->what;
		$this->log($request, $text, ['ujiansemester.id' => $ujiansemester->id]);
		return view('UjianSemester::ujiansemester_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_semester' => 'required',
			'id_mapel' => 'required',
			'id_guru' => 'required',
			'id_jurusan' => 'required',
			'id_tingkat' => 'required',
			'kisi_kisi' => 'required',
			'norma_penilaian' => 'required',
			'jml_soal' => 'required',

		]);

		$ujiansemester = UjianSemester::find($id);
		$ujiansemester->id_semester = $request->input("id_semester");
		$ujiansemester->id_mapel = $request->input("id_mapel");
		$ujiansemester->id_guru = $request->input("id_guru");
		$ujiansemester->id_jurusan = $request->input("id_jurusan");
		$ujiansemester->id_tingkat = $request->input("id_tingkat");
		$ujiansemester->kisi_kisi = $request->input("kisi_kisi");
		$ujiansemester->norma_penilaian = $request->input("norma_penilaian");
		$ujiansemester->jml_soal = $request->input("jml_soal");

		$ujiansemester->updated_by = Auth::id();
		$ujiansemester->save();


		$text = 'mengedit ' . $this->title;//.' '.$ujiansemester->what;
		$this->log($request, $text, ['ujiansemester.id' => $ujiansemester->id]);
		return redirect()->route('ujiansemester.index')->with('message_success', 'Ujian Semester berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$ujiansemester = UjianSemester::find($id);
		$ujiansemester->deleted_by = Auth::id();
		$ujiansemester->save();
		$ujiansemester->delete();

		$text = 'menghapus ' . $this->title;//.' '.$ujiansemester->what;
		$this->log($request, $text, ['ujiansemester.id' => $ujiansemester->id]);
		return back()->with('message_success', 'Ujian Semester berhasil dihapus!');
	}

	public function import(Request $request)
	{
		try {
			// Validate request
			$request->validate([
				'file' => 'required|file|mimes:xlsx,xls',
				'id_ujiansemester' => 'required|uuid'
			]);

			$idUjian = $request->get('id_ujiansemester');

			// Verify ujian exists and user has access
			$ujian = UjianSemester::find($idUjian);
			if (!$ujian) {
				return response()->json([
					'success' => false,
					'message' => 'Ujian semester tidak ditemukan.'
				], 404);
			}

			// Verify authorization (user is the owner or admin)
			$isAdmin = in_array(session('active_role')['id'], [
				'bf1548f3-295c-4d73-809d-66ab7c240091',
				'1fe8326c-22c4-4732-9c12-f7b83a16b842'
			]);
			if (!$isAdmin && $ujian->id_guru !== session('id_guru')) {
				return response()->json([
					'success' => false,
					'message' => 'Anda tidak memiliki akses untuk mengimport soal ujian ini.'
				], 403);
			}

			// Load Excel file
			$file = $request->file('file');
			$spreadsheet = IOFactory::load($file);
			$worksheet = $spreadsheet->getActiveSheet();

			// Get all rows
			$rows = $worksheet->toArray();
			if (count($rows) < 2) {
				return response()->json([
					'success' => false,
					'message' => 'File Excel kosong atau hanya memiliki header.'
				]);
			}

			// Prepare data
			$soalData = [];
			$errors = [];
			$startRow = 2; // Start from row 2 (row 1 is header)

			DB::beginTransaction();

			foreach ($rows as $index => $row) {
				$rowNumber = $index + 1;
				if ($rowNumber < $startRow) continue;
				if (empty($row[0])) continue; // Skip empty rows

				// Column mapping: A=0, B=1, C=2, ... P=15
				$noSoal = trim($row[0] ?? '');
				$soal = trim($row[1] ?? '');
				$opsiA = trim($row[2] ?? '');
				$opsiB = trim($row[3] ?? '');
				$opsiC = trim($row[4] ?? '');
				$opsiD = trim($row[5] ?? '');
				$opsiE = trim($row[6] ?? '');
				$kunci = strtoupper(trim($row[7] ?? ''));
				// Skip column I (index 8) - not used
				$gambar = trim($row[9] ?? '');
				// Skip column K (index 10) - not used
				$gambarA = trim($row[11] ?? '');
				$gambarB = trim($row[12] ?? '');
				$gambarC = trim($row[13] ?? '');
				$gambarD = trim($row[14] ?? '');
				$gambarE = trim($row[15] ?? '');

				// Validation
				if (empty($noSoal)) {
					$errors[] = "Baris $rowNumber: No soal tidak boleh kosong.";
					continue;
				}

				if (empty($soal)) {
					$errors[] = "Baris $rowNumber: Soal tidak boleh kosong.";
					continue;
				}

				if (!in_array($kunci, ['A', 'B', 'C', 'D', 'E'])) {
					$errors[] = "Baris $rowNumber: Kunci harus A, B, C, D, atau E (ditemukan: $kunci).";
					continue;
				}

				// Prepare data for insert
				$soalData[] = [
					'id' => Str::uuid(),
					'id_ujiansemester' => $idUjian,
					'no_soal' => $noSoal,
					'soal' => $soal,
					'opsi_a' => $opsiA,
					'opsi_b' => $opsiB,
					'opsi_c' => $opsiC,
					'opsi_d' => $opsiD,
					'opsi_e' => $opsiE,
					'kunci' => $kunci,
					'gambar' => $gambar ? $gambar : null,
					'gambar_a' => $gambarA ? $gambarA : null,
					'gambar_b' => $gambarB ? $gambarB : null,
					'gambar_c' => $gambarC ? $gambarC : null,
					'gambar_d' => $gambarD ? $gambarD : null,
					'gambar_e' => $gambarE ? $gambarE : null,
					'created_by' => Auth::id(),
					'created_at' => now(),
					'updated_at' => now()
				];
			}

			// If there are errors, rollback and return
			if (!empty($errors)) {
				DB::rollBack();
				return response()->json([
					'success' => false,
					'message' => 'Data tidak valid:<br>' . implode('<br>', array_slice($errors, 0, 10)),
					'errors' => $errors
				]);
			}

			// If no valid data, return error
			if (empty($soalData)) {
				DB::rollBack();
				return response()->json([
					'success' => false,
					'message' => 'Tidak ada data soal yang valid untuk diimport.'
				]);
			}

			// Delete existing soal for this ujian before import
			SoalSemester::where('id_ujiansemester', $idUjian)->delete();

			// Bulk insert in chunks for better performance
			$chunks = array_chunk($soalData, 100);
			$totalInserted = 0;

			foreach ($chunks as $chunk) {
				SoalSemester::insert($chunk);
				$totalInserted += count($chunk);
			}

			DB::commit();

			$this->log($request, 'mengimport soal ujian semester', ['ujiansemester.id' => $idUjian, 'total_soal' => $totalInserted]);

			return response()->json([
				'success' => true,
				'message' => "Import berhasil! Soal lama dihapus dan $totalInserted soal baru telah ditambahkan.",
				'count' => $totalInserted
			]);

		} catch (\Exception $e) {
			if (DB::transactionLevel() > 0) {
				DB::rollBack();
			}

			return response()->json([
				'success' => false,
				'message' => 'Terjadi kesalahan: ' . $e->getMessage()
			], 500);
		}
	}

}
