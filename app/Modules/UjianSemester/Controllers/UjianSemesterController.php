<?php
namespace App\Modules\UjianSemester\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\UjianSemester\Models\UjianSemester;
use App\Modules\Semester\Models\Semester;
use App\Modules\Mapel\Models\Mapel;
use App\Modules\Guru\Models\Guru;
use App\Modules\Jurusan\Models\Jurusan;
use App\Modules\Tingkat\Models\Tingkat;

use App\Http\Controllers\Controller;
use App\Modules\SoalSemester\Models\SoalSemester;
use Illuminate\Support\Facades\Auth;

class UjianSemesterController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Ujian Semester";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$admin = [
			'bf1548f3-295c-4d73-809d-66ab7c240091',
			'1fe8326c-22c4-4732-9c12-f7b83a16b842'
		];

		// dd(session('active_role')['id']);

		if(in_array(session('active_role')['id'], $admin))
		{
			return redirect()->route('ujiansemester.admin.index');
		}

		$query = UjianSemester::query()->whereIdGuru(session('id_guru'));
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('UjianSemester::ujiansemester', array_merge($data, ['title' => $this->title]));
	}

	public function index_admin(Request $request)
	{
		$query = UjianSemester::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('UjianSemester::ujiansemester', array_merge($data, ['title' => $this->title]));
	}

	public function upload(Request $request, String $id_ujian)
	{
		$data['data'] = UjianSemester::find($id_ujian);
		$data['soal'] = SoalSemester::where('id_ujiansemester', $id_ujian)->get();

		$this->log($request, 'melihat halaman upload ujian semester '.$this->title);
		return view('UjianSemester::ujiansemester_upload', array_merge($data, ['title' => $this->title]));
	}

	public function aksi_upload(Request $request)
	{
		$request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:10240'
        ]);

		$fileName = time().'.'.$request->file->extension();  

        $request->file->move(public_path('uploads/'.$request->get('jenis')), $fileName);

		$ujian = UjianSemester::find($request->get('id'));
		if($request->get('jenis') == 'kisikisi')
		{
			$ujian->kisi_kisi = $fileName;
		}
		else if($request->get('jenis') == 'norma')
		{
			$ujian->norma_penilaian = $fileName;
		}
		else if($request->get('jenis') == 'soal')
		{
			$ujian->soal = $fileName;
		}
		$ujian->save();
		

		$text = 'mengupload '.$this->title; //' baru '.$gurumapel->what;
		$this->log($request, $text);
		return redirect()->route('ujiansemester.upload.index', $request->get('id'))->with('message_success', 'Berkas berhasil diupload!');
	}

	public function create(Request $request)
	{
		$ref_mapel = Mapel::all()->sortBy('mapel')->pluck('mapel','id');
		$ref_mapel->prepend('-PILIH SALAH SATU-', '');
		$ref_jurusan = Jurusan::all()->sortBy('jurusan')->pluck('jurusan','id');
		$ref_jurusan->prepend('-PILIH SALAH SATU-', '');
		$ref_tingkat = Tingkat::all()->sortBy('tingkat')->pluck('tingkat','id');
		$ref_tingkat->prepend('PILIH SALAH SATU-', '');
		
		$data['forms'] = array(
			'id_mapel' => ['Mapel', Form::select("id_mapel", $ref_mapel, null, ["class" => "form-control select2"]) ],
			'id_jurusan' => ['Jurusan', Form::select("id_jurusan", $ref_jurusan, null, ["class" => "form-control select2"]) ],
			'id_tingkat' => ['Tingkat', Form::select("id_tingkat", $ref_tingkat, null, ["class" => "form-control select2"]) ],
			// 'kisi_kisi' => ['Kisi Kisi', Form::text("kisi_kisi", old("kisi_kisi"), ["class" => "form-control","placeholder" => ""]) ],
			// 'norma_penilaian' => ['Norma Penilaian', Form::text("norma_penilaian", old("norma_penilaian"), ["class" => "form-control","placeholder" => ""]) ],
			'jml_soal' => ['Jml Soal', Form::number("jml_soal", old("jml_soal"), ["class" => "form-control","placeholder" => ""]) ],
			'id_semester' => ['', Form::hidden("id_semester", get_semester('active_semester_id')) ],
			'id_guru' => ['', Form::hidden("id_guru", session('id_guru')) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('UjianSemester::ujiansemester_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_semester' => 'required',
			'id_mapel' => 'required',
			'id_guru' => 'required',
			'id_jurusan' => 'required',
			'id_tingkat' => 'required',
			// 'kisi_kisi' => 'required',
			// 'norma_penilaian' => 'required',
			'jml_soal' => 'required',
			
		]);

		$ujiansemester = new UjianSemester();
		$ujiansemester->id_semester = $request->input("id_semester");
		$ujiansemester->id_mapel = $request->input("id_mapel");
		$ujiansemester->id_guru = $request->input("id_guru");
		$ujiansemester->id_jurusan = $request->input("id_jurusan");
		$ujiansemester->id_tingkat = $request->input("id_tingkat");
		$ujiansemester->kisi_kisi = $request->input("kisi_kisi");
		$ujiansemester->norma_penilaian = $request->input("norma_penilaian");
		$ujiansemester->jml_soal = $request->input("jml_soal");
		
		$ujiansemester->created_by = Auth::id();
		$ujiansemester->save();

		$text = 'membuat '.$this->title; //' baru '.$ujiansemester->what;
		$this->log($request, $text, ['ujiansemester.id' => $ujiansemester->id]);
		return redirect()->route('ujiansemester.index')->with('message_success', 'Ujian Semester berhasil ditambahkan!');
	}

	public function show(Request $request, UjianSemester $ujiansemester)
	{
		$data['ujiansemester'] = $ujiansemester;

		$text = 'melihat detail '.$this->title;//.' '.$ujiansemester->what;
		$this->log($request, $text, ['ujiansemester.id' => $ujiansemester->id]);
		return view('UjianSemester::ujiansemester_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, UjianSemester $ujiansemester)
	{
		$data['ujiansemester'] = $ujiansemester;

		$ref_semester = Semester::all()->pluck('semester','id');
		$ref_mapel = Mapel::all()->pluck('mapel','id');
		$ref_guru = Guru::all()->pluck('nama','id');
		$ref_jurusan = Jurusan::all()->pluck('jurusan','id');
		$ref_tingkat = Tingkat::all()->pluck('tingkat','id');
		
		$data['forms'] = array(
			'id_semester' => ['Semester', Form::select("id_semester", $ref_semester, null, ["class" => "form-control select2"]) ],
			'id_mapel' => ['Mapel', Form::select("id_mapel", $ref_mapel, null, ["class" => "form-control select2"]) ],
			'id_guru' => ['Guru', Form::select("id_guru", $ref_guru, null, ["class" => "form-control select2"]) ],
			'id_jurusan' => ['Jurusan', Form::select("id_jurusan", $ref_jurusan, null, ["class" => "form-control select2"]) ],
			'id_tingkat' => ['Tingkat', Form::select("id_tingkat", $ref_tingkat, null, ["class" => "form-control select2"]) ],
			'kisi_kisi' => ['Kisi Kisi', Form::text("kisi_kisi", $ujiansemester->kisi_kisi, ["class" => "form-control","placeholder" => "", "id" => "kisi_kisi"]) ],
			'norma_penilaian' => ['Norma Penilaian', Form::text("norma_penilaian", $ujiansemester->norma_penilaian, ["class" => "form-control","placeholder" => "", "id" => "norma_penilaian"]) ],
			'jml_soal' => ['Jml Soal', Form::text("jml_soal", $ujiansemester->jml_soal, ["class" => "form-control","placeholder" => "", "id" => "jml_soal"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$ujiansemester->what;
		$this->log($request, $text, ['ujiansemester.id' => $ujiansemester->id]);
		return view('UjianSemester::ujiansemester_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_semester' => 'required',
			'id_mapel' => 'required',
			'id_guru' => 'required',
			'id_jurusan' => 'required',
			'id_tingkat' => 'required',
			'kisi_kisi' => 'required',
			'norma_penilaian' => 'required',
			'jml_soal' => 'required',
			
		]);
		
		$ujiansemester = UjianSemester::find($id);
		$ujiansemester->id_semester = $request->input("id_semester");
		$ujiansemester->id_mapel = $request->input("id_mapel");
		$ujiansemester->id_guru = $request->input("id_guru");
		$ujiansemester->id_jurusan = $request->input("id_jurusan");
		$ujiansemester->id_tingkat = $request->input("id_tingkat");
		$ujiansemester->kisi_kisi = $request->input("kisi_kisi");
		$ujiansemester->norma_penilaian = $request->input("norma_penilaian");
		$ujiansemester->jml_soal = $request->input("jml_soal");
		
		$ujiansemester->updated_by = Auth::id();
		$ujiansemester->save();


		$text = 'mengedit '.$this->title;//.' '.$ujiansemester->what;
		$this->log($request, $text, ['ujiansemester.id' => $ujiansemester->id]);
		return redirect()->route('ujiansemester.index')->with('message_success', 'Ujian Semester berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$ujiansemester = UjianSemester::find($id);
		$ujiansemester->deleted_by = Auth::id();
		$ujiansemester->save();
		$ujiansemester->delete();

		$text = 'menghapus '.$this->title;//.' '.$ujiansemester->what;
		$this->log($request, $text, ['ujiansemester.id' => $ujiansemester->id]);
		return back()->with('message_success', 'Ujian Semester berhasil dihapus!');
	}

}
