<?php
namespace App\Modules\SuratDudi\Controllers;

use Form;
use Carbon\Carbon;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Dudi\Models\Dudi;
use App\Modules\Magang\Models\Magang;
use App\Modules\SuratDudi\Models\SuratDudi;
use App\Modules\PeriodeMagang\Models\PeriodeMagang;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SuratDudiController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Surat Dudi";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = SuratDudi::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('SuratDudi::suratdudi', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$ref_periode_magang = PeriodeMagang::all()->pluck('nama_periode','id');
		$ref_periode_magang->prepend('-PILIH SALAH SATU-', '');
		
		$data['forms'] = array(
			'id_periode' => ['Periode', Form::select("id_periode", $ref_periode_magang, null, ["class" => "form-control select2"]) ],
			'no_surat' => ['No Surat', Form::text("no_surat", old("no_surat"), ["class" => "form-control","placeholder" => ""]) ],
			'tgl_surat' => ['Tgl Surat', Form::text("tgl_surat", old("tgl_surat"), ["class" => "form-control datepicker"]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('SuratDudi::suratdudi_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_periode' => 'required',
			'tgl_surat' => 'required',
			'no_surat' => 'required',
			
		]);

		$suratdudi = new SuratDudi();
		$suratdudi->id_periode = $request->input("id_periode");
		$suratdudi->tgl_surat = $request->input("tgl_surat");
		$suratdudi->no_surat = $request->input("no_surat");
		
		$suratdudi->created_by = Auth::id();
		$suratdudi->save();

		$text = 'membuat '.$this->title; //' baru '.$suratdudi->what;
		$this->log($request, $text, ['suratdudi.id' => $suratdudi->id]);
		return redirect()->route('suratdudi.index')->with('message_success', 'Surat Dudi berhasil ditambahkan!');
	}

	public function show(Request $request, SuratDudi $suratdudi)
	{
		$data['suratdudi'] = $suratdudi;
		$data['dudis'] = $suratdudi->periode->magang()->with('dudi')->get()
			->pluck('dudi')->filter()->unique('id')->values();

		$text = 'melihat detail '.$this->title;//.' '.$suratdudi->what;
		$this->log($request, $text, ['suratdudi.id' => $suratdudi->id]);
		return view('SuratDudi::suratdudi_detail', array_merge($data, ['title' => $this->title]));
	}

	public function cetakSurat(Request $request, SuratDudi $suratdudi, Dudi $dudi)
	{
		$periode = $suratdudi->periode;

		$siswas = Magang::where('id_periode_magang', $suratdudi->id_periode)
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
			'suratdudi' => $suratdudi,
			'dudi' => $dudi,
			'siswas' => $siswas,
			'tglMulai' => $periode->tgl_mulai ? $formatTanggal($periode->tgl_mulai) : '-',
			'tglSelesai' => $periode->tgl_selesai ? $formatTanggal($periode->tgl_selesai) : '-',
			'tglSurat' => $suratdudi->tgl_surat ? $formatTanggal($suratdudi->tgl_surat) : $formatTanggal(now()),
		];

		$text = 'mengunduh surat '.$dudi->nama_dudi.' pada '.$this->title;
		$this->log($request, $text, ['suratdudi.id' => $suratdudi->id, 'dudi.id' => $dudi->id]);

		$pdf = Pdf::loadView('SuratDudi::suratdudi_surat', $data);
		return $pdf->download('Surat Penerjunan - '.$dudi->nama_dudi.'.pdf');
	}

	public function edit(Request $request, SuratDudi $suratdudi)
	{
		$data['suratdudi'] = $suratdudi;

		$ref_periode_magang = PeriodeMagang::all()->pluck('nama_periode','id');
		
		$data['forms'] = array(
			'id_periode' => ['Periode', Form::select("id_periode", $ref_periode_magang, null, ["class" => "form-control select2"]) ],
			'tgl_surat' => ['Tgl Surat', Form::text("tgl_surat", $suratdudi->tgl_surat, ["class" => "form-control datepicker", "id" => "tgl_surat"]) ],
			'no_surat' => ['No Surat', Form::text("no_surat", $suratdudi->no_surat, ["class" => "form-control","placeholder" => "", "id" => "no_surat"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$suratdudi->what;
		$this->log($request, $text, ['suratdudi.id' => $suratdudi->id]);
		return view('SuratDudi::suratdudi_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_periode' => 'required',
			'tgl_surat' => 'required',
			'no_surat' => 'required',
			
		]);
		
		$suratdudi = SuratDudi::find($id);
		$suratdudi->id_periode = $request->input("id_periode");
		$suratdudi->tgl_surat = $request->input("tgl_surat");
		$suratdudi->no_surat = $request->input("no_surat");
		
		$suratdudi->updated_by = Auth::id();
		$suratdudi->save();


		$text = 'mengedit '.$this->title;//.' '.$suratdudi->what;
		$this->log($request, $text, ['suratdudi.id' => $suratdudi->id]);
		return redirect()->route('suratdudi.index')->with('message_success', 'Surat Dudi berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$suratdudi = SuratDudi::find($id);
		$suratdudi->deleted_by = Auth::id();
		$suratdudi->save();
		$suratdudi->delete();

		$text = 'menghapus '.$this->title;//.' '.$suratdudi->what;
		$this->log($request, $text, ['suratdudi.id' => $suratdudi->id]);
		return back()->with('message_success', 'Surat Dudi berhasil dihapus!');
	}

}
