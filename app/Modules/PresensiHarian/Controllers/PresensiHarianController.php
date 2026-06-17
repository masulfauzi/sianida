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
		$id_semester = get_semester('active_semester_id');

		$data['ref_kelas'] = Kelas::orderBy('kelas')->get()->pluck('kelas', 'id');
		$data['id_kelas']  = $request->get('id_kelas');
		$data['bulan']     = $request->get('bulan');
		$data['tahun']     = $request->get('tahun', date('Y'));

		$data['siswa']       = collect();
		$data['rekap']       = [];
		$data['jumlah_hari'] = 0;

		if ($data['id_kelas'] && $data['bulan']) {
			$tahun = $data['tahun'];
			$bulan = $data['bulan'];

			$data['jumlah_hari'] = (int) date('t', mktime(0, 0, 0, (int) $bulan, 1, (int) $tahun));

			$data['siswa'] = Pesertadidik::get_pd_by_idkelas($data['id_kelas'], $id_semester);

			$rows = PresensiHarian::rekap_bulanan($data['id_kelas'], $id_semester, $tahun, $bulan);
			$matriks = [];
			foreach ($rows as $row) {
				$matriks[$row->id_siswa][$row->tanggal] = $row->status;
			}
			$data['rekap'] = $matriks;
		}

		$this->log($request, 'melihat rekap bulanan '.$this->title);
		return view('PresensiHarian::presensiharian_rekap_bulanan', array_merge($data, ['title' => $this->title]));
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
