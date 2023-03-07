<?php
namespace App\Modules\Prestasi\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Prestasi\Models\Prestasi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PrestasiController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Prestasi";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = Prestasi::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Prestasi::prestasi', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		
		$data['forms'] = array(
			'prestasi' => ['Prestasi', Form::text("prestasi", old("prestasi"), ["class" => "form-control","placeholder" => ""]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Prestasi::prestasi_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'prestasi' => 'required',
			
		]);

		$prestasi = new Prestasi();
		$prestasi->prestasi = $request->input("prestasi");
		
		$prestasi->created_by = Auth::id();
		$prestasi->save();

		$text = 'membuat '.$this->title; //' baru '.$prestasi->what;
		$this->log($request, $text, ['prestasi.id' => $prestasi->id]);
		return redirect()->route('prestasi.index')->with('message_success', 'Prestasi berhasil ditambahkan!');
	}

	public function show(Request $request, Prestasi $prestasi)
	{
		$data['prestasi'] = $prestasi;

		$text = 'melihat detail '.$this->title;//.' '.$prestasi->what;
		$this->log($request, $text, ['prestasi.id' => $prestasi->id]);
		return view('Prestasi::prestasi_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Prestasi $prestasi)
	{
		$data['prestasi'] = $prestasi;

		
		$data['forms'] = array(
			'prestasi' => ['Prestasi', Form::text("prestasi", $prestasi->prestasi, ["class" => "form-control","placeholder" => "", "id" => "prestasi"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$prestasi->what;
		$this->log($request, $text, ['prestasi.id' => $prestasi->id]);
		return view('Prestasi::prestasi_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'prestasi' => 'required',
			
		]);
		
		$prestasi = Prestasi::find($id);
		$prestasi->prestasi = $request->input("prestasi");
		
		$prestasi->updated_by = Auth::id();
		$prestasi->save();


		$text = 'mengedit '.$this->title;//.' '.$prestasi->what;
		$this->log($request, $text, ['prestasi.id' => $prestasi->id]);
		return redirect()->route('prestasi.index')->with('message_success', 'Prestasi berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$prestasi = Prestasi::find($id);
		$prestasi->deleted_by = Auth::id();
		$prestasi->save();
		$prestasi->delete();

		$text = 'menghapus '.$this->title;//.' '.$prestasi->what;
		$this->log($request, $text, ['prestasi.id' => $prestasi->id]);
		return back()->with('message_success', 'Prestasi berhasil dihapus!');
	}

}
