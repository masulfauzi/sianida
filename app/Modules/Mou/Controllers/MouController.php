<?php
namespace App\Modules\Mou\Controllers;

use Form;
use Illuminate\Support\HtmlString;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Mou\Models\Mou;
use App\Modules\Dudi\Models\Dudi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MouController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Mou";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = Mou::query()->where('id_dudi', $request->input('id_dudi'));
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();
		$data['dudi'] = Dudi::find($request->input('id_dudi'));

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Mou::mou', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$ref_dudi = Dudi::all()->pluck('nama_dudi','id');
		$dudi = Dudi::find($request->input('id_dudi'));
		
		$data['forms'] = array(
			'id_dudi' => ['Dudi', new HtmlString(Form::text("nama_dudi_display", $dudi->nama_dudi, ["class" => "form-control", "readonly"]) . Form::hidden("id_dudi", $dudi->id)) ],
			'tgl_mulai' => ['Tgl Mulai', Form::text("tgl_mulai", old("tgl_mulai"), ["class" => "form-control datepicker"]) ],
			'tgl_selesai' => ['Tgl Selesai', Form::text("tgl_selesai", old("tgl_selesai"), ["class" => "form-control datepicker"]) ],
			'tgl_surat' => ['Tgl Surat', Form::text("tgl_surat", old("tgl_surat"), ["class" => "form-control datepicker"]) ],
			'file_mou' => ['File Mou', Form::file("file_mou", ["class" => "form-control","placeholder" => ""]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Mou::mou_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_dudi' => 'required',
			'tgl_mulai' => 'required',
			'tgl_selesai' => 'required',
			'tgl_surat' => 'required',
			'file_mou' => 'required|file|mimes:pdf|max:10240',

		]);

		$uploaded = $request->file('file_mou');
		$fileName = time().'_'.$uploaded->getClientOriginalName();
		$uploaded->move(public_path('uploads/mou'), $fileName);

		$mou = new Mou();
		$mou->id_dudi = $request->input("id_dudi");
		$mou->tgl_mulai = $request->input("tgl_mulai");
		$mou->tgl_selesai = $request->input("tgl_selesai");
		$mou->tgl_surat = $request->input("tgl_surat");
		$mou->file_mou = $fileName;

		$mou->created_by = Auth::id();
		$mou->save();

		$text = 'membuat '.$this->title; //' baru '.$mou->what;
		$this->log($request, $text, ['mou.id' => $mou->id]);
		return redirect()->route('mou.index', ['id_dudi' => $request->input("id_dudi")])->with('message_success', 'Mou berhasil ditambahkan!');
	}

	public function show(Request $request, Mou $mou)
	{
		$data['mou'] = $mou;

		$text = 'melihat detail '.$this->title;//.' '.$mou->what;
		$this->log($request, $text, ['mou.id' => $mou->id]);
		return view('Mou::mou_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Mou $mou)
	{
		$data['mou'] = $mou;

		$ref_dudi = Dudi::all()->pluck('nama_dudi','id');
		
		$data['forms'] = array(
			'id_dudi' => ['Dudi', Form::select("id_dudi", $ref_dudi, null, ["class" => "form-control select2"]) ],
			'tgl_mulai' => ['Tgl Mulai', Form::text("tgl_mulai", $mou->tgl_mulai, ["class" => "form-control datepicker", "id" => "tgl_mulai"]) ],
			'tgl_selesai' => ['Tgl Selesai', Form::text("tgl_selesai", $mou->tgl_selesai, ["class" => "form-control datepicker", "id" => "tgl_selesai"]) ],
			'tgl_surat' => ['Tgl Surat', Form::text("tgl_surat", $mou->tgl_surat, ["class" => "form-control datepicker", "id" => "tgl_surat"]) ],
			'file_mou' => ['File Mou', Form::text("file_mou", $mou->file_mou, ["class" => "form-control","placeholder" => "", "id" => "file_mou"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$mou->what;
		$this->log($request, $text, ['mou.id' => $mou->id]);
		return view('Mou::mou_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_dudi' => 'required',
			'tgl_mulai' => 'required',
			'tgl_selesai' => 'required',
			'tgl_surat' => 'required',
			'file_mou' => 'required',
			
		]);
		
		$mou = Mou::find($id);
		$mou->id_dudi = $request->input("id_dudi");
		$mou->tgl_mulai = $request->input("tgl_mulai");
		$mou->tgl_selesai = $request->input("tgl_selesai");
		$mou->tgl_surat = $request->input("tgl_surat");
		$mou->file_mou = $request->input("file_mou");
		
		$mou->updated_by = Auth::id();
		$mou->save();


		$text = 'mengedit '.$this->title;//.' '.$mou->what;
		$this->log($request, $text, ['mou.id' => $mou->id]);
		return redirect()->route('mou.index')->with('message_success', 'Mou berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$mou = Mou::find($id);
		$mou->deleted_by = Auth::id();
		$mou->save();
		$mou->delete();

		$text = 'menghapus '.$this->title;//.' '.$mou->what;
		$this->log($request, $text, ['mou.id' => $mou->id]);
		return back()->with('message_success', 'Mou berhasil dihapus!');
	}

}
