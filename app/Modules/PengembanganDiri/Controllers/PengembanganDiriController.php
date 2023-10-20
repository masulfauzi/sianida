<?php
namespace App\Modules\PengembanganDiri\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\PengembanganDiri\Models\PengembanganDiri;
use App\Modules\JenisPengembangan\Models\JenisPengembangan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PengembanganDiriController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Pengembangan Diri";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = PengembanganDiri::query();
		if(session('active_role')['id'] == '9ec7541e-5a5e-4a3a-a255-6ffb46895f46')
		{
			$query->where('id_guru', session('id_guru'));
		}
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('PengembanganDiri::pengembangandiri', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$ref_jenis_pengembangan = JenisPengembangan::all()->pluck('jenis_pengembangan','id');
		$ref_jenis_pengembangan->prepend('-PILIH SALAH SATU-', '');
		
		$data['forms'] = array(
			'id_jenis_pengembangan' => ['Jenis Pengembangan', Form::select("id_jenis_pengembangan", $ref_jenis_pengembangan, null, ["class" => "form-control select2"]) ],
			'nama_kegiatan' => ['Nama Kegiatan', Form::text("nama_kegiatan", old("nama_kegiatan"), ["class" => "form-control","placeholder" => ""]) ],
			'tgl_kegiatan' => ['Tanggal Kegiatan (Tanggal Sertifikat)', Form::text("tgl_kegiatan", old("tgl_kegiatan"), ["class" => "form-control datepicker"]) ],
			'tempat' => ['Tempat', Form::text("tempat", old("tempat"), ["class" => "form-control","placeholder" => ""]) ],
			'sertifkat' => ['Sertifikat', Form::file("sertifikat", ["class" => "form-control","placeholder" => ""]) ],
			'laporan' => ['Laporan (Tidak Wajib)', Form::file("laporan", ["class" => "form-control","placeholder" => ""]) ],
			'id_guru' => ['', Form::hidden("id_guru", session('id_guru')) ],

		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('PengembanganDiri::pengembangandiri_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_jenis_pengembangan' => 'required',
			'id_guru' => 'required',
			'nama_kegiatan' => 'required',
			'tgl_kegiatan' => 'required|date',
			'tempat' => 'required',
			'sertifikat' => 'required|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
			
		]);

		if($request->has('laporan'))
		{
			$laporan = time().'.'.$request->laporan->extension();  

        	$request->laporan->move(public_path('uploads/pengembangan_diri/'), $laporan);
		}
		else{
			$laporan = NULL;
		}

		$tgl = explode('-', $request->input('tgl_kegiatan'));

		$fileName = time().'.'.$request->sertifikat->extension();  

        $request->sertifikat->move(public_path('uploads/pengembangan_diri/'), $fileName);

		$pengembangandiri = new PengembanganDiri();
		$pengembangandiri->id_jenis_pengembangan = $request->input("id_jenis_pengembangan");
		$pengembangandiri->id_guru = $request->input("id_guru");
		$pengembangandiri->nama_kegiatan = $request->input("nama_kegiatan");
		$pengembangandiri->tgl_kegiatan = $request->input("tgl_kegiatan");
		$pengembangandiri->tempat = $request->input("tempat");
		$pengembangandiri->tahun = $tgl[0];
		$pengembangandiri->sertifikat = $fileName;
		$pengembangandiri->laporan = $laporan;
		
		$pengembangandiri->created_by = Auth::id();
		$pengembangandiri->save();

		$text = 'membuat '.$this->title; //' baru '.$pengembangandiri->what;
		$this->log($request, $text, ['pengembangandiri.id' => $pengembangandiri->id]);
		return redirect()->route('pengembangandiri.index')->with('message_success', 'Pengembangan Diri berhasil ditambahkan!');
	}

	public function show(Request $request, PengembanganDiri $pengembangandiri)
	{
		$data['pengembangandiri'] = $pengembangandiri;

		$text = 'melihat detail '.$this->title;//.' '.$pengembangandiri->what;
		$this->log($request, $text, ['pengembangandiri.id' => $pengembangandiri->id]);
		return view('PengembanganDiri::pengembangandiri_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, PengembanganDiri $pengembangandiri)
	{
		$data['pengembangandiri'] = $pengembangandiri;

		$ref_jenis_pengembangan = JenisPengembangan::all()->pluck('jenis_pengembangan','id');
		
		$data['forms'] = array(
			'id_jenis_pengembangan' => ['Jenis Pengembangan', Form::select("id_jenis_pengembangan", $ref_jenis_pengembangan, null, ["class" => "form-control select2"]) ],
			'id_guru' => ['Guru', Form::text("id_guru", $pengembangandiri->id_guru, ["class" => "form-control","placeholder" => "", "id" => "id_guru"]) ],
			'nama_kegiatan' => ['Nama Kegiatan', Form::text("nama_kegiatan", $pengembangandiri->nama_kegiatan, ["class" => "form-control","placeholder" => "", "id" => "nama_kegiatan"]) ],
			'tgl_kegiatan' => ['Tgl Kegiatan', Form::text("tgl_kegiatan", $pengembangandiri->tgl_kegiatan, ["class" => "form-control datepicker", "id" => "tgl_kegiatan"]) ],
			'tempat' => ['Tempat', Form::text("tempat", $pengembangandiri->tempat, ["class" => "form-control","placeholder" => "", "id" => "tempat"]) ],
			'tahun' => ['Tahun', Form::text("tahun", $pengembangandiri->tahun, ["class" => "form-control","placeholder" => "", "id" => "tahun"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$pengembangandiri->what;
		$this->log($request, $text, ['pengembangandiri.id' => $pengembangandiri->id]);
		return view('PengembanganDiri::pengembangandiri_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_jenis_pengembangan' => 'required',
			'id_guru' => 'required',
			'nama_kegiatan' => 'required',
			'tgl_kegiatan' => 'required',
			'tempat' => 'required',
			'tahun' => 'required',
			
		]);
		
		$pengembangandiri = PengembanganDiri::find($id);
		$pengembangandiri->id_jenis_pengembangan = $request->input("id_jenis_pengembangan");
		$pengembangandiri->id_guru = $request->input("id_guru");
		$pengembangandiri->nama_kegiatan = $request->input("nama_kegiatan");
		$pengembangandiri->tgl_kegiatan = $request->input("tgl_kegiatan");
		$pengembangandiri->tempat = $request->input("tempat");
		$pengembangandiri->tahun = $request->input("tahun");
		
		$pengembangandiri->updated_by = Auth::id();
		$pengembangandiri->save();


		$text = 'mengedit '.$this->title;//.' '.$pengembangandiri->what;
		$this->log($request, $text, ['pengembangandiri.id' => $pengembangandiri->id]);
		return redirect()->route('pengembangandiri.index')->with('message_success', 'Pengembangan Diri berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$pengembangandiri = PengembanganDiri::find($id);
		$pengembangandiri->deleted_by = Auth::id();
		$pengembangandiri->save();
		$pengembangandiri->delete();

		$text = 'menghapus '.$this->title;//.' '.$pengembangandiri->what;
		$this->log($request, $text, ['pengembangandiri.id' => $pengembangandiri->id]);
		return back()->with('message_success', 'Pengembangan Diri berhasil dihapus!');
	}

}
