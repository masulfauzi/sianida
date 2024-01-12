<?php
namespace App\Modules\Siswa\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Siswa\Models\Siswa;
use App\Modules\Jeniskelamin\Models\Jeniskelamin;
use App\Modules\Agama\Models\Agama;
use App\Modules\Semester\Models\Semester;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use PDF;

class SiswaController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Siswa";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		dd();

		if(session('active_role')['id'] == 'ce70ee2f-b43b-432b-b71c-30d073a4ba23')
		{
			return redirect(route('siswa.biodata.index'));
		}

		$query = Siswa::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Siswa::siswa', array_merge($data, ['title' => $this->title]));
	}

	public function hasil_abm(Request $request)
	{
		$id_siswa = session()->get('id_siswa');

		$siswa = Siswa::find($id_siswa);

		$data['hasil'] = $siswa->nisn . ".pdf";

		$this->log($request, 'melihat halaman hasil ABM');
		return view('Siswa::siswa_abm', array_merge($data, ['title' => $this->title]));
	}

	public function kelulusan(Request $request)
	{
		$data['siswa'] = Siswa::detail_siswa(session('id_siswa'));
		$data['semester']	= Semester::find(get_semester('active_semester_id'));

		return view('Siswa::siswa_kelulusan', array_merge($data, ['title' => $this->title]));
	}

	public function upload_file(Request $request)
	{
		$data['data'] = Siswa::find(session('id_siswa'));

		return view('Siswa::upload_file', array_merge($data, ['title' => $this->title]));
	}

	public function aksi_upload(Request $request)
	{
		$request->validate([
            'file' => 'required|mimes:pdf,jpg,jpeg|max:10240'
        ]);

		$file = time().'.'.$request->file->extension();  

        $request->file->move(public_path('uploads/'.$request->get('jenis')), $file);

		$siswa = Siswa::find($request->get('id'));
		if($request->get('jenis') == 'ijazah')
		{
			$siswa->file_ijazah_smp = $file;
		}
		else if($request->get('jenis') == 'skhun')
		{
			$siswa->file_skhun = $file;
		}
		else if($request->get('jenis') == 'kk')
		{
			$siswa->file_kk = $file;
		}
		else if($request->get('jenis') == 'akta')
		{
			$siswa->file_akta_lahir = $file;
		}
		$siswa->save();
		

		$text = 'mengupload '.$request->get('jenis'); //' baru '.$gurumapel->what;
		$this->log($request, $text);
		return redirect()->back()->with('message_success', 'Berkas berhasil diupload!');
	}

	public function downloads(Request $request)
	{
		$data['data']	= '';
		return view('Siswa::download', array_merge($data, ['title' => $this->title]));
	}

	public function download_biodata(Request $request, $id_siswa = NULL)
	{
		if($id_siswa == NULL)
		{
			$id_siswa = session('id_siswa');
		}

		$data['data']	=	Siswa::find($id_siswa);

		if(!$data['data']->file_ijazah_smp)
		{
			// return redirect()->back()->with('message_error', 'File Ijazah SMP belum di upload');
			return "File Ijazah SMP belum di upload";
		}
		if(!$data['data']->file_kk)
		{
			// return redirect()->back()->with('message_error', 'File Kartu Keluarga belum di upload');
			return "File Kartu Keluarga belum di upload";
		}
		if(!$data['data']->file_akta_lahir)
		{
			// return redirect()->back()->with('message_error', 'File Akta Kelahiran belum di upload');
			return "File Akta Kelahiran belum di upload";
		}

		// return view("Siswa::download_biodata", array_merge($data, ['title' => $this->title]));
		$pdf = PDF::loadview('Siswa::download_biodata',$data);
    	return $pdf->download('BiodataPesertaUjian');
	}

	public function lihat_file(Request $request, $file, $jenis)
	{
		$data['file']  = $file;
		$data['jenis']  = $jenis;

		return view('Siswa::lihat_file', $data);
	}

	public function biodata(Request $request)
	{
		$data['data'] = Siswa::find(session('id_siswa'));

		$this->log($request, 'melihat halaman biodata');
		return view('Siswa::biodata', array_merge($data, ['title' => $this->title]));
	}

	public function store_biodata(Request $request)
	{
		$validate = $this->validate($request, [
			'id' 			=> 'required',
			'nama_siswa' 	=> 'required',
			'tempat_lahir'	=> 'required',
			'tgl_lahir'		=> 'required|date',
			'nama_ayah'		=> 'required',
			'nama_ibu'		=> 'required',
			'alamat'		=> 'required',
			'sekolah_asal'	=> 'required',
			'no_ijazah_smp'	=> 'required',
		]);

		$siswa = Siswa::find($validate['id']);
		$siswa->nama_siswa 		= $validate['nama_siswa'];
		$siswa->tempat_lahir 	= $validate['tempat_lahir'];
		$siswa->tgl_lahir		= $validate['tgl_lahir'];
		$siswa->nama_ayah		= $validate['nama_ayah'];
		$siswa->nama_ibu		= $validate['nama_ibu'];
		$siswa->alamat			= $validate['alamat'];
		$siswa->sekolah_asal	= $validate['sekolah_asal'];
		$siswa->no_ijazah_smp	= $validate['no_ijazah_smp'];
		$siswa->no_skhun		= $request->input('no_skhun');
		
		$siswa->updated_by 		= Auth::id();
		$siswa->save();
		

		$text = 'Mengedit biodata';
		$this->log($request, $text, ['siswa.id' => $validate['id']]);
		return redirect()->back()->with('message_success', 'Biodata berhasil disimpan!');
	}

	public function create(Request $request)
	{
		$ref_jeniskelamin = Jeniskelamin::all()->pluck('jeniskelamin','id');
		$ref_agama = Agama::all()->pluck('agama','id');
		
		$data['forms'] = array(
			'nama_siswa' => ['Nama Siswa', Form::text("nama_siswa", old("nama_siswa"), ["class" => "form-control","placeholder" => ""]) ],
			'nis' => ['Nis', Form::text("nis", old("nis"), ["class" => "form-control","placeholder" => ""]) ],
			'nisn' => ['Nisn', Form::text("nisn", old("nisn"), ["class" => "form-control","placeholder" => ""]) ],
			'nik' => ['Nik', Form::text("nik", old("nik"), ["class" => "form-control","placeholder" => ""]) ],
			'id_jeniskelamin' => ['Jeniskelamin', Form::select("id_jeniskelamin", $ref_jeniskelamin, null, ["class" => "form-control select2"]) ],
			'id_agama' => ['Agama', Form::select("id_agama", $ref_agama, null, ["class" => "form-control select2"]) ],
			'tahun_masuk' => ['Tahun Masuk', Form::text("tahun_masuk", old("tahun_masuk"), ["class" => "form-control","placeholder" => ""]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Siswa::siswa_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'nama_siswa' => 'required',
			'nis' => 'required',
			'nisn' => 'required',
			'nik' => 'required',
			'id_jeniskelamin' => 'required',
			'id_agama' => 'required',
			'tahun_masuk' => 'required',
			
		]);

		$siswa = new Siswa();
		$siswa->nama_siswa = $request->input("nama_siswa");
		$siswa->nis = $request->input("nis");
		$siswa->nisn = $request->input("nisn");
		$siswa->nik = $request->input("nik");
		$siswa->id_jeniskelamin = $request->input("id_jeniskelamin");
		$siswa->id_agama = $request->input("id_agama");
		$siswa->tahun_masuk = $request->input("tahun_masuk");
		
		$siswa->created_by = Auth::id();
		$siswa->save();

		$text = 'membuat '.$this->title; //' baru '.$siswa->what;
		$this->log($request, $text, ['siswa.id' => $siswa->id]);
		return redirect()->route('siswa.index')->with('message_success', 'Siswa berhasil ditambahkan!');
	}

	public function show(Request $request, Siswa $siswa)
	{
		$data['siswa'] = $siswa;

		$text = 'melihat detail '.$this->title;//.' '.$siswa->what;
		$this->log($request, $text, ['siswa.id' => $siswa->id]);
		return view('Siswa::siswa_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Siswa $siswa)
	{
		$data['siswa'] = $siswa;

		$ref_jeniskelamin = Jeniskelamin::all()->pluck('jeniskelamin','id');
		$ref_agama = Agama::all()->pluck('agama','id');
		
		$data['forms'] = array(
			'nama_siswa' => ['Nama Siswa', Form::text("nama_siswa", $siswa->nama_siswa, ["class" => "form-control","placeholder" => "", "id" => "nama_siswa"]) ],
			'nis' => ['Nis', Form::text("nis", $siswa->nis, ["class" => "form-control","placeholder" => "", "id" => "nis"]) ],
			'nisn' => ['Nisn', Form::text("nisn", $siswa->nisn, ["class" => "form-control","placeholder" => "", "id" => "nisn"]) ],
			'nik' => ['Nik', Form::text("nik", $siswa->nik, ["class" => "form-control","placeholder" => "", "id" => "nik"]) ],
			'id_jeniskelamin' => ['Jeniskelamin', Form::select("id_jeniskelamin", $ref_jeniskelamin, null, ["class" => "form-control select2"]) ],
			'id_agama' => ['Agama', Form::select("id_agama", $ref_agama, null, ["class" => "form-control select2"]) ],
			'tahun_masuk' => ['Tahun Masuk', Form::text("tahun_masuk", $siswa->tahun_masuk, ["class" => "form-control","placeholder" => "", "id" => "tahun_masuk"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$siswa->what;
		$this->log($request, $text, ['siswa.id' => $siswa->id]);
		return view('Siswa::siswa_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'nama_siswa' => 'required',
			'nis' => 'required',
			'nisn' => 'required',
			'nik' => 'required',
			'id_jeniskelamin' => 'required',
			'id_agama' => 'required',
			'tahun_masuk' => 'required',
			
		]);
		
		$siswa = Siswa::find($id);
		$siswa->nama_siswa = $request->input("nama_siswa");
		$siswa->nis = $request->input("nis");
		$siswa->nisn = $request->input("nisn");
		$siswa->nik = $request->input("nik");
		$siswa->id_jeniskelamin = $request->input("id_jeniskelamin");
		$siswa->id_agama = $request->input("id_agama");
		$siswa->tahun_masuk = $request->input("tahun_masuk");
		
		$siswa->updated_by = Auth::id();
		$siswa->save();


		$text = 'mengedit '.$this->title;//.' '.$siswa->what;
		$this->log($request, $text, ['siswa.id' => $siswa->id]);
		return redirect()->route('siswa.index')->with('message_success', 'Siswa berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$siswa = Siswa::find($id);
		$siswa->deleted_by = Auth::id();
		$siswa->save();
		$siswa->delete();

		$text = 'menghapus '.$this->title;//.' '.$siswa->what;
		$this->log($request, $text, ['siswa.id' => $siswa->id]);
		return back()->with('message_success', 'Siswa berhasil dihapus!');
	}

}
