<?php
namespace App\Modules\PeriodeMagang\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\PeriodeMagang\Models\PeriodeMagang;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PeriodeMagangController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Periode Magang";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = PeriodeMagang::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('PeriodeMagang::periodemagang', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		
		$data['forms'] = array(
			'nama_periode' => ['Nama Periode', Form::text("nama_periode", old("nama_periode"), ["class" => "form-control","placeholder" => ""]) ],
			'tgl_mulai' => ['Tgl Mulai', Form::text("tgl_mulai", old("tgl_mulai"), ["class" => "form-control datepicker"]) ],
			'tgl_selesai' => ['Tgl Selesai', Form::text("tgl_selesai", old("tgl_selesai"), ["class" => "form-control datepicker"]) ],
			'id_semester' => ['', Form::hidden("id_semester", get_semester('active_semester_id')) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('PeriodeMagang::periodemagang_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'nama_periode' => 'required',
			'id_semester' => 'required',
			'tgl_mulai' => 'required',
			'tgl_selesai' => 'required',

		]);

		$periodemagang = new PeriodeMagang();
		$periodemagang->nama_periode = $request->input("nama_periode");
		$periodemagang->id_semester = $request->input("id_semester");
		$periodemagang->tgl_mulai = $request->input("tgl_mulai");
		$periodemagang->tgl_selesai = $request->input("tgl_selesai");

		$periodemagang->created_by = Auth::id();
		$periodemagang->save();

		$text = 'membuat '.$this->title; //' baru '.$periodemagang->what;
		$this->log($request, $text, ['periodemagang.id' => $periodemagang->id]);
		return redirect()->route('periodemagang.index')->with('message_success', 'Periode Magang berhasil ditambahkan!');
	}

	public function show(Request $request, PeriodeMagang $periodemagang)
	{
		$data['periodemagang'] = $periodemagang;
		$data['magang'] = $periodemagang->magang()->with(['dudi', 'pembimbing', 'pesertadidik.siswa'])->get();

		$text = 'melihat detail '.$this->title;//.' '.$periodemagang->what;
		$this->log($request, $text, ['periodemagang.id' => $periodemagang->id]);
		return view('PeriodeMagang::periodemagang_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, PeriodeMagang $periodemagang)
	{
		$data['periodemagang'] = $periodemagang;

		$data['forms'] = array(
			'nama_periode' => ['Nama Periode', Form::text("nama_periode", $periodemagang->nama_periode, ["class" => "form-control","placeholder" => "", "id" => "nama_periode"]) ],
			'tgl_mulai' => ['Tgl Mulai', Form::text("tgl_mulai", $periodemagang->tgl_mulai, ["class" => "form-control datepicker", "id" => "tgl_mulai"]) ],
			'tgl_selesai' => ['Tgl Selesai', Form::text("tgl_selesai", $periodemagang->tgl_selesai, ["class" => "form-control datepicker", "id" => "tgl_selesai"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$periodemagang->what;
		$this->log($request, $text, ['periodemagang.id' => $periodemagang->id]);
		return view('PeriodeMagang::periodemagang_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'nama_periode' => 'required',
			'tgl_mulai' => 'required',
			'tgl_selesai' => 'required',

		]);

		$periodemagang = PeriodeMagang::find($id);
		$periodemagang->nama_periode = $request->input("nama_periode");
		$periodemagang->tgl_mulai = $request->input("tgl_mulai");
		$periodemagang->tgl_selesai = $request->input("tgl_selesai");

		$periodemagang->updated_by = Auth::id();
		$periodemagang->save();


		$text = 'mengedit '.$this->title;//.' '.$periodemagang->what;
		$this->log($request, $text, ['periodemagang.id' => $periodemagang->id]);
		return redirect()->route('periodemagang.index')->with('message_success', 'Periode Magang berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$periodemagang = PeriodeMagang::find($id);
		$periodemagang->deleted_by = Auth::id();
		$periodemagang->save();
		$periodemagang->delete();

		$text = 'menghapus '.$this->title;//.' '.$periodemagang->what;
		$this->log($request, $text, ['periodemagang.id' => $periodemagang->id]);
		return back()->with('message_success', 'Periode Magang berhasil dihapus!');
	}

}
