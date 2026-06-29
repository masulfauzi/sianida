<?php
namespace App\Modules\FileKsp\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\FileKsp\Models\FileKsp;
use App\Modules\Ksp\Models\Ksp;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FileKspController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "File Ksp";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = FileKsp::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('FileKsp::fileksp', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$ref_ksp = Ksp::all()->pluck('id_semester','id');
		
		$data['forms'] = array(
			'id_ksp' => ['Ksp', Form::select("id_ksp", $ref_ksp, null, ["class" => "form-control select2"]) ],
			'nama_file' => ['Nama File', Form::text("nama_file", old("nama_file"), ["class" => "form-control","placeholder" => ""]) ],
			'file' => ['File', Form::text("file", old("file"), ["class" => "form-control","placeholder" => ""]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('FileKsp::fileksp_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_ksp' => 'required',
			'nama_file' => 'required',
			'file' => 'required',
			
		]);

		$fileksp = new FileKsp();
		$fileksp->id_ksp = $request->input("id_ksp");
		$fileksp->nama_file = $request->input("nama_file");
		$fileksp->file = $request->input("file");
		
		$fileksp->created_by = Auth::id();
		$fileksp->save();

		$text = 'membuat '.$this->title; //' baru '.$fileksp->what;
		$this->log($request, $text, ['fileksp.id' => $fileksp->id]);
		return redirect()->route('fileksp.index')->with('message_success', 'File Ksp berhasil ditambahkan!');
	}

	public function show(Request $request, FileKsp $fileksp)
	{
		$data['fileksp'] = $fileksp;

		$text = 'melihat detail '.$this->title;//.' '.$fileksp->what;
		$this->log($request, $text, ['fileksp.id' => $fileksp->id]);
		return view('FileKsp::fileksp_detail', array_merge($data, ['title' => $this->title]));
	}

	public function upload(Request $request, Ksp $ksp)
	{
		$this->validate($request, [
			'nama_file' => 'required',
			'file' => 'required|mimes:pdf,doc,docx,xls,xlsx|max:10240',
		]);

		$uploaded = $request->file('file');
		$namaFile = $request->input('nama_file');
		$fileName = Str::slug($namaFile).'-'.time().'.'.$uploaded->getClientOriginalExtension();
		$uploaded->move(public_path('download/ksp/file_ksp'), $fileName);

		$fileksp = new FileKsp();
		$fileksp->id_ksp = $ksp->id;
		$fileksp->nama_file = $namaFile;
		$fileksp->file = $fileName;
		$fileksp->created_by = Auth::id();
		$fileksp->save();

		$text = 'mengunggah '.$this->title;
		$this->log($request, $text, ['fileksp.id' => $fileksp->id]);
		return redirect()->route('ksp.show', $ksp->id)->with('message_success', 'File Ksp berhasil diupload!');
	}

	public function edit(Request $request, FileKsp $fileksp)
	{
		$data['fileksp'] = $fileksp;

		$ref_ksp = Ksp::all()->pluck('id_semester','id');
		
		$data['forms'] = array(
			'id_ksp' => ['Ksp', Form::select("id_ksp", $ref_ksp, null, ["class" => "form-control select2"]) ],
			'nama_file' => ['Nama File', Form::text("nama_file", $fileksp->nama_file, ["class" => "form-control","placeholder" => "", "id" => "nama_file"]) ],
			'file' => ['File', Form::text("file", $fileksp->file, ["class" => "form-control","placeholder" => "", "id" => "file"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$fileksp->what;
		$this->log($request, $text, ['fileksp.id' => $fileksp->id]);
		return view('FileKsp::fileksp_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_ksp' => 'required',
			'nama_file' => 'required',
			'file' => 'required',
			
		]);
		
		$fileksp = FileKsp::find($id);
		$fileksp->id_ksp = $request->input("id_ksp");
		$fileksp->nama_file = $request->input("nama_file");
		$fileksp->file = $request->input("file");
		
		$fileksp->updated_by = Auth::id();
		$fileksp->save();


		$text = 'mengedit '.$this->title;//.' '.$fileksp->what;
		$this->log($request, $text, ['fileksp.id' => $fileksp->id]);
		return redirect()->route('fileksp.index')->with('message_success', 'File Ksp berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$fileksp = FileKsp::find($id);
		$fileksp->deleted_by = Auth::id();
		$fileksp->save();
		$fileksp->delete();

		$text = 'menghapus '.$this->title;//.' '.$fileksp->what;
		$this->log($request, $text, ['fileksp.id' => $fileksp->id]);
		return back()->with('message_success', 'File Ksp berhasil dihapus!');
	}

}
