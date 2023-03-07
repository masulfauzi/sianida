<?php
namespace App\Modules\Kelas\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Kelas\Models\Kelas;
use App\Modules\Tingkat\Models\Tingkat;
use App\Modules\Jurusan\Models\Jurusan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class KelasController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Kelas";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = Kelas::query()->orderBy('kelas');
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Kelas::kelas', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$ref_tingkat = Tingkat::all()->sortBy('tingkat')->pluck('tingkat','id');
		$ref_jurusan = Jurusan::all()->sortBy('jurusan')->pluck('jurusan','id');
		
		$data['forms'] = array(
			'kelas' => ['Kelas', Form::text("kelas", old("kelas"), ["class" => "form-control","placeholder" => ""]) ],
			'id_tingkat' => ['Tingkat', Form::select("id_tingkat", $ref_tingkat, null, ["class" => "form-control select2"]) ],
			'id_jurusan' => ['Jurusan', Form::select("id_jurusan", $ref_jurusan, null, ["class" => "form-control select2"]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Kelas::kelas_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'kelas' => 'required',
			'id_tingkat' => 'required',
			'id_jurusan' => 'required',
			
		]);

		$kelas = new Kelas();
		$kelas->kelas = $request->input("kelas");
		$kelas->id_tingkat = $request->input("id_tingkat");
		$kelas->id_jurusan = $request->input("id_jurusan");
		
		$kelas->created_by = Auth::id();
		$kelas->save();

		$text = 'membuat '.$this->title; //' baru '.$kelas->what;
		$this->log($request, $text, ['kelas.id' => $kelas->id]);
		return redirect()->route('kelas.index')->with('message_success', 'Kelas berhasil ditambahkan!');
	}

	public function show(Request $request, Kelas $kelas)
	{
		$data['kelas'] = $kelas;

		$text = 'melihat detail '.$this->title;//.' '.$kelas->what;
		$this->log($request, $text, ['kelas.id' => $kelas->id]);
		return view('Kelas::kelas_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Kelas $kelas)
	{
		$data['kelas'] = $kelas;

		$ref_tingkat = Tingkat::all()->pluck('tingkat','id');
		$ref_jurusan = Jurusan::all()->pluck('jurusan','id');
		
		$data['forms'] = array(
			'kelas' => ['Kelas', Form::text("kelas", $kelas->kelas, ["class" => "form-control","placeholder" => "", "id" => "kelas"]) ],
			'id_tingkat' => ['Tingkat', Form::select("id_tingkat", $ref_tingkat, null, ["class" => "form-control select2"]) ],
			'id_jurusan' => ['Jurusan', Form::select("id_jurusan", $ref_jurusan, null, ["class" => "form-control select2"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$kelas->what;
		$this->log($request, $text, ['kelas.id' => $kelas->id]);
		return view('Kelas::kelas_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'kelas' => 'required',
			'id_tingkat' => 'required',
			'id_jurusan' => 'required',
			
		]);
		
		$kelas = Kelas::find($id);
		$kelas->kelas = $request->input("kelas");
		$kelas->id_tingkat = $request->input("id_tingkat");
		$kelas->id_jurusan = $request->input("id_jurusan");
		
		$kelas->updated_by = Auth::id();
		$kelas->save();


		$text = 'mengedit '.$this->title;//.' '.$kelas->what;
		$this->log($request, $text, ['kelas.id' => $kelas->id]);
		return redirect()->route('kelas.index')->with('message_success', 'Kelas berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$kelas = Kelas::find($id);
		$kelas->deleted_by = Auth::id();
		$kelas->save();
		$kelas->delete();

		$text = 'menghapus '.$this->title;//.' '.$kelas->what;
		$this->log($request, $text, ['kelas.id' => $kelas->id]);
		return back()->with('message_success', 'Kelas berhasil dihapus!');
	}

}
