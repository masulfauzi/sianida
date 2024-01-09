<?php
namespace App\Modules\Prestasi\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Prestasi\Models\Prestasi;
use App\Modules\Juara\Models\Juara;
use App\Modules\Siswa\Models\Siswa;

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
		$query = Prestasi::query()->whereIdSiswa(session()->get('id_siswa'));
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
		$ref_juara = Juara::all()->sortBy('poin')->pluck('juara','id');
		$ref_juara->prepend('-PILIH SALAH SATU-', '');
		// $ref_siswa = Siswa::all()->pluck('nama_siswa','id');
		$id_siswa = session()->get('id_siswa');
		
		$data['forms'] = array(
			'id_juara' => ['Juara', Form::select("id_juara", $ref_juara, null, ["class" => "form-control select2"]) ],
			'prestasi' => ['Prestasi', Form::text("prestasi", old("prestasi"), ["class" => "form-control","placeholder" => ""]) ],
			'tgl_perolehan' => ['Tgl Perolehan', Form::text("tgl_perolehan", old("tgl_perolehan"), ["class" => "form-control datepicker"]) ],
			'sertifikat' => ['Sertifikat', Form::file("sertifikat",  ["class" => "form-control","placeholder" => ""]) ],
			// 'is_pakai' => ['Is Pakai', Form::text("is_pakai", old("is_pakai"), ["class" => "form-control","placeholder" => ""]) ],
			'id_siswa' => ['', Form::hidden("id_siswa", $id_siswa) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Prestasi::prestasi_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_juara' => 'required',
			'id_siswa' => 'required',
			'prestasi' => 'required',
			'sertifikat' => 'required|mimes:pdf,jpg,jpeg,png|max:10240',
			'tgl_perolehan' => 'required|date',
			// 'is_pakai' => 'required',
			
		]);

		$fileName = time().'.'.$request->sertifikat->extension();  

        $request->sertifikat->move(public_path('uploads/sertifikat_prestasi/'), $fileName);

		$prestasi = new Prestasi();
		$prestasi->id_juara = $request->input("id_juara");
		$prestasi->id_siswa = $request->input("id_siswa");
		$prestasi->prestasi = $request->input("prestasi");
		$prestasi->sertifikat = $fileName;
		$prestasi->tgl_perolehan = $request->input("tgl_perolehan");
		// $prestasi->is_pakai = $request->input("is_pakai");
		
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

		$ref_juara = Juara::all()->pluck('id_tingkat_juara','id');
		$ref_siswa = Siswa::all()->pluck('nama_siswa','id');
		
		$data['forms'] = array(
			// 'id_juara' => ['Juara', Form::select("id_juara", $ref_juara, null, ["class" => "form-control select2"]) ],
			// 'id_siswa' => ['Siswa', Form::select("id_siswa", $ref_siswa, null, ["class" => "form-control select2"]) ],
			'prestasi' => ['Prestasi', Form::text("prestasi", $prestasi->prestasi, ["class" => "form-control","placeholder" => "", "id" => "prestasi"]) ],
			// 'sertifikat' => ['Sertifikat', Form::text("sertifikat", $prestasi->sertifikat, ["class" => "form-control","placeholder" => "", "id" => "sertifikat"]) ],
			'tgl_perolehan' => ['Tgl Perolehan', Form::text("tgl_perolehan", $prestasi->tgl_perolehan, ["class" => "form-control datepicker", "id" => "tgl_perolehan"]) ],
			// 'is_pakai' => ['Is Pakai', Form::text("is_pakai", $prestasi->is_pakai, ["class" => "form-control","placeholder" => "", "id" => "is_pakai"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$prestasi->what;
		$this->log($request, $text, ['prestasi.id' => $prestasi->id]);
		return view('Prestasi::prestasi_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			// 'id_juara' => 'required',
			// 'id_siswa' => 'required',
			'prestasi' => 'required',
			// 'sertifikat' => 'required',
			'tgl_perolehan' => 'required',
			// 'is_pakai' => 'required',
			
		]);
		
		$prestasi = Prestasi::find($id);
		$prestasi->id_juara = $request->input("id_juara");
		$prestasi->id_siswa = $request->input("id_siswa");
		$prestasi->prestasi = $request->input("prestasi");
		$prestasi->sertifikat = $request->input("sertifikat");
		$prestasi->tgl_perolehan = $request->input("tgl_perolehan");
		$prestasi->is_pakai = $request->input("is_pakai");
		
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
