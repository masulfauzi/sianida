<?php
namespace App\Modules\Dudi\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Dudi\Models\Dudi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DudiController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Dudi";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = Dudi::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Dudi::dudi', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		
		$data['forms'] = array(
			'nama_dudi' => ['Nama Dudi', Form::text("nama_dudi", old("nama_dudi"), ["class" => "form-control","placeholder" => ""]) ],
			'alamat' => ['Alamat', Form::textarea("alamat", old("alamat"), ["class" => "form-control rich-editor"]) ],
			'pimpinan' => ['Pimpinan', Form::text("pimpinan", old("pimpinan"), ["class" => "form-control","placeholder" => ""]) ],
			'no_hp' => ['No Hp', Form::text("no_hp", old("no_hp"), ["class" => "form-control","placeholder" => ""]) ],
			'jarak' => ['Jarak', Form::number("jarak", old("jarak"), ["class" => "form-control","placeholder" => "", "step" => "any"]) ],
			'tarif' => ['Tarif', Form::text("tarif", old("tarif"), ["class" => "form-control","placeholder" => ""]) ],

		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Dudi::dudi_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'nama_dudi' => 'required',
			'alamat' => 'required',
			'pimpinan' => 'required',
			'no_hp' => 'required',
			'jarak' => 'required|numeric',
			'tarif' => 'required',

		]);

		$dudi = new Dudi();
		$dudi->nama_dudi = $request->input("nama_dudi");
		$dudi->alamat = $request->input("alamat");
		$dudi->pimpinan = $request->input("pimpinan");
		$dudi->no_hp = $request->input("no_hp");
		$dudi->jarak = $request->input("jarak");
		$dudi->tarif = $request->input("tarif");

		$dudi->created_by = Auth::id();
		$dudi->save();

		$text = 'membuat '.$this->title; //' baru '.$dudi->what;
		$this->log($request, $text, ['dudi.id' => $dudi->id]);
		return redirect()->route('dudi.index')->with('message_success', 'Dudi berhasil ditambahkan!');
	}

	public function show(Request $request, Dudi $dudi)
	{
		$data['dudi'] = $dudi;

		$text = 'melihat detail '.$this->title;//.' '.$dudi->what;
		$this->log($request, $text, ['dudi.id' => $dudi->id]);
		return view('Dudi::dudi_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Dudi $dudi)
	{
		$data['dudi'] = $dudi;

		
		$data['forms'] = array(
			'nama_dudi' => ['Nama Dudi', Form::text("nama_dudi", $dudi->nama_dudi, ["class" => "form-control","placeholder" => "", "id" => "nama_dudi"]) ],
			'alamat' => ['Alamat', Form::textarea("alamat", $dudi->alamat, ["class" => "form-control rich-editor"]) ],
			'pimpinan' => ['Pimpinan', Form::text("pimpinan", $dudi->pimpinan, ["class" => "form-control","placeholder" => "", "id" => "pimpinan"]) ],
			'no_hp' => ['No Hp', Form::text("no_hp", $dudi->no_hp, ["class" => "form-control","placeholder" => "", "id" => "no_hp"]) ],
			'jarak' => ['Jarak', Form::number("jarak", $dudi->jarak, ["class" => "form-control","placeholder" => "", "id" => "jarak", "step" => "any"]) ],
			'tarif' => ['Tarif', Form::text("tarif", $dudi->tarif, ["class" => "form-control","placeholder" => "", "id" => "tarif"]) ],

		);

		$text = 'membuka form edit '.$this->title;//.' '.$dudi->what;
		$this->log($request, $text, ['dudi.id' => $dudi->id]);
		return view('Dudi::dudi_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'nama_dudi' => 'required',
			'alamat' => 'required',
			'pimpinan' => 'required',
			'no_hp' => 'required',
			'jarak' => 'required|numeric',
			'tarif' => 'required',

		]);

		$dudi = Dudi::find($id);
		$dudi->nama_dudi = $request->input("nama_dudi");
		$dudi->alamat = $request->input("alamat");
		$dudi->pimpinan = $request->input("pimpinan");
		$dudi->no_hp = $request->input("no_hp");
		$dudi->jarak = $request->input("jarak");
		$dudi->tarif = $request->input("tarif");

		$dudi->updated_by = Auth::id();
		$dudi->save();


		$text = 'mengedit '.$this->title;//.' '.$dudi->what;
		$this->log($request, $text, ['dudi.id' => $dudi->id]);
		return redirect()->route('dudi.index')->with('message_success', 'Dudi berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$dudi = Dudi::find($id);
		$dudi->deleted_by = Auth::id();
		$dudi->save();
		$dudi->delete();

		$text = 'menghapus '.$this->title;//.' '.$dudi->what;
		$this->log($request, $text, ['dudi.id' => $dudi->id]);
		return back()->with('message_success', 'Dudi berhasil dihapus!');
	}

}
