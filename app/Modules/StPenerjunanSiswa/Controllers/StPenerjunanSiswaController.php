<?php
namespace App\Modules\StPenerjunanSiswa\Controllers;

use Form;
use Carbon\Carbon;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Dudi\Models\Dudi;
use App\Modules\Magang\Models\Magang;
use App\Modules\StPenerjunanSiswa\Models\StPenerjunanSiswa;
use App\Modules\PeriodeMagang\Models\PeriodeMagang;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StPenerjunanSiswaController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "St Penerjunan Siswa";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = StPenerjunanSiswa::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('StPenerjunanSiswa::stpenerjunansiswa', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$ref_periode_magang = PeriodeMagang::all()->pluck('nama_periode','id');
		$ref_periode_magang->prepend('-PILIH SALAH SATU-', '');
		
		$data['forms'] = array(
			'id_periode' => ['Periode', Form::select("id_periode", $ref_periode_magang, null, ["class" => "form-control select2"]) ],
			'tgl_surat' => ['Tgl Surat', Form::text("tgl_surat", old("tgl_surat"), ["class" => "form-control datepicker"]) ],
			'no_surat' => ['No Surat', Form::text("no_surat", old("no_surat"), ["class" => "form-control","placeholder" => ""]) ],

		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('StPenerjunanSiswa::stpenerjunansiswa_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_periode' => 'required',
			'tgl_surat' => 'required',
			'no_surat' => 'required',

		]);

		$stpenerjunansiswa = new StPenerjunanSiswa();
		$stpenerjunansiswa->id_periode = $request->input("id_periode");
		$stpenerjunansiswa->tgl_surat = $request->input("tgl_surat");
		$stpenerjunansiswa->no_surat = $request->input("no_surat");
		
		$stpenerjunansiswa->created_by = Auth::id();
		$stpenerjunansiswa->save();

		$text = 'membuat '.$this->title; //' baru '.$stpenerjunansiswa->what;
		$this->log($request, $text, ['stpenerjunansiswa.id' => $stpenerjunansiswa->id]);
		return redirect()->route('stpenerjunansiswa.index')->with('message_success', 'St Penerjunan Siswa berhasil ditambahkan!');
	}

	public function show(Request $request, StPenerjunanSiswa $stpenerjunansiswa)
	{
		$data['stpenerjunansiswa'] = $stpenerjunansiswa;
		$data['dudis'] = $stpenerjunansiswa->periode->magang()->with(['dudi', 'pesertadidik.siswa'])->get()
			->filter(fn($magang) => $magang->dudi)
			->groupBy('id_dudi')
			->map(function ($items) {
				$dudi = $items->first()->dudi;
				$dudi->siswas = $items->pluck('pesertadidik.siswa.nama_siswa')->filter()->values();
				return $dudi;
			})
			->values();

		$text = 'melihat detail '.$this->title;//.' '.$stpenerjunansiswa->what;
		$this->log($request, $text, ['stpenerjunansiswa.id' => $stpenerjunansiswa->id]);
		return view('StPenerjunanSiswa::stpenerjunansiswa_detail', array_merge($data, ['title' => $this->title]));
	}

	public function suratTugas(Request $request, StPenerjunanSiswa $stpenerjunansiswa, Dudi $dudi)
	{
		$periode = $stpenerjunansiswa->periode;

		$siswas = Magang::where('id_periode_magang', $stpenerjunansiswa->id_periode)
			->where('id_dudi', $dudi->id)
			->with(['pesertadidik.siswa', 'pesertadidik.kelas'])
			->get()
			->pluck('pesertadidik')
			->filter()
			->values();

		$bulanIndonesia = [
			'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
			'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
		];
		$formatTanggal = function ($tanggal) use ($bulanIndonesia) {
			$date = Carbon::parse($tanggal);
			return $date->day.' '.$bulanIndonesia[$date->month - 1].' '.$date->year;
		};

		$data = [
			'stpenerjunansiswa' => $stpenerjunansiswa,
			'dudi' => $dudi,
			'siswas' => $siswas,
			'tglMulai' => $periode->tgl_mulai ? $formatTanggal($periode->tgl_mulai) : '-',
			'tglSelesai' => $periode->tgl_selesai ? $formatTanggal($periode->tgl_selesai) : '-',
			'tglSurat' => $stpenerjunansiswa->tgl_surat ? $formatTanggal($stpenerjunansiswa->tgl_surat) : $formatTanggal(now()),
		];

		$text = 'mengunduh surat tugas siswa '.$dudi->nama_dudi.' pada '.$this->title;
		$this->log($request, $text, ['stpenerjunansiswa.id' => $stpenerjunansiswa->id, 'dudi.id' => $dudi->id]);

		$pdf = Pdf::loadView('StPenerjunanSiswa::stpenerjunansiswa_surat_tugas', $data);
		return $pdf->download('Surat Tugas Siswa - '.$dudi->nama_dudi.'.pdf');
	}

	public function edit(Request $request, StPenerjunanSiswa $stpenerjunansiswa)
	{
		$data['stpenerjunansiswa'] = $stpenerjunansiswa;

		$ref_periode_magang = PeriodeMagang::all()->pluck('nama_periode','id');
		
		$data['forms'] = array(
			'id_periode' => ['Periode', Form::select("id_periode", $ref_periode_magang, null, ["class" => "form-control select2"]) ],
			'tgl_surat' => ['Tgl Surat', Form::text("tgl_surat", $stpenerjunansiswa->tgl_surat, ["class" => "form-control datepicker", "id" => "tgl_surat"]) ],
			'no_surat' => ['No Surat', Form::text("no_surat", $stpenerjunansiswa->no_surat, ["class" => "form-control","placeholder" => "", "id" => "no_surat"]) ],

		);

		$text = 'membuka form edit '.$this->title;//.' '.$stpenerjunansiswa->what;
		$this->log($request, $text, ['stpenerjunansiswa.id' => $stpenerjunansiswa->id]);
		return view('StPenerjunanSiswa::stpenerjunansiswa_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_periode' => 'required',
			'tgl_surat' => 'required',
			'no_surat' => 'required',

		]);

		$stpenerjunansiswa = StPenerjunanSiswa::find($id);
		$stpenerjunansiswa->id_periode = $request->input("id_periode");
		$stpenerjunansiswa->tgl_surat = $request->input("tgl_surat");
		$stpenerjunansiswa->no_surat = $request->input("no_surat");

		$stpenerjunansiswa->updated_by = Auth::id();
		$stpenerjunansiswa->save();


		$text = 'mengedit '.$this->title;//.' '.$stpenerjunansiswa->what;
		$this->log($request, $text, ['stpenerjunansiswa.id' => $stpenerjunansiswa->id]);
		return redirect()->route('stpenerjunansiswa.index')->with('message_success', 'St Penerjunan Siswa berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$stpenerjunansiswa = StPenerjunanSiswa::find($id);
		$stpenerjunansiswa->deleted_by = Auth::id();
		$stpenerjunansiswa->save();
		$stpenerjunansiswa->delete();

		$text = 'menghapus '.$this->title;//.' '.$stpenerjunansiswa->what;
		$this->log($request, $text, ['stpenerjunansiswa.id' => $stpenerjunansiswa->id]);
		return back()->with('message_success', 'St Penerjunan Siswa berhasil dihapus!');
	}

}
