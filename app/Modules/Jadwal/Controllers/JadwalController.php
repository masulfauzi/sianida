<?php
namespace App\Modules\Jadwal\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Jadwal\Models\Jadwal;
use App\Modules\Guru\Models\Guru;
use App\Modules\Hari\Models\Hari;
use App\Modules\Kelas\Models\Kelas;
use App\Modules\Jampelajaran\Models\Jampelajaran;
use App\Modules\Mapel\Models\Mapel;
use App\Modules\Ruang\Models\Ruang;
use App\Modules\Semester\Models\Semester;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Jadwal";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		if(session('active_role')['id'] == '9ec7541e-5a5e-4a3a-a255-6ffb46895f46')
		{
			return redirect(route('jadwal.guru.index'));
		}

		// $query = Jadwal::query()->where('id_semester', get_semester('active_semester_id'))->orderBy(Guru::select('nama')->whereColumn('guru.id', 'jadwal.id_guru'));
		$query = Guru::query()->orderBy('nama');
		if($request->has('search')){
			$search = $request->get('search');
			$query->where('nama', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(20)->withQueryString();
		$data['jam_pelajaran'] = Jampelajaran::all()->pluck('jam_pelajaran','id');

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Jadwal::jadwal', array_merge($data, ['title' => $this->title]));
	}

	public function detail_guru(Request $request, $id_guru)
	{
		$query = Jadwal::query()->where('id_guru', $id_guru);
		$guru = Guru::find($id_guru);
		$ref_hari = Hari::all()->sortBy('urutan')->pluck('hari','id');
		$ref_kelas = Kelas::all()->sortBy('kelas')->pluck('kelas','id');
		$ref_jampelajaran = Jampelajaran::all()->sortBy('jam_pelajaran')->pluck('jam_pelajaran','id');
		$ref_mapel = Mapel::all()->sortBy('mapel')->pluck('mapel','id');
		$ref_ruang = Ruang::all()->sortBy('ruang')->pluck('ruang','id');
		
		$data['forms'] = array(
			'guru' => ['Guru', Form::text("guru", $guru->nama, ["class" => "form-control", "disabled"]) ],
			'id_hari' => ['Hari', Form::select("id_hari", $ref_hari, null, ["class" => "form-control select2"]) ],
			'id_kelas' => ['Kelas', Form::select("id_kelas", $ref_kelas, null, ["class" => "form-control select2"]) ],
			'jam_mulai' => ['Jam Mulai', Form::select("jam_mulai", $ref_jampelajaran, null, ["class" => "form-control select2"]) ],
			'jam_selesai' => ['Jam Selesai', Form::select("jam_selesai", $ref_jampelajaran, null, ["class" => "form-control select2"]) ],
			'id_mapel' => ['Mapel', Form::select("id_mapel", $ref_mapel, null, ["class" => "form-control select2"]) ],
			'id_ruang' => ['Ruang', Form::select("id_ruang", $ref_ruang, null, ["class" => "form-control select2"]) ],
			'id_semester' => ['', Form::hidden("id_semester", get_semester('active_semester_id')) ],
			'id_guru' => ['', Form::hidden("id_guru", $id_guru) ],
			
		);

		$data['jam_pelajaran'] 	= Jampelajaran::all()->sortBy('jam_pelajaran')->pluck('jam_pelajaran','id');
		$data['data']			= $query;
		$data['id_guru']		=  $id_guru;

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Jadwal::jadwal_detail_guru', array_merge($data, ['title' => $this->title]));
	}

	public function index_guru(Request $request)
	{
		$query = Jadwal::query()->where('id_guru', session('id_guru'));
		
		$data['data'] = $query;
		$data['jam_pelajaran'] = Jampelajaran::all()->sortBy('jam_pelajaran')->pluck('jam_pelajaran','id');

		$this->log($request, 'melihat halaman jadwal guru');
		return view('Jadwal::jadwalguru', array_merge($data, ['title' => 'Jadwal Guru']))->withModel($query);
	}

	public function create(Request $request)
	{
		$ref_guru = Guru::all()->sortBy('nama')->pluck('nama','id');
		$ref_hari = Hari::all()->sortBy('urutan')->pluck('hari','id');
		$ref_kelas = Kelas::all()->pluck('kelas','id');
		$ref_jampelajaran = Jampelajaran::all()->sortBy('jam_pelajaran')->pluck('jam_pelajaran','id');
		$ref_mapel = Mapel::all()->sortBy('mapel')->pluck('mapel','id');
		$ref_ruang = Ruang::all()->sortBy('ruang')->pluck('ruang','id');
		
		$data['forms'] = array(
			'id_guru' => ['Guru', Form::select("id_guru", $ref_guru, null, ["class" => "form-control select2"]) ],
			'id_hari' => ['Hari', Form::select("id_hari", $ref_hari, null, ["class" => "form-control select2"]) ],
			'id_kelas' => ['Kelas', Form::select("id_kelas", $ref_kelas, null, ["class" => "form-control select2"]) ],
			'jam_mulai' => ['Jam Mulai', Form::select("jam_mulai", $ref_jampelajaran, null, ["class" => "form-control select2"]) ],
			'jam_selesai' => ['Jam Selesai', Form::select("jam_selesai", $ref_jampelajaran, null, ["class" => "form-control select2"]) ],
			'id_mapel' => ['Mapel', Form::select("id_mapel", $ref_mapel, null, ["class" => "form-control select2"]) ],
			'id_ruang' => ['Ruang', Form::select("id_ruang", $ref_ruang, null, ["class" => "form-control select2"]) ],
			'id_semester' => ['', Form::hidden("id_semester", get_semester('active_semester_id')) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Jadwal::jadwal_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_guru' => 'required',
			'id_hari' => 'required',
			'id_kelas' => 'required',
			'jam_mulai' => 'required',
			'jam_selesai' => 'required',
			'id_mapel' => 'required',
			'id_ruang' => 'required',
			'id_semester' => 'required',
			
		]);

		$jadwal = new Jadwal();
		$jadwal->id_guru = $request->input("id_guru");
		$jadwal->id_hari = $request->input("id_hari");
		$jadwal->id_kelas = $request->input("id_kelas");
		$jadwal->jam_mulai = $request->input("jam_mulai");
		$jadwal->jam_selesai = $request->input("jam_selesai");
		$jadwal->id_mapel = $request->input("id_mapel");
		$jadwal->id_ruang = $request->input("id_ruang");
		$jadwal->id_semester = $request->input("id_semester");
		
		$jadwal->created_by = Auth::id();
		$jadwal->save();

		$text = 'membuat '.$this->title; //' baru '.$jadwal->what;
		$this->log($request, $text, ['jadwal.id' => $jadwal->id]);
		return redirect()->back()->with('message_success', 'Jadwal berhasil ditambahkan!');
	}

	public function show(Request $request, Jadwal $jadwal)
	{
		$data['jadwal'] = $jadwal;

		$text = 'melihat detail '.$this->title;//.' '.$jadwal->what;
		$this->log($request, $text, ['jadwal.id' => $jadwal->id]);
		return view('Jadwal::jadwal_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Jadwal $jadwal)
	{
		$data['jadwal'] = $jadwal;

		// dd($jadwal->id_guru);

		$ref_guru = Guru::all()->pluck('nama','id');
		$ref_hari = Hari::all()->pluck('hari','id');
		$ref_kelas = Kelas::all()->pluck('kelas','id');
		$ref_jampelajaran = Jampelajaran::all()->pluck('jam_pelajaran','id');
		$ref_jampelajaran = Jampelajaran::all()->pluck('jam_pelajaran','id');
		$ref_mapel = Mapel::all()->pluck('mapel','id');
		$ref_ruang = Ruang::all()->pluck('ruang','id');
		$ref_semester = Semester::all()->pluck('semester','id');
		
		$data['forms'] = array(
			'id_guru' => ['Guru', Form::select("id_guru", $ref_guru, $jadwal->id_guru, ["class" => "form-control select2"]) ],
			'id_hari' => ['Hari', Form::select("id_hari", $ref_hari, $jadwal->id_hari, ["class" => "form-control select2"]) ],
			'id_kelas' => ['Kelas', Form::select("id_kelas", $ref_kelas, $jadwal->id_kelas, ["class" => "form-control select2"]) ],
			'jam_mulai' => ['Jam Mulai', Form::select("jam_mulai", $ref_jampelajaran, $jadwal->jam_mulai, ["class" => "form-control select2"]) ],
			'jam_selesai' => ['Jam Selesai', Form::select("jam_selesai", $ref_jampelajaran, $jadwal->jam_selesai, ["class" => "form-control select2"]) ],
			'id_mapel' => ['Mapel', Form::select("id_mapel", $ref_mapel, $jadwal->id_mapel, ["class" => "form-control select2"]) ],
			'id_ruang' => ['Ruang', Form::select("id_ruang", $ref_ruang, $jadwal->id_ruang, ["class" => "form-control select2"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$jadwal->what;
		$this->log($request, $text, ['jadwal.id' => $jadwal->id]);
		return view('Jadwal::jadwal_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_guru' => 'required',
			'id_hari' => 'required',
			'id_kelas' => 'required',
			'jam_mulai' => 'required',
			'jam_selesai' => 'required',
			'id_mapel' => 'required',
			'id_ruang' => 'required',
			
		]);
		
		$jadwal = Jadwal::find($id);
		$jadwal->id_guru = $request->input("id_guru");
		$jadwal->id_hari = $request->input("id_hari");
		$jadwal->id_kelas = $request->input("id_kelas");
		$jadwal->jam_mulai = $request->input("jam_mulai");
		$jadwal->jam_selesai = $request->input("jam_selesai");
		$jadwal->id_mapel = $request->input("id_mapel");
		$jadwal->id_ruang = $request->input("id_ruang");
		// $jadwal->id_semester = $request->input("id_semester");
		
		$jadwal->updated_by = Auth::id();
		$jadwal->save();


		$text = 'mengedit '.$this->title;//.' '.$jadwal->what;
		$this->log($request, $text, ['jadwal.id' => $jadwal->id]);
		return redirect()->route('jadwal.index')->with('message_success', 'Jadwal berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$jadwal = Jadwal::find($id);
		$jadwal->deleted_by = Auth::id();
		$jadwal->save();
		$jadwal->delete();

		$text = 'menghapus '.$this->title;//.' '.$jadwal->what;
		$this->log($request, $text, ['jadwal.id' => $jadwal->id]);
		return back()->with('message_success', 'Jadwal berhasil dihapus!');
	}

}