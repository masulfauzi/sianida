<?php
namespace App\Modules\Ekskul\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Ekskul\Models\Ekskul;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EkskulController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Ekskul";

	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = Ekskul::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Ekskul::ekskul', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{

		$data['forms'] = array(
			'nama' => ['Nama', Form::text("nama", old("nama"), ["class" => "form-control","placeholder" => ""]) ],
			'id_pembimbing' => ['Pembimbing', Form::text("id_pembimbing", old("id_pembimbing"), ["class" => "form-control","placeholder" => ""]) ],

		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Ekskul::ekskul_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'nama' => 'required',


		]);

		$ekskul = new Ekskul();
		$ekskul->nama = $request->input("nama");
		$ekskul->id_pembimbing = $request->input("id_pembimbing");

		$ekskul->created_by = Auth::id();
		$ekskul->save();

		$text = 'membuat '.$this->title; //' baru '.$ekskul->what;
		$this->log($request, $text, ['ekskul.id' => $ekskul->id]);
		return redirect()->route('ekskul.index')->with('message_success', 'Ekskul berhasil ditambahkan!');
	}

	public function show(Request $request, Ekskul $ekskul)
	{
		$data['ekskul'] = $ekskul;

		$text = 'melihat detail '.$this->title;//.' '.$ekskul->what;
		$this->log($request, $text, ['ekskul.id' => $ekskul->id]);
		return view('Ekskul::ekskul_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Ekskul $ekskul)
	{
		$data['ekskul'] = $ekskul;


		$data['forms'] = array(
			'nama' => ['Nama', Form::text("nama", $ekskul->nama, ["class" => "form-control","placeholder" => "", "id" => "nama"]) ],
			'id_pembimbing' => ['Pembimbing', Form::text("id_pembimbing", $ekskul->id_pembimbing, ["class" => "form-control","placeholder" => "", "id" => "id_pembimbing"]) ],

		);

		$text = 'membuka form edit '.$this->title;//.' '.$ekskul->what;
		$this->log($request, $text, ['ekskul.id' => $ekskul->id]);
		return view('Ekskul::ekskul_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'nama' => 'required',
			'id_pembimbing' => 'required',

		]);

		$ekskul = Ekskul::find($id);
		$ekskul->nama = $request->input("nama");
		$ekskul->id_pembimbing = $request->input("id_pembimbing");

		$ekskul->updated_by = Auth::id();
		$ekskul->save();


		$text = 'mengedit '.$this->title;//.' '.$ekskul->what;
		$this->log($request, $text, ['ekskul.id' => $ekskul->id]);
		return redirect()->route('ekskul.index')->with('message_success', 'Ekskul berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$ekskul = Ekskul::find($id);
		$ekskul->deleted_by = Auth::id();
		$ekskul->save();
		$ekskul->delete();

		$text = 'menghapus '.$this->title;//.' '.$ekskul->what;
		$this->log($request, $text, ['ekskul.id' => $ekskul->id]);
		return back()->with('message_success', 'Ekskul berhasil dihapus!');
	}

}
