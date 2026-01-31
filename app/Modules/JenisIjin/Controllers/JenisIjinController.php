<?php
namespace App\Modules\JenisIjin\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\JenisIjin\Models\JenisIjin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class JenisIjinController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Jenis Ijin";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = JenisIjin::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('JenisIjin::jenisijin', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		
		$data['forms'] = array(
			'jenis_ijin' => ['Jenis Ijin', Form::text("jenis_ijin", old("jenis_ijin"), ["class" => "form-control","placeholder" => ""]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('JenisIjin::jenisijin_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'jenis_ijin' => 'required',
			
		]);

		$jenisijin = new JenisIjin();
		$jenisijin->jenis_ijin = $request->input("jenis_ijin");
		
		$jenisijin->created_by = Auth::id();
		$jenisijin->save();

		$text = 'membuat '.$this->title; //' baru '.$jenisijin->what;
		$this->log($request, $text, ['jenisijin.id' => $jenisijin->id]);
		return redirect()->route('jenisijin.index')->with('message_success', 'Jenis Ijin berhasil ditambahkan!');
	}

	public function show(Request $request, JenisIjin $jenisijin)
	{
		$data['jenisijin'] = $jenisijin;

		$text = 'melihat detail '.$this->title;//.' '.$jenisijin->what;
		$this->log($request, $text, ['jenisijin.id' => $jenisijin->id]);
		return view('JenisIjin::jenisijin_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, JenisIjin $jenisijin)
	{
		$data['jenisijin'] = $jenisijin;

		
		$data['forms'] = array(
			'jenis_ijin' => ['Jenis Ijin', Form::text("jenis_ijin", $jenisijin->jenis_ijin, ["class" => "form-control","placeholder" => "", "id" => "jenis_ijin"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$jenisijin->what;
		$this->log($request, $text, ['jenisijin.id' => $jenisijin->id]);
		return view('JenisIjin::jenisijin_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'jenis_ijin' => 'required',
			
		]);
		
		$jenisijin = JenisIjin::find($id);
		$jenisijin->jenis_ijin = $request->input("jenis_ijin");
		
		$jenisijin->updated_by = Auth::id();
		$jenisijin->save();


		$text = 'mengedit '.$this->title;//.' '.$jenisijin->what;
		$this->log($request, $text, ['jenisijin.id' => $jenisijin->id]);
		return redirect()->route('jenisijin.index')->with('message_success', 'Jenis Ijin berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$jenisijin = JenisIjin::find($id);
		$jenisijin->deleted_by = Auth::id();
		$jenisijin->save();
		$jenisijin->delete();

		$text = 'menghapus '.$this->title;//.' '.$jenisijin->what;
		$this->log($request, $text, ['jenisijin.id' => $jenisijin->id]);
		return back()->with('message_success', 'Jenis Ijin berhasil dihapus!');
	}

}
