<?php
namespace App\Modules\Ksp\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Ksp\Models\Ksp;
use App\Modules\Semester\Models\Semester;
use App\Modules\FileKsp\Models\FileKsp;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class KspController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Ksp";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = Ksp::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Ksp::ksp', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$ref_semester = Semester::all()->pluck('semester','id');
		
		$data['forms'] = array(
			'id_semester' => ['Semester', Form::select("id_semester", $ref_semester, null, ["class" => "form-control select2"]) ],
			'nama_ksp' => ['Nama Ksp', Form::text("nama_ksp", old("nama_ksp"), ["class" => "form-control","placeholder" => ""]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Ksp::ksp_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_semester' => 'required',
			'nama_ksp' => 'required',
			
		]);

		$ksp = new Ksp();
		$ksp->id_semester = $request->input("id_semester");
		$ksp->nama_ksp = $request->input("nama_ksp");
		
		$ksp->created_by = Auth::id();
		$ksp->save();

		$text = 'membuat '.$this->title; //' baru '.$ksp->what;
		$this->log($request, $text, ['ksp.id' => $ksp->id]);
		return redirect()->route('ksp.index')->with('message_success', 'Ksp berhasil ditambahkan!');
	}

	public function show(Request $request, Ksp $ksp)
	{
		$data['ksp'] = $ksp;
		$data['files'] = FileKsp::where('id_ksp', $ksp->id)->orderByDesc('created_at')->get();

		$text = 'melihat detail '.$this->title;//.' '.$ksp->what;
		$this->log($request, $text, ['ksp.id' => $ksp->id]);
		return view('Ksp::ksp_detail', array_merge($data, ['title' => $this->title]));
	}

	public function publicShow(Request $request, Ksp $ksp)
	{
		$data['ksp'] = $ksp;
		$data['files'] = FileKsp::where('id_ksp', $ksp->id)->orderByDesc('created_at')->get();

		$text = 'melihat detail publik '.$this->title;
		$this->log($request, $text, ['ksp.id' => $ksp->id]);
		return view('Ksp::ksp_public', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Ksp $ksp)
	{
		$data['ksp'] = $ksp;

		$ref_semester = Semester::all()->pluck('semester','id');
		
		$data['forms'] = array(
			'id_semester' => ['Semester', Form::select("id_semester", $ref_semester, null, ["class" => "form-control select2"]) ],
			'nama_ksp' => ['Nama Ksp', Form::text("nama_ksp", $ksp->nama_ksp, ["class" => "form-control","placeholder" => "", "id" => "nama_ksp"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$ksp->what;
		$this->log($request, $text, ['ksp.id' => $ksp->id]);
		return view('Ksp::ksp_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_semester' => 'required',
			'nama_ksp' => 'required',
			
		]);
		
		$ksp = Ksp::find($id);
		$ksp->id_semester = $request->input("id_semester");
		$ksp->nama_ksp = $request->input("nama_ksp");
		
		$ksp->updated_by = Auth::id();
		$ksp->save();


		$text = 'mengedit '.$this->title;//.' '.$ksp->what;
		$this->log($request, $text, ['ksp.id' => $ksp->id]);
		return redirect()->route('ksp.index')->with('message_success', 'Ksp berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$ksp = Ksp::find($id);
		$ksp->deleted_by = Auth::id();
		$ksp->save();
		$ksp->delete();

		$text = 'menghapus '.$this->title;//.' '.$ksp->what;
		$this->log($request, $text, ['ksp.id' => $ksp->id]);
		return back()->with('message_success', 'Ksp berhasil dihapus!');
	}

}
