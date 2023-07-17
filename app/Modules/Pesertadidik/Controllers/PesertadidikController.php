<?php
namespace App\Modules\Pesertadidik\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Pesertadidik\Models\Pesertadidik;
use App\Modules\Semester\Models\Semester;
use App\Modules\Siswa\Models\Siswa;
use App\Modules\Kelas\Models\Kelas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PesertadidikController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Pesertadidik";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = Pesertadidik::query()->whereIdSemester(get_semester('active_semester_id'));
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Pesertadidik::pesertadidik', array_merge($data, ['title' => $this->title]));
	}

	public function mutasi(Request $request)
	{
		return view('Pesertadidik::mutasi');
	}

	public function cetak_mutasi(Request $request)
	{
		$data['data'] = Pesertadidik::get_all(get_semester('active_semester_id'));

		return view('Pesertadidik::cetak_mutasi', $data);
	}

	public function create(Request $request)
	{
		$ref_siswa = Siswa::all()->pluck('nama_siswa','id');
		$ref_kelas = Kelas::all()->pluck('kelas','id');
		
		$data['forms'] = array(
			'id_semester' => ['', Form::hidden("id_semester", get_semester('active_semester_id'), ["class" => "form-control"]) ],
			'id_siswa' => ['Siswa', Form::select("id_siswa", $ref_siswa, null, ["class" => "form-control select2"]) ],
			'id_kelas' => ['Kelas', Form::select("id_kelas", $ref_kelas, null, ["class" => "form-control select2"]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Pesertadidik::pesertadidik_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_semester' => 'required',
			'id_siswa' => 'required',
			'id_kelas' => 'required',
			
		]);

		$pesertadidik = new Pesertadidik();
		$pesertadidik->id_semester = $request->input("id_semester");
		$pesertadidik->id_siswa = $request->input("id_siswa");
		$pesertadidik->id_kelas = $request->input("id_kelas");
		
		$pesertadidik->created_by = Auth::id();
		$pesertadidik->save();

		$text = 'membuat '.$this->title; //' baru '.$pesertadidik->what;
		$this->log($request, $text, ['pesertadidik.id' => $pesertadidik->id]);
		return redirect()->route('pesertadidik.index')->with('message_success', 'Pesertadidik berhasil ditambahkan!');
	}

	public function show(Request $request, Pesertadidik $pesertadidik)
	{
		$data['pesertadidik'] = $pesertadidik;

		$text = 'melihat detail '.$this->title;//.' '.$pesertadidik->what;
		$this->log($request, $text, ['pesertadidik.id' => $pesertadidik->id]);
		return view('Pesertadidik::pesertadidik_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Pesertadidik $pesertadidik)
	{
		$data['pesertadidik'] = $pesertadidik;

		$ref_semester = Semester::all()->pluck('semester','id');
		$ref_siswa = Siswa::all()->pluck('nama_siswa','id');
		$ref_kelas = Kelas::all()->pluck('kelas','id');
		
		$data['forms'] = array(
			'id_semester' => ['Semester', Form::select("id_semester", $ref_semester, null, ["class" => "form-control select2"]) ],
			'id_siswa' => ['Siswa', Form::select("id_siswa", $ref_siswa, null, ["class" => "form-control select2"]) ],
			'id_kelas' => ['Kelas', Form::select("id_kelas", $ref_kelas, null, ["class" => "form-control select2"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$pesertadidik->what;
		$this->log($request, $text, ['pesertadidik.id' => $pesertadidik->id]);
		return view('Pesertadidik::pesertadidik_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_semester' => 'required',
			'id_siswa' => 'required',
			'id_kelas' => 'required',
			
		]);
		
		$pesertadidik = Pesertadidik::find($id);
		$pesertadidik->id_semester = $request->input("id_semester");
		$pesertadidik->id_siswa = $request->input("id_siswa");
		$pesertadidik->id_kelas = $request->input("id_kelas");
		
		$pesertadidik->updated_by = Auth::id();
		$pesertadidik->save();


		$text = 'mengedit '.$this->title;//.' '.$pesertadidik->what;
		$this->log($request, $text, ['pesertadidik.id' => $pesertadidik->id]);
		return redirect()->route('pesertadidik.index')->with('message_success', 'Pesertadidik berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$pesertadidik = Pesertadidik::find($id);
		$pesertadidik->deleted_by = Auth::id();
		$pesertadidik->save();
		$pesertadidik->delete();

		$text = 'menghapus '.$this->title;//.' '.$pesertadidik->what;
		$this->log($request, $text, ['pesertadidik.id' => $pesertadidik->id]);
		return back()->with('message_success', 'Pesertadidik berhasil dihapus!');
	}

}
