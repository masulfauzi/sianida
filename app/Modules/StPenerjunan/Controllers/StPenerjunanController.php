<?php
namespace App\Modules\StPenerjunan\Controllers;

use Form;
use Carbon\Carbon;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Guru\Models\Guru;
use App\Modules\Magang\Models\Magang;
use App\Modules\StPenerjunan\Models\StPenerjunan;
use App\Modules\PeriodeMagang\Models\PeriodeMagang;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StPenerjunanController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "St Penerjunan";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = StPenerjunan::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('StPenerjunan::stpenerjunan', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$ref_periode_magang = PeriodeMagang::all()->pluck('nama_periode','id');
		$ref_periode_magang->prepend('-PILIH SALAH SATU-', '');
		
		$data['forms'] = array(
			'id_periode' => ['Periode', Form::select("id_periode", $ref_periode_magang, null, ["class" => "form-control select2"]) ],
			'no_surat' => ['No Surat', Form::text("no_surat", old("no_surat"), ["class" => "form-control","placeholder" => ""]) ],
			'tgl_surat' => ['Tgl Surat', Form::text("tgl_surat", old("tgl_surat"), ["class" => "form-control datepicker"]) ],
			'tgl_penerjunan' => ['Tgl Penerjunan', Form::text("tgl_penerjunan", old("tgl_penerjunan"), ["class" => "form-control datepicker"]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('StPenerjunan::stpenerjunan_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_periode' => 'required',
			'no_surat' => 'required',
			'tgl_surat' => 'required',
			'tgl_penerjunan' => 'required',
			
		]);

		$stpenerjunan = new StPenerjunan();
		$stpenerjunan->id_periode = $request->input("id_periode");
		$stpenerjunan->no_surat = $request->input("no_surat");
		$stpenerjunan->tgl_surat = $request->input("tgl_surat");
		$stpenerjunan->tgl_penerjunan = $request->input("tgl_penerjunan");
		
		$stpenerjunan->created_by = Auth::id();
		$stpenerjunan->save();

		$text = 'membuat '.$this->title; //' baru '.$stpenerjunan->what;
		$this->log($request, $text, ['stpenerjunan.id' => $stpenerjunan->id]);
		return redirect()->route('stpenerjunan.index')->with('message_success', 'St Penerjunan berhasil ditambahkan!');
	}

	public function show(Request $request, StPenerjunan $stpenerjunan)
	{
		$data['stpenerjunan'] = $stpenerjunan;
		$data['gurus'] = $stpenerjunan->periode->magang()->with('pembimbing')->get()
			->pluck('pembimbing')->filter()->unique('id')->values();

		$text = 'melihat detail '.$this->title;//.' '.$stpenerjunan->what;
		$this->log($request, $text, ['stpenerjunan.id' => $stpenerjunan->id]);
		return view('StPenerjunan::stpenerjunan_detail', array_merge($data, ['title' => $this->title]));
	}

	public function suratTugas(Request $request, StPenerjunan $stpenerjunan, Guru $guru)
	{
		$tempats = Magang::where('id_periode_magang', $stpenerjunan->id_periode)
			->where('id_pembimbing', $guru->id)
			->with('dudi')
			->get()
			->pluck('dudi')
			->filter()
			->unique('id')
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
			'stpenerjunan' => $stpenerjunan,
			'guru' => $guru,
			'tempats' => $tempats,
			'tglPenerjunan' => $stpenerjunan->tgl_penerjunan ? $formatTanggal($stpenerjunan->tgl_penerjunan) : '-',
			'tglSurat' => $stpenerjunan->tgl_surat ? $formatTanggal($stpenerjunan->tgl_surat) : $formatTanggal(now()),
		];

		$text = 'mengunduh surat tugas '.$guru->nama.' pada '.$this->title;
		$this->log($request, $text, ['stpenerjunan.id' => $stpenerjunan->id, 'guru.id' => $guru->id]);

		$pdf = Pdf::loadView('StPenerjunan::stpenerjunan_surat_tugas', $data);
		return $pdf->download('Surat Tugas - '.$guru->nama.'.pdf');
	}

	public function edit(Request $request, StPenerjunan $stpenerjunan)
	{
		$data['stpenerjunan'] = $stpenerjunan;

		$ref_periode_magang = PeriodeMagang::all()->pluck('nama_periode','id');
		
		$data['forms'] = array(
			'id_periode' => ['Periode', Form::select("id_periode", $ref_periode_magang, null, ["class" => "form-control select2"]) ],
			'no_surat' => ['No Surat', Form::text("no_surat", $stpenerjunan->no_surat, ["class" => "form-control","placeholder" => "", "id" => "no_surat"]) ],
			'tgl_surat' => ['Tgl Surat', Form::text("tgl_surat", $stpenerjunan->tgl_surat, ["class" => "form-control datepicker", "id" => "tgl_surat"]) ],
			'tgl_penerjunan' => ['Tgl Penerjunan', Form::text("tgl_penerjunan", $stpenerjunan->tgl_penerjunan, ["class" => "form-control datepicker", "id" => "tgl_penerjunan"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$stpenerjunan->what;
		$this->log($request, $text, ['stpenerjunan.id' => $stpenerjunan->id]);
		return view('StPenerjunan::stpenerjunan_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_periode' => 'required',
			'no_surat' => 'required',
			'tgl_surat' => 'required',
			'tgl_penerjunan' => 'required',
			
		]);
		
		$stpenerjunan = StPenerjunan::find($id);
		$stpenerjunan->id_periode = $request->input("id_periode");
		$stpenerjunan->no_surat = $request->input("no_surat");
		$stpenerjunan->tgl_surat = $request->input("tgl_surat");
		$stpenerjunan->tgl_penerjunan = $request->input("tgl_penerjunan");
		
		$stpenerjunan->updated_by = Auth::id();
		$stpenerjunan->save();


		$text = 'mengedit '.$this->title;//.' '.$stpenerjunan->what;
		$this->log($request, $text, ['stpenerjunan.id' => $stpenerjunan->id]);
		return redirect()->route('stpenerjunan.index')->with('message_success', 'St Penerjunan berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$stpenerjunan = StPenerjunan::find($id);
		$stpenerjunan->deleted_by = Auth::id();
		$stpenerjunan->save();
		$stpenerjunan->delete();

		$text = 'menghapus '.$this->title;//.' '.$stpenerjunan->what;
		$this->log($request, $text, ['stpenerjunan.id' => $stpenerjunan->id]);
		return back()->with('message_success', 'St Penerjunan berhasil dihapus!');
	}

}
