<?php
namespace App\Modules\IjinKeluarIjinKeluarKelas\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\IjinKeluarKelas\Models\IjinKeluarKelas;
use App\Modules\Siswa\Models\Siswa;
use App\Modules\Guru\Models\Guru;
use App\Modules\JenisIjinKeluarKelas\Models\JenisIjinKeluarKelas;
use App\Modules\Jampelajaran\Models\Jampelajaran;
use App\Modules\Jampelajaran\Models\Jampelajaran;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class IjinKeluarIjinKeluarKelasController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Ijin Keluar Kelas";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = IjinKeluarKelas::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('IjinKeluarKelas::ijinkeluarkelas', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$ref_siswa = Siswa::all()->pluck('nama_siswa','id');
		$ref_guru = Guru::all()->pluck('nama','id');
		$ref_jenis_ijin_keluar_kelas = JenisIjinKeluarKelas::all()->pluck('jenis_ijin_keluar_kelas','id');
		$ref_jampelajaran = Jampelajaran::all()->pluck('jam_pelajaran','id');
		$ref_jampelajaran = Jampelajaran::all()->pluck('jam_pelajaran','id');
		
		$data['forms'] = array(
			'id_siswa' => ['Siswa', Form::select("id_siswa", $ref_siswa, null, ["class" => "form-control select2"]) ],
			'id_guru' => ['Guru', Form::select("id_guru", $ref_guru, null, ["class" => "form-control select2"]) ],
			'id_jenis_ijin_keluar' => ['Jenis Ijin Keluar', Form::select("id_jenis_ijin_keluar", $ref_jenis_ijin_keluar_kelas, null, ["class" => "form-control select2"]) ],
			'keperluan' => ['Keperluan', Form::textarea("keperluan", old("keperluan"), ["class" => "form-control rich-editor"]) ],
			'jam_keluar' => ['Jam Keluar', Form::select("jam_keluar", $ref_jampelajaran, null, ["class" => "form-control select2"]) ],
			'jam_kembali' => ['Jam Kembali', Form::select("jam_kembali", $ref_jampelajaran, null, ["class" => "form-control select2"]) ],
			'is_valid_guru' => ['Is Valguru', Form::text("is_valid_guru", old("is_valid_guru"), ["class" => "form-control","placeholder" => ""]) ],
			'is_valid_bk' => ['Is Valbk', Form::text("is_valid_bk", old("is_valid_bk"), ["class" => "form-control","placeholder" => ""]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('IjinKeluarKelas::ijinkeluarkelas_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_siswa' => 'required',
			'id_guru' => 'required',
			'id_jenis_ijin_keluar' => 'required',
			'keperluan' => 'required',
			'jam_keluar' => 'required',
			'jam_kembali' => 'required',
			'is_valid_guru' => 'required',
			'is_valid_bk' => 'required',
			
		]);

		$ijinkeluarkelas = new IjinKeluarKelas();
		$ijinkeluarkelas->id_siswa = $request->input("id_siswa");
		$ijinkeluarkelas->id_guru = $request->input("id_guru");
		$ijinkeluarkelas->id_jenis_ijin_keluar = $request->input("id_jenis_ijin_keluar");
		$ijinkeluarkelas->keperluan = $request->input("keperluan");
		$ijinkeluarkelas->jam_keluar = $request->input("jam_keluar");
		$ijinkeluarkelas->jam_kembali = $request->input("jam_kembali");
		$ijinkeluarkelas->is_valid_guru = $request->input("is_valid_guru");
		$ijinkeluarkelas->is_valid_bk = $request->input("is_valid_bk");
		
		$ijinkeluarkelas->created_by = Auth::id();
		$ijinkeluarkelas->save();

		$text = 'membuat '.$this->title; //' baru '.$ijinkeluarkelas->what;
		$this->log($request, $text, ['ijinkeluarkelas.id' => $ijinkeluarkelas->id]);
		return redirect()->route('ijinkeluarkelas.index')->with('message_success', 'Ijin Keluar Kelas berhasil ditambahkan!');
	}

	public function show(Request $request, IjinKeluarKelas $ijinkeluarkelas)
	{
		$data['ijinkeluarkelas'] = $ijinkeluarkelas;

		$text = 'melihat detail '.$this->title;//.' '.$ijinkeluarkelas->what;
		$this->log($request, $text, ['ijinkeluarkelas.id' => $ijinkeluarkelas->id]);
		return view('IjinKeluarKelas::ijinkeluarkelas_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, IjinKeluarKelas $ijinkeluarkelas)
	{
		$data['ijinkeluarkelas'] = $ijinkeluarkelas;

		$ref_siswa = Siswa::all()->pluck('nama_siswa','id');
		$ref_guru = Guru::all()->pluck('nama','id');
		$ref_jenis_ijin_keluar_kelas = JenisIjinKeluarKelas::all()->pluck('jenis_ijin_keluar_kelas','id');
		$ref_jampelajaran = Jampelajaran::all()->pluck('jam_pelajaran','id');
		$ref_jampelajaran = Jampelajaran::all()->pluck('jam_pelajaran','id');
		
		$data['forms'] = array(
			'id_siswa' => ['Siswa', Form::select("id_siswa", $ref_siswa, null, ["class" => "form-control select2"]) ],
			'id_guru' => ['Guru', Form::select("id_guru", $ref_guru, null, ["class" => "form-control select2"]) ],
			'id_jenis_ijin_keluar' => ['Jenis Ijin Keluar', Form::select("id_jenis_ijin_keluar", $ref_jenis_ijin_keluar_kelas, null, ["class" => "form-control select2"]) ],
			'keperluan' => ['Keperluan', Form::textarea("keperluan", $ijinkeluarkelas->keperluan, ["class" => "form-control rich-editor"]) ],
			'jam_keluar' => ['Jam Keluar', Form::select("jam_keluar", $ref_jampelajaran, null, ["class" => "form-control select2"]) ],
			'jam_kembali' => ['Jam Kembali', Form::select("jam_kembali", $ref_jampelajaran, null, ["class" => "form-control select2"]) ],
			'is_valid_guru' => ['Is Valguru', Form::text("is_valid_guru", $ijinkeluarkelas->is_valid_guru, ["class" => "form-control","placeholder" => "", "id" => "is_valid_guru"]) ],
			'is_valid_bk' => ['Is Valbk', Form::text("is_valid_bk", $ijinkeluarkelas->is_valid_bk, ["class" => "form-control","placeholder" => "", "id" => "is_valid_bk"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$ijinkeluarkelas->what;
		$this->log($request, $text, ['ijinkeluarkelas.id' => $ijinkeluarkelas->id]);
		return view('IjinKeluarKelas::ijinkeluarkelas_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_siswa' => 'required',
			'id_guru' => 'required',
			'id_jenis_ijin_keluar' => 'required',
			'keperluan' => 'required',
			'jam_keluar' => 'required',
			'jam_kembali' => 'required',
			'is_valid_guru' => 'required',
			'is_valid_bk' => 'required',
			
		]);
		
		$ijinkeluarkelas = IjinKeluarKelas::find($id);
		$ijinkeluarkelas->id_siswa = $request->input("id_siswa");
		$ijinkeluarkelas->id_guru = $request->input("id_guru");
		$ijinkeluarkelas->id_jenis_ijin_keluar = $request->input("id_jenis_ijin_keluar");
		$ijinkeluarkelas->keperluan = $request->input("keperluan");
		$ijinkeluarkelas->jam_keluar = $request->input("jam_keluar");
		$ijinkeluarkelas->jam_kembali = $request->input("jam_kembali");
		$ijinkeluarkelas->is_valid_guru = $request->input("is_valid_guru");
		$ijinkeluarkelas->is_valid_bk = $request->input("is_valid_bk");
		
		$ijinkeluarkelas->updated_by = Auth::id();
		$ijinkeluarkelas->save();


		$text = 'mengedit '.$this->title;//.' '.$ijinkeluarkelas->what;
		$this->log($request, $text, ['ijinkeluarkelas.id' => $ijinkeluarkelas->id]);
		return redirect()->route('ijinkeluarkelas.index')->with('message_success', 'Ijin Keluar Kelas berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$ijinkeluarkelas = IjinKeluarKelas::find($id);
		$ijinkeluarkelas->deleted_by = Auth::id();
		$ijinkeluarkelas->save();
		$ijinkeluarkelas->delete();

		$text = 'menghapus '.$this->title;//.' '.$ijinkeluarkelas->what;
		$this->log($request, $text, ['ijinkeluarkelas.id' => $ijinkeluarkelas->id]);
		return back()->with('message_success', 'Ijin Keluar Kelas berhasil dihapus!');
	}

}
