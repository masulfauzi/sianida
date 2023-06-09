<?php
namespace App\Modules\PerangkatPembelajaran\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\PerangkatPembelajaran\Models\PerangkatPembelajaran;
use App\Modules\Guru\Models\Guru;
use App\Modules\Mapel\Models\Mapel;
use App\Modules\Tingkat\Models\Tingkat;
use App\Modules\Semester\Models\Semester;
use App\Modules\JenisPerangkat\Models\JenisPerangkat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PerangkatPembelajaranController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Perangkat Pembelajaran";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = PerangkatPembelajaran::query()->whereIdGuru(session('id_guru'));
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('PerangkatPembelajaran::perangkatpembelajaran', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$id_guru = session('id_guru');
		$ref_mapel = Mapel::all()->pluck('mapel','id');
		$ref_tingkat = Tingkat::all()->sortBy('tingkat')->pluck('tingkat','id');
		$semester = get_semester('active_semester_id');
		$ref_jenis_perangkat = JenisPerangkat::all()->pluck('jenis_perangkat','id');

		$ref_mapel->prepend('-PILIH SALAH SATU-', '');
		$ref_tingkat->prepend('-PILIH SALAH SATU-', '');
		$ref_jenis_perangkat->prepend('-PILIH SALAH SATU-', '');
		
		$data['forms'] = array(
			
			'id_mapel' => ['Mapel', Form::select("id_mapel", $ref_mapel, null, ["class" => "form-control select2"]) ],
			'id_tingkat' => ['Tingkat', Form::select("id_tingkat", $ref_tingkat, null, ["class" => "form-control select2"]) ],
			'id_jenis_perangkat' => ['Jenis Perangkat', Form::select("id_jenis_perangkat", $ref_jenis_perangkat, null, ["class" => "form-control select2"]) ],
			'file' => ['File', Form::file("file", ["class" => "form-control","placeholder" => ""]) ],
			
			'id_guru' => ['', Form::hidden("id_guru", $id_guru) ],
			'id_semester' => ['', Form::hidden("id_semester", $semester) ],
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('PerangkatPembelajaran::perangkatpembelajaran_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_guru' => 'required',
			'id_mapel' => 'required',
			'id_tingkat' => 'required',
			'id_semester' => 'required',
			'file' => 'required',
			'id_jenis_perangkat' => 'required',
			
		]);

		$fileName = time().'.'.$request->file->extension();  

        $request->file->move(public_path('uploads/perangkat/'), $fileName);

		$perangkatpembelajaran = new PerangkatPembelajaran();
		$perangkatpembelajaran->id_guru = $request->input("id_guru");
		$perangkatpembelajaran->id_mapel = $request->input("id_mapel");
		$perangkatpembelajaran->id_tingkat = $request->input("id_tingkat");
		$perangkatpembelajaran->id_semester = $request->input("id_semester");
		$perangkatpembelajaran->file = $fileName;
		$perangkatpembelajaran->id_jenis_perangkat = $request->input("id_jenis_perangkat");
		
		$perangkatpembelajaran->created_by = Auth::id();
		$perangkatpembelajaran->save();

		$text = 'membuat '.$this->title; //' baru '.$perangkatpembelajaran->what;
		$this->log($request, $text, ['perangkatpembelajaran.id' => $perangkatpembelajaran->id]);
		return redirect()->route('perangkatpembelajaran.index')->with('message_success', 'Perangkat Pembelajaran berhasil ditambahkan!');
	}

	public function show(Request $request, PerangkatPembelajaran $perangkatpembelajaran)
	{
		$data['perangkatpembelajaran'] = $perangkatpembelajaran;

		$text = 'melihat detail '.$this->title;//.' '.$perangkatpembelajaran->what;
		$this->log($request, $text, ['perangkatpembelajaran.id' => $perangkatpembelajaran->id]);
		return view('PerangkatPembelajaran::perangkatpembelajaran_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, PerangkatPembelajaran $perangkatpembelajaran)
	{
		$data['perangkatpembelajaran'] = $perangkatpembelajaran;

		$ref_guru = Guru::all()->pluck('nama','id');
		$ref_mapel = Mapel::all()->pluck('mapel','id');
		$ref_tingkat = Tingkat::all()->pluck('tingkat','id');
		$ref_semester = Semester::all()->pluck('semester','id');
		$ref_jenis_perangkat = JenisPerangkat::all()->pluck('jenis_perangkat','id');
		
		$data['forms'] = array(
			'id_guru' => ['Guru', Form::select("id_guru", $ref_guru, null, ["class" => "form-control select2"]) ],
			'id_mapel' => ['Mapel', Form::select("id_mapel", $ref_mapel, null, ["class" => "form-control select2"]) ],
			'id_tingkat' => ['Tingkat', Form::select("id_tingkat", $ref_tingkat, null, ["class" => "form-control select2"]) ],
			'id_semester' => ['Semester', Form::select("id_semester", $ref_semester, null, ["class" => "form-control select2"]) ],
			'file' => ['File', Form::text("file", $perangkatpembelajaran->file, ["class" => "form-control","placeholder" => "", "id" => "file"]) ],
			'id_jenis_perangkat' => ['Jenis Perangkat', Form::select("id_jenis_perangkat", $ref_jenis_perangkat, null, ["class" => "form-control select2"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$perangkatpembelajaran->what;
		$this->log($request, $text, ['perangkatpembelajaran.id' => $perangkatpembelajaran->id]);
		return view('PerangkatPembelajaran::perangkatpembelajaran_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_guru' => 'required',
			'id_mapel' => 'required',
			'id_tingkat' => 'required',
			'id_semester' => 'required',
			'file' => 'required',
			'id_jenis_perangkat' => 'required',
			
		]);
		
		$perangkatpembelajaran = PerangkatPembelajaran::find($id);
		$perangkatpembelajaran->id_guru = $request->input("id_guru");
		$perangkatpembelajaran->id_mapel = $request->input("id_mapel");
		$perangkatpembelajaran->id_tingkat = $request->input("id_tingkat");
		$perangkatpembelajaran->id_semester = $request->input("id_semester");
		$perangkatpembelajaran->file = $request->input("file");
		$perangkatpembelajaran->id_jenis_perangkat = $request->input("id_jenis_perangkat");
		
		$perangkatpembelajaran->updated_by = Auth::id();
		$perangkatpembelajaran->save();


		$text = 'mengedit '.$this->title;//.' '.$perangkatpembelajaran->what;
		$this->log($request, $text, ['perangkatpembelajaran.id' => $perangkatpembelajaran->id]);
		return redirect()->route('perangkatpembelajaran.index')->with('message_success', 'Perangkat Pembelajaran berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$perangkatpembelajaran = PerangkatPembelajaran::find($id);
		$perangkatpembelajaran->deleted_by = Auth::id();
		$perangkatpembelajaran->save();
		$perangkatpembelajaran->delete();

		$text = 'menghapus '.$this->title;//.' '.$perangkatpembelajaran->what;
		$this->log($request, $text, ['perangkatpembelajaran.id' => $perangkatpembelajaran->id]);
		return back()->with('message_success', 'Perangkat Pembelajaran berhasil dihapus!');
	}

}
