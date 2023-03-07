<?php
namespace App\Modules\Jurnal\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Jurnal\Models\Jurnal;
use App\Modules\Jadwal\Models\Jadwal;
use App\Modules\Presensi\Models\Presensi;
use App\Modules\Guru\Models\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class JurnalController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Jurnal";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		if(session('active_role')['id'] == '9ec7541e-5a5e-4a3a-a255-6ffb46895f46')
		{
			return redirect(route('jurnal.guru.index'));
		}

		$query = Jurnal::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Jurnal::jurnal', array_merge($data, ['title' => $this->title]));
	}

	public function index_guru(Request $request)
	{
		$query = Jurnal::get_jurnal_guru(Auth::id(), get_semester('active_semester_id'));
		$data['data'] = $query->paginate(20)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Jurnal::jurnal_guru', array_merge($data, ['title' => $this->title]));
	}

	public function detail_jurnal(Request $request, $id_jurnal)
	{
		$data_jurnal = Jurnal::get_detail_jurnal($id_jurnal);

		$data['siswa']	= Presensi::get_presensi_by_idjurnal($data_jurnal->id);
		$data['jurnal']	= $data_jurnal;

		$this->log($request, 'melihat halaman presensi jurnal');
		return view('Jurnal::detail_jurnal', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$cek_jurnal = Jurnal::get_jurnal_by_idjadwal_and_date($request->get('id_jadwal'), date('Y-m-d'));

		if($cek_jurnal)
		{
			return redirect()->route('presensi.jurnal.index', $cek_jurnal->id)->with('message_success', 'Jurnal berhasil disimpan!');
		}

		$ref_jadwal = Jadwal::find($request->get('id_jadwal'));
		
		$data['forms'] = array(
			'id_jadwal' => ['', Form::hidden("id_jadwal", $ref_jadwal->id, ["class" => "form-control"]) ],
			'jadwal' => ['Jadwal', Form::text("jadwal", $ref_jadwal->mapel['mapel'], ["class" => "form-control", "disabled" => "disabled"]) ],
			'tgl_pembelajaran' => ['Tanggal Pembelajaran', Form::date("tgl_pembelajaran", date('Y-m-d'), ["class" => "form-control"]) ],
			'materi' => ['Materi', Form::textarea("materi", old("materi"), ["class" => "form-control rich-editor"]) ],
			'catatan' => ['Catatan', Form::textarea("catatan", old("catatan"), ["class" => "form-control rich-editor"]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Jurnal::jurnal_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_jadwal' => 'required',
			'materi' => 'required',
			'tgl_pembelajaran' => 'required'
			
		]);

		// dd($request);

		$jurnal = new Jurnal();
		$jurnal->id_jadwal = $request->input("id_jadwal");
		$jurnal->materi = $request->input("materi");
		$jurnal->tgl_pembelajaran = $request->input("tgl_pembelajaran");
		$jurnal->catatan = $request->input("catatan");
		
		$jurnal->created_by = Auth::id();
		$jurnal->save();

		$text = 'membuat '.$this->title; //' baru '.$jurnal->what;
		$this->log($request, $text, ['jurnal.id' => $jurnal->id]);
		// return redirect()->route('jurnal.index')->with('message_success', 'Jurnal berhasil ditambahkan!');
		return redirect()->route('presensi.jurnal.index', $jurnal->id)->with('message_success', 'Jurnal berhasil disimpan!');
	}

	public function show(Request $request, Jurnal $jurnal)
	{
		$data['jurnal'] = $jurnal;

		$text = 'melihat detail '.$this->title;//.' '.$jurnal->what;
		$this->log($request, $text, ['jurnal.id' => $jurnal->id]);
		return view('Jurnal::jurnal_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Jurnal $jurnal)
	{
		$data['jurnal'] = $jurnal;

		$ref_jadwal = Jadwal::find($jurnal->id_jadwal);
		
		$data['forms'] = array(
			'id_jadwal' => ['Jadwal', Form::text("id_jadwal", $ref_jadwal->mapel['mapel'], ["class" => "form-control", "disabled"]) ],
			'materi' => ['Materi', Form::textarea("materi", $jurnal->materi, ["class" => "form-control rich-editor"]) ],
			'tgl_pembelajaran' => ['Tgl Pembelajaran', Form::date("tgl_pembelajaran", $jurnal->tgl_pembelajaran, ["class" => "form-control", "id" => "tgl_pembelajaran"]) ],
			'catatan' => ['Catatan', Form::textarea("catatan", $jurnal->catatan, ["class" => "form-control rich-editor"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$jurnal->what;
		$this->log($request, $text, ['jurnal.id' => $jurnal->id]);
		return view('Jurnal::jurnal_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'materi' => 'required',
			'tgl_pembelajaran' => 'required'
			
		]);
		
		$jurnal = Jurnal::find($id);
		$jurnal->materi = $request->input("materi");
		$jurnal->tgl_pembelajaran = $request->input("tgl_pembelajaran");
		$jurnal->catatan = $request->input("catatan");
		
		$jurnal->updated_by = Auth::id();
		$jurnal->save();


		$text = 'mengedit '.$this->title;//.' '.$jurnal->what;
		$this->log($request, $text, ['jurnal.id' => $jurnal->id]);
		return redirect()->route('jurnal.index')->with('message_success', 'Jurnal berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$jurnal = Jurnal::find($id);
		$jurnal->deleted_by = Auth::id();
		$jurnal->save();
		$jurnal->delete();

		$text = 'menghapus '.$this->title;//.' '.$jurnal->what;
		$this->log($request, $text, ['jurnal.id' => $jurnal->id]);
		return back()->with('message_success', 'Jurnal berhasil dihapus!');
	}

}
