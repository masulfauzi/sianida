<?php
namespace App\Modules\Ijin\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Ijin\Models\Ijin;
use App\Modules\JenisIjin\Models\JenisIjin;
use App\Modules\Siswa\Models\Siswa;
use App\Modules\StatusIjin\Models\StatusIjin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class IjinController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Ijin";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = Ijin::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Ijin::ijin', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$ref_jenis_ijin = JenisIjin::all()->pluck('created_by','id');
		$ref_siswa = Siswa::all()->pluck('created_at','id');
		$ref_status_ijin = StatusIjin::all()->pluck('created_by','id');
		
		$data['forms'] = array(
			'id_jenis_ijin' => ['Jenis Ijin', Form::select("id_jenis_ijin", $ref_jenis_ijin, null, ["class" => "form-control select2"]) ],
			'id_siswa' => ['Siswa', Form::select("id_siswa", $ref_siswa, null, ["class" => "form-control select2"]) ],
			'id_status_ijin' => ['Status Ijin', Form::select("id_status_ijin", $ref_status_ijin, null, ["class" => "form-control select2"]) ],
			'lama_ijin' => ['Lama Ijin', Form::text("lama_ijin", old("lama_ijin"), ["class" => "form-control","placeholder" => ""]) ],
			'tgl_mulai' => ['Tgl Mulai', Form::text("tgl_mulai", old("tgl_mulai"), ["class" => "form-control datepicker"]) ],
			'tgl_selesai' => ['Tgl Selesai', Form::text("tgl_selesai", old("tgl_selesai"), ["class" => "form-control datepicker"]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Ijin::ijin_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_jenis_ijin' => 'required',
			'id_siswa' => 'required',
			'id_status_ijin' => 'required',
			'lama_ijin' => 'required',
			'tgl_mulai' => 'required',
			'tgl_selesai' => 'required',
			
		]);

		$ijin = new Ijin();
		$ijin->id_jenis_ijin = $request->input("id_jenis_ijin");
		$ijin->id_siswa = $request->input("id_siswa");
		$ijin->id_status_ijin = $request->input("id_status_ijin");
		$ijin->lama_ijin = $request->input("lama_ijin");
		$ijin->tgl_mulai = $request->input("tgl_mulai");
		$ijin->tgl_selesai = $request->input("tgl_selesai");
		
		$ijin->created_by = Auth::id();
		$ijin->save();

		$text = 'membuat '.$this->title; //' baru '.$ijin->what;
		$this->log($request, $text, ['ijin.id' => $ijin->id]);
		return redirect()->route('ijin.index')->with('message_success', 'Ijin berhasil ditambahkan!');
	}

	public function show(Request $request, Ijin $ijin)
	{
		$data['ijin'] = $ijin;

		$text = 'melihat detail '.$this->title;//.' '.$ijin->what;
		$this->log($request, $text, ['ijin.id' => $ijin->id]);
		return view('Ijin::ijin_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Ijin $ijin)
	{
		$data['ijin'] = $ijin;

		$ref_jenis_ijin = JenisIjin::all()->pluck('created_by','id');
		$ref_siswa = Siswa::all()->pluck('created_at','id');
		$ref_status_ijin = StatusIjin::all()->pluck('created_by','id');
		
		$data['forms'] = array(
			'id_jenis_ijin' => ['Jenis Ijin', Form::select("id_jenis_ijin", $ref_jenis_ijin, null, ["class" => "form-control select2"]) ],
			'id_siswa' => ['Siswa', Form::select("id_siswa", $ref_siswa, null, ["class" => "form-control select2"]) ],
			'id_status_ijin' => ['Status Ijin', Form::select("id_status_ijin", $ref_status_ijin, null, ["class" => "form-control select2"]) ],
			'lama_ijin' => ['Lama Ijin', Form::text("lama_ijin", $ijin->lama_ijin, ["class" => "form-control","placeholder" => "", "id" => "lama_ijin"]) ],
			'tgl_mulai' => ['Tgl Mulai', Form::text("tgl_mulai", $ijin->tgl_mulai, ["class" => "form-control datepicker", "id" => "tgl_mulai"]) ],
			'tgl_selesai' => ['Tgl Selesai', Form::text("tgl_selesai", $ijin->tgl_selesai, ["class" => "form-control datepicker", "id" => "tgl_selesai"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$ijin->what;
		$this->log($request, $text, ['ijin.id' => $ijin->id]);
		return view('Ijin::ijin_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_jenis_ijin' => 'required',
			'id_siswa' => 'required',
			'id_status_ijin' => 'required',
			'lama_ijin' => 'required',
			'tgl_mulai' => 'required',
			'tgl_selesai' => 'required',
			
		]);
		
		$ijin = Ijin::find($id);
		$ijin->id_jenis_ijin = $request->input("id_jenis_ijin");
		$ijin->id_siswa = $request->input("id_siswa");
		$ijin->id_status_ijin = $request->input("id_status_ijin");
		$ijin->lama_ijin = $request->input("lama_ijin");
		$ijin->tgl_mulai = $request->input("tgl_mulai");
		$ijin->tgl_selesai = $request->input("tgl_selesai");
		
		$ijin->updated_by = Auth::id();
		$ijin->save();


		$text = 'mengedit '.$this->title;//.' '.$ijin->what;
		$this->log($request, $text, ['ijin.id' => $ijin->id]);
		return redirect()->route('ijin.index')->with('message_success', 'Ijin berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$ijin = Ijin::find($id);
		$ijin->deleted_by = Auth::id();
		$ijin->save();
		$ijin->delete();

		$text = 'menghapus '.$this->title;//.' '.$ijin->what;
		$this->log($request, $text, ['ijin.id' => $ijin->id]);
		return back()->with('message_success', 'Ijin berhasil dihapus!');
	}

}
