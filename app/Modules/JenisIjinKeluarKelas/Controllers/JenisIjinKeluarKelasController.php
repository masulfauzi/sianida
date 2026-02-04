<?php
namespace App\Modules\JenisIjinKeluarJenisIjinKeluarKelas\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\JenisIjinKeluarKelas\Models\JenisIjinKeluarKelas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class JenisIjinKeluarJenisIjinKeluarKelasController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Jenis Ijin Keluar Kelas";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = JenisIjinKeluarKelas::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('JenisIjinKeluarKelas::jenisijinkeluarkelas', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		
		$data['forms'] = array(
			'jenis_ijin_keluar_kelas' => ['Jenis Ijin Keluar Kelas', Form::text("jenis_ijin_keluar_kelas", old("jenis_ijin_keluar_kelas"), ["class" => "form-control","placeholder" => ""]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('JenisIjinKeluarKelas::jenisijinkeluarkelas_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'jenis_ijin_keluar_kelas' => 'required',
			
		]);

		$jenisijinkeluarkelas = new JenisIjinKeluarKelas();
		$jenisijinkeluarkelas->jenis_ijin_keluar_kelas = $request->input("jenis_ijin_keluar_kelas");
		
		$jenisijinkeluarkelas->created_by = Auth::id();
		$jenisijinkeluarkelas->save();

		$text = 'membuat '.$this->title; //' baru '.$jenisijinkeluarkelas->what;
		$this->log($request, $text, ['jenisijinkeluarkelas.id' => $jenisijinkeluarkelas->id]);
		return redirect()->route('jenisijinkeluarkelas.index')->with('message_success', 'Jenis Ijin Keluar Kelas berhasil ditambahkan!');
	}

	public function show(Request $request, JenisIjinKeluarKelas $jenisijinkeluarkelas)
	{
		$data['jenisijinkeluarkelas'] = $jenisijinkeluarkelas;

		$text = 'melihat detail '.$this->title;//.' '.$jenisijinkeluarkelas->what;
		$this->log($request, $text, ['jenisijinkeluarkelas.id' => $jenisijinkeluarkelas->id]);
		return view('JenisIjinKeluarKelas::jenisijinkeluarkelas_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, JenisIjinKeluarKelas $jenisijinkeluarkelas)
	{
		$data['jenisijinkeluarkelas'] = $jenisijinkeluarkelas;

		
		$data['forms'] = array(
			'jenis_ijin_keluar_kelas' => ['Jenis Ijin Keluar Kelas', Form::text("jenis_ijin_keluar_kelas", $jenisijinkeluarkelas->jenis_ijin_keluar_kelas, ["class" => "form-control","placeholder" => "", "id" => "jenis_ijin_keluar_kelas"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$jenisijinkeluarkelas->what;
		$this->log($request, $text, ['jenisijinkeluarkelas.id' => $jenisijinkeluarkelas->id]);
		return view('JenisIjinKeluarKelas::jenisijinkeluarkelas_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'jenis_ijin_keluar_kelas' => 'required',
			
		]);
		
		$jenisijinkeluarkelas = JenisIjinKeluarKelas::find($id);
		$jenisijinkeluarkelas->jenis_ijin_keluar_kelas = $request->input("jenis_ijin_keluar_kelas");
		
		$jenisijinkeluarkelas->updated_by = Auth::id();
		$jenisijinkeluarkelas->save();


		$text = 'mengedit '.$this->title;//.' '.$jenisijinkeluarkelas->what;
		$this->log($request, $text, ['jenisijinkeluarkelas.id' => $jenisijinkeluarkelas->id]);
		return redirect()->route('jenisijinkeluarkelas.index')->with('message_success', 'Jenis Ijin Keluar Kelas berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$jenisijinkeluarkelas = JenisIjinKeluarKelas::find($id);
		$jenisijinkeluarkelas->deleted_by = Auth::id();
		$jenisijinkeluarkelas->save();
		$jenisijinkeluarkelas->delete();

		$text = 'menghapus '.$this->title;//.' '.$jenisijinkeluarkelas->what;
		$this->log($request, $text, ['jenisijinkeluarkelas.id' => $jenisijinkeluarkelas->id]);
		return back()->with('message_success', 'Jenis Ijin Keluar Kelas berhasil dihapus!');
	}

}
