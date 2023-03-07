<?php
namespace App\Modules\Siswa\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Siswa\Models\Siswa;
use App\Modules\Jeniskelamin\Models\Jeniskelamin;
use App\Modules\Agama\Models\Agama;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SiswaController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Siswa";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = Siswa::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Siswa::siswa', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$ref_jeniskelamin = Jeniskelamin::all()->pluck('jeniskelamin','id');
		$ref_agama = Agama::all()->pluck('agama','id');
		
		$data['forms'] = array(
			'nama_siswa' => ['Nama Siswa', Form::text("nama_siswa", old("nama_siswa"), ["class" => "form-control","placeholder" => ""]) ],
			'nis' => ['Nis', Form::text("nis", old("nis"), ["class" => "form-control","placeholder" => ""]) ],
			'nisn' => ['Nisn', Form::text("nisn", old("nisn"), ["class" => "form-control","placeholder" => ""]) ],
			'nik' => ['Nik', Form::text("nik", old("nik"), ["class" => "form-control","placeholder" => ""]) ],
			'id_jeniskelamin' => ['Jeniskelamin', Form::select("id_jeniskelamin", $ref_jeniskelamin, null, ["class" => "form-control select2"]) ],
			'id_agama' => ['Agama', Form::select("id_agama", $ref_agama, null, ["class" => "form-control select2"]) ],
			'tahun_masuk' => ['Tahun Masuk', Form::text("tahun_masuk", old("tahun_masuk"), ["class" => "form-control","placeholder" => ""]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Siswa::siswa_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'nama_siswa' => 'required',
			'nis' => 'required',
			'nisn' => 'required',
			'nik' => 'required',
			'id_jeniskelamin' => 'required',
			'id_agama' => 'required',
			'tahun_masuk' => 'required',
			
		]);

		$siswa = new Siswa();
		$siswa->nama_siswa = $request->input("nama_siswa");
		$siswa->nis = $request->input("nis");
		$siswa->nisn = $request->input("nisn");
		$siswa->nik = $request->input("nik");
		$siswa->id_jeniskelamin = $request->input("id_jeniskelamin");
		$siswa->id_agama = $request->input("id_agama");
		$siswa->tahun_masuk = $request->input("tahun_masuk");
		
		$siswa->created_by = Auth::id();
		$siswa->save();

		$text = 'membuat '.$this->title; //' baru '.$siswa->what;
		$this->log($request, $text, ['siswa.id' => $siswa->id]);
		return redirect()->route('siswa.index')->with('message_success', 'Siswa berhasil ditambahkan!');
	}

	public function show(Request $request, Siswa $siswa)
	{
		$data['siswa'] = $siswa;

		$text = 'melihat detail '.$this->title;//.' '.$siswa->what;
		$this->log($request, $text, ['siswa.id' => $siswa->id]);
		return view('Siswa::siswa_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Siswa $siswa)
	{
		$data['siswa'] = $siswa;

		$ref_jeniskelamin = Jeniskelamin::all()->pluck('jeniskelamin','id');
		$ref_agama = Agama::all()->pluck('agama','id');
		
		$data['forms'] = array(
			'nama_siswa' => ['Nama Siswa', Form::text("nama_siswa", $siswa->nama_siswa, ["class" => "form-control","placeholder" => "", "id" => "nama_siswa"]) ],
			'nis' => ['Nis', Form::text("nis", $siswa->nis, ["class" => "form-control","placeholder" => "", "id" => "nis"]) ],
			'nisn' => ['Nisn', Form::text("nisn", $siswa->nisn, ["class" => "form-control","placeholder" => "", "id" => "nisn"]) ],
			'nik' => ['Nik', Form::text("nik", $siswa->nik, ["class" => "form-control","placeholder" => "", "id" => "nik"]) ],
			'id_jeniskelamin' => ['Jeniskelamin', Form::select("id_jeniskelamin", $ref_jeniskelamin, null, ["class" => "form-control select2"]) ],
			'id_agama' => ['Agama', Form::select("id_agama", $ref_agama, null, ["class" => "form-control select2"]) ],
			'tahun_masuk' => ['Tahun Masuk', Form::text("tahun_masuk", $siswa->tahun_masuk, ["class" => "form-control","placeholder" => "", "id" => "tahun_masuk"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$siswa->what;
		$this->log($request, $text, ['siswa.id' => $siswa->id]);
		return view('Siswa::siswa_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'nama_siswa' => 'required',
			'nis' => 'required',
			'nisn' => 'required',
			'nik' => 'required',
			'id_jeniskelamin' => 'required',
			'id_agama' => 'required',
			'tahun_masuk' => 'required',
			
		]);
		
		$siswa = Siswa::find($id);
		$siswa->nama_siswa = $request->input("nama_siswa");
		$siswa->nis = $request->input("nis");
		$siswa->nisn = $request->input("nisn");
		$siswa->nik = $request->input("nik");
		$siswa->id_jeniskelamin = $request->input("id_jeniskelamin");
		$siswa->id_agama = $request->input("id_agama");
		$siswa->tahun_masuk = $request->input("tahun_masuk");
		
		$siswa->updated_by = Auth::id();
		$siswa->save();


		$text = 'mengedit '.$this->title;//.' '.$siswa->what;
		$this->log($request, $text, ['siswa.id' => $siswa->id]);
		return redirect()->route('siswa.index')->with('message_success', 'Siswa berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$siswa = Siswa::find($id);
		$siswa->deleted_by = Auth::id();
		$siswa->save();
		$siswa->delete();

		$text = 'menghapus '.$this->title;//.' '.$siswa->what;
		$this->log($request, $text, ['siswa.id' => $siswa->id]);
		return back()->with('message_success', 'Siswa berhasil dihapus!');
	}

}
