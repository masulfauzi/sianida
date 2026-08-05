<?php
namespace App\Modules\PresensiHarian\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\PresensiHarian\Models\PresensiHarian;
use App\Modules\Siswa\Models\Siswa;
use App\Modules\Statuskehadiran\Models\Statuskehadiran;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Kelas\Models\Kelas;
use App\Modules\Pesertadidik\Models\Pesertadidik;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PresensiHarianController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Presensi Harian";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$id_semester = get_semester('active_semester_id');
		$tgl = $request->get('tgl', today()->format('Y-m-d'));

		$data['tgl'] = $tgl;
		$data['chart_x']   = $this->buildChartData('X', $id_semester, $tgl);
		$data['chart_xi']  = $this->buildChartData('XI', $id_semester, $tgl);
		$data['chart_xii'] = $this->buildChartData('XII', $id_semester, $tgl);

		$this->log($request, 'melihat halaman grafik '.$this->title);
		return view('PresensiHarian::presensiharian', array_merge($data, ['title' => $this->title]));
	}

	private function buildChartData($tingkat, $id_semester, $tgl = null)
	{
		$rows = PresensiHarian::rekap_kehadiran_per_kelas($tingkat, $id_semester, $tgl);

		$categories = $rows->pluck('nama_kelas')->unique()->values();
		$statuses   = $rows->pluck('status_kehadiran')->unique()->values();

		$series = [];
		foreach ($statuses as $status) {
			$dataPerKelas = [];
			foreach ($categories as $kelas) {
				$match = $rows->first(function ($r) use ($kelas, $status) {
					return $r->nama_kelas === $kelas && $r->status_kehadiran === $status;
				});
				$dataPerKelas[] = $match ? (int) $match->jumlah : 0;
			}
			$series[] = ['name' => $status, 'data' => $dataPerKelas];
		}

		return [
			'categories' => $categories->values(),
			'series'     => $series,
		];
	}

	public function rekap_bulanan(Request $request)
	{
		$data = $this->build_rekap_bulanan_data($request);

		$this->log($request, 'melihat rekap bulanan '.$this->title);
		return view('PresensiHarian::presensiharian_rekap_bulanan', array_merge($data, ['title' => $this->title]));
	}

	public function rekap_bulanan_export(Request $request)
	{
		$this->validate($request, [
			'id_kelas' => 'required',
			'bulan'    => 'required',
			'tahun'    => 'required',
		]);

		$data = $this->build_rekap_bulanan_data($request);

		// Warna disamakan dengan kelas Bootstrap table-* yang dipakai di tampilan web
		$bgSecondary = 'E2E3E5';
		$bgSuccess   = 'D1E7DD';
		$bgWarning   = 'FFF3CD';
		$bgInfo      = 'CFF4FC';
		$bgDanger    = 'F8D7DA';

		$namaBulanList = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
		$namaKelas = $data['ref_kelas'][$data['id_kelas']] ?? 'Kelas';
		$namaBulan = $namaBulanList[(int) $data['bulan']] ?? $data['bulan'];

		$lastColIndex = 2 + $data['jumlah_hari'] + 4; // No, Nama Siswa, kolom tanggal, Hadir/Sakit/Ijin/Alfa
		$lastColLetter = Coordinate::stringFromColumnIndex($lastColIndex);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// Judul & keterangan kelas/bulan, tabel diturunkan agar tidak tertimpa
		$sheet->setCellValue('A1', 'Rekap Presensi Harian Bulanan');
		$sheet->mergeCells('A1:' . $lastColLetter . '1');
		$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
		$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

		$sheet->setCellValue('A2', 'Kelas: ' . $namaKelas . '     Bulan: ' . $namaBulan . ' ' . $data['tahun']);
		$sheet->mergeCells('A2:' . $lastColLetter . '2');
		$sheet->getStyle('A2')->getFont()->setBold(true);
		$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

		$offset = 3;
		$headerRow1 = $offset + 1;
		$headerRow2 = $offset + 2;

		$sheet->setCellValue('A' . $headerRow1, 'No');
		$sheet->setCellValue('B' . $headerRow1, 'Nama Siswa');
		$sheet->mergeCells('A' . $headerRow1 . ':A' . $headerRow2);
		$sheet->mergeCells('B' . $headerRow1 . ':B' . $headerRow2);

		$tanggalStartCol = 3;
		$col = $tanggalStartCol;
		for ($d = 1; $d <= $data['jumlah_hari']; $d++) {
			$isWeekend = in_array(date('N', mktime(0, 0, 0, (int) $data['bulan'], $d, (int) $data['tahun'])), [6, 7]);
			$cell = Coordinate::stringFromColumnIndex($col) . $headerRow2;
			$sheet->setCellValue($cell, $d);
			if ($isWeekend) {
				$sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bgSecondary);
			}
			$col++;
		}
		$tanggalEndCol = $col - 1;
		$sheet->setCellValue(Coordinate::stringFromColumnIndex($tanggalStartCol) . $headerRow1, 'Tanggal');
		$sheet->mergeCells(Coordinate::stringFromColumnIndex($tanggalStartCol) . $headerRow1 . ':' . Coordinate::stringFromColumnIndex($tanggalEndCol) . $headerRow1);
		$sheet->getStyle(Coordinate::stringFromColumnIndex($tanggalStartCol) . $headerRow1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

		$summaryColors = ['Hadir' => $bgSuccess, 'Sakit' => $bgWarning, 'Ijin' => $bgInfo, 'Alfa' => $bgDanger];
		foreach ($summaryColors as $label => $bgColor) {
			$cell = Coordinate::stringFromColumnIndex($col) . $headerRow1;
			$range = $cell . ':' . Coordinate::stringFromColumnIndex($col) . $headerRow2;
			$sheet->setCellValue($cell, $label);
			$sheet->mergeCells($range);
			$sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bgColor);
			$col++;
		}
		$sheet->getStyle('A' . $headerRow1 . ':' . $lastColLetter . $headerRow2)->getFont()->setBold(true);
		$sheet->getStyle('A' . $headerRow1 . ':' . $lastColLetter . $headerRow2)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

		$row = $offset + 3;
		foreach ($data['siswa'] as $i => $s) {
			$col = 1;
			$sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $row, $i + 1);
			$sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $row, $s->nama_siswa);

			for ($d = 1; $d <= $data['jumlah_hari']; $d++) {
				$isWeekend = in_array(date('N', mktime(0, 0, 0, (int) $data['bulan'], $d, (int) $data['tahun'])), [6, 7]);
				$value = $isWeekend ? 'OFF' : ($data['rekap'][$s->id_siswa][$d] ?? 'A');
				$cell = Coordinate::stringFromColumnIndex($col) . $row;
				$sheet->setCellValue($cell, $value);
				if ($isWeekend) {
					$sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bgSecondary);
					$sheet->getStyle($cell)->getFont()->getColor()->setRGB('6C757D');
				}
				$col++;
			}

			$hadir = $data['summary'][$s->id_siswa]['hadir'] ?? 0;
			$sakit = $data['summary'][$s->id_siswa]['sakit'] ?? 0;
			$ijin  = $data['summary'][$s->id_siswa]['ijin'] ?? 0;
			$alfa  = max(0, $data['hari_efektif'] - $hadir - $sakit - $ijin);

			foreach ([$hadir, $sakit, $ijin, $alfa] as $index => $value) {
				$bgColor = [$bgSuccess, $bgWarning, $bgInfo, $bgDanger][$index];
				$cell = Coordinate::stringFromColumnIndex($col) . $row;
				$sheet->setCellValue($cell, $value);
				$sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bgColor);
				$sheet->getStyle($cell)->getFont()->setBold(true);
				$col++;
			}

			$row++;
		}

		$lastRow = $row - 1;

		$tableRange = 'A' . $headerRow1 . ':' . $lastColLetter . $lastRow;
		$sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
		$sheet->getStyle($tableRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle('B' . ($offset + 3) . ':B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

		for ($colIndex = 1; $colIndex <= $lastColIndex; $colIndex++) {
			$sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex))->setAutoSize(true);
		}

		$filename = 'Rekap Presensi Bulanan - ' . $namaKelas . ' - ' . $namaBulan . ' ' . $data['tahun'] . '.xlsx';

		$this->log($request, 'export excel rekap bulanan '.$this->title);

		return response()->streamDownload(function () use ($spreadsheet) {
			$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
			$writer->save('php://output');
		}, $filename, [
			'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		]);
	}

	private function build_rekap_bulanan_data(Request $request)
	{
		$id_semester = get_semester('active_semester_id');

		$data['ref_kelas'] = Kelas::orderBy('kelas')->get()->pluck('kelas', 'id');
		$data['id_kelas']  = $request->get('id_kelas');
		$data['bulan']     = $request->get('bulan');
		$data['tahun']     = $request->get('tahun', date('Y'));

		$data['siswa']        = collect();
		$data['rekap']        = [];
		$data['summary']      = [];
		$data['jumlah_hari']  = 0;
		$data['hari_efektif'] = 0;

		if ($data['id_kelas'] && $data['bulan']) {
			$tahun = $data['tahun'];
			$bulan = $data['bulan'];

			$data['jumlah_hari'] = (int) date('t', mktime(0, 0, 0, (int) $bulan, 1, (int) $tahun));

			$hari_efektif = 0;
			for ($d = 1; $d <= $data['jumlah_hari']; $d++) {
				if (!in_array(date('N', mktime(0, 0, 0, (int) $bulan, $d, (int) $tahun)), [6, 7])) {
					$hari_efektif++;
				}
			}
			$data['hari_efektif'] = $hari_efektif;

			$data['siswa'] = Pesertadidik::get_pd_by_idkelas($data['id_kelas'], $id_semester);

			$rows = PresensiHarian::rekap_bulanan($data['id_kelas'], $id_semester, $tahun, $bulan);
			$matriks = [];
			$summary = [];
			foreach ($rows as $row) {
				$matriks[$row->id_siswa][$row->tanggal] = $row->status;

				if (!isset($summary[$row->id_siswa])) {
					$summary[$row->id_siswa] = ['hadir' => 0, 'sakit' => 0, 'ijin' => 0];
				}
				$sl = strtolower($row->status_lengkap);
				if (in_array($sl, ['hadir', 'terlambat'])) {
					$summary[$row->id_siswa]['hadir']++;
				} elseif ($sl === 'sakit') {
					$summary[$row->id_siswa]['sakit']++;
				} elseif (in_array($sl, ['ijin', 'izin'])) {
					$summary[$row->id_siswa]['ijin']++;
				}
			}
			$data['rekap']   = $matriks;
			$data['summary'] = $summary;
		}

		return $data;
	}

	public function create(Request $request)
	{
		$ref_siswa = Siswa::all()->pluck('nama_siswa','id');
		$ref_statuskehadiran = Statuskehadiran::all()->pluck('status_kehadiran','id');
		
		$data['forms'] = array(
			'id_siswa' => ['Siswa', Form::select("id_siswa", $ref_siswa, null, ["class" => "form-control select2"]) ],
			'id_status_kehadiran' => ['Status Kehadiran', Form::select("id_status_kehadiran", $ref_statuskehadiran, null, ["class" => "form-control select2"]) ],
			'tgl' => ['Tgl', Form::text("tgl", old("tgl"), ["class" => "form-control datepicker"]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('PresensiHarian::presensiharian_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_siswa' => 'required',
			'id_status_kehadiran' => 'required',
			'tgl' => 'required',
			
		]);

		$presensiharian = new PresensiHarian();
		$presensiharian->id_siswa = $request->input("id_siswa");
		$presensiharian->id_status_kehadiran = $request->input("id_status_kehadiran");
		$presensiharian->tgl = $request->input("tgl");
		
		$presensiharian->created_by = Auth::id();
		$presensiharian->save();

		$text = 'membuat '.$this->title; //' baru '.$presensiharian->what;
		$this->log($request, $text, ['presensiharian.id' => $presensiharian->id]);
		return redirect()->route('presensiharian.index')->with('message_success', 'Presensi Harian berhasil ditambahkan!');
	}

	public function show(Request $request, PresensiHarian $presensiharian)
	{
		$data['presensiharian'] = $presensiharian;

		$text = 'melihat detail '.$this->title;//.' '.$presensiharian->what;
		$this->log($request, $text, ['presensiharian.id' => $presensiharian->id]);
		return view('PresensiHarian::presensiharian_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, PresensiHarian $presensiharian)
	{
		$data['presensiharian'] = $presensiharian;

		$ref_siswa = Siswa::all()->pluck('nama_siswa','id');
		$ref_statuskehadiran = Statuskehadiran::all()->pluck('status_kehadiran','id');
		
		$data['forms'] = array(
			'id_siswa' => ['Siswa', Form::select("id_siswa", $ref_siswa, null, ["class" => "form-control select2"]) ],
			'id_status_kehadiran' => ['Status Kehadiran', Form::select("id_status_kehadiran", $ref_statuskehadiran, null, ["class" => "form-control select2"]) ],
			'tgl' => ['Tgl', Form::text("tgl", $presensiharian->tgl, ["class" => "form-control datepicker", "id" => "tgl"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$presensiharian->what;
		$this->log($request, $text, ['presensiharian.id' => $presensiharian->id]);
		return view('PresensiHarian::presensiharian_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_siswa' => 'required',
			'id_status_kehadiran' => 'required',
			'tgl' => 'required',
			
		]);
		
		$presensiharian = PresensiHarian::find($id);
		$presensiharian->id_siswa = $request->input("id_siswa");
		$presensiharian->id_status_kehadiran = $request->input("id_status_kehadiran");
		$presensiharian->tgl = $request->input("tgl");
		
		$presensiharian->updated_by = Auth::id();
		$presensiharian->save();


		$text = 'mengedit '.$this->title;//.' '.$presensiharian->what;
		$this->log($request, $text, ['presensiharian.id' => $presensiharian->id]);
		return redirect()->route('presensiharian.index')->with('message_success', 'Presensi Harian berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$presensiharian = PresensiHarian::find($id);
		$presensiharian->deleted_by = Auth::id();
		$presensiharian->save();
		$presensiharian->delete();

		$text = 'menghapus '.$this->title;//.' '.$presensiharian->what;
		$this->log($request, $text, ['presensiharian.id' => $presensiharian->id]);
		return back()->with('message_success', 'Presensi Harian berhasil dihapus!');
	}

}
