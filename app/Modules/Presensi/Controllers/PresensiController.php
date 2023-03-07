<?php
namespace App\Modules\Presensi\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Presensi\Models\Presensi;
use App\Modules\Jurnal\Models\Jurnal;
use App\Modules\Pesertadidik\Models\Pesertadidik;
use App\Modules\Statuskehadiran\Models\Statuskehadiran;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PresensiController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Presensi";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = Presensi::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Presensi::presensi', array_merge($data, ['title' => $this->title]));
	}

	public function presensi_jurnal(Request $request, $id_jurnal)
	{
		$data_jurnal = Jurnal::get_detail_jurnal($id_jurnal);

		// dd($data_jurnal);

		//cek tabel presensi
		$data_siswa = Presensi::get_presensi_by_idjurnal($data_jurnal->id);

		if($data_siswa->count() == 0)
		{
			$this->insert_presensi_by_idkelas($data_jurnal->id_kelas, $data_jurnal->id);
		}

		$data['siswa']	= Presensi::get_presensi_by_idjurnal($data_jurnal->id);
		$data['jurnal']	= $data_jurnal;

		$this->log($request, 'melihat halaman presensi jurnal');
		return view('Presensi::presensi_jurnal', array_merge($data, ['title' => $this->title]));
	}

	public function presensi_jurnal_store(Request $request)
	{
		// dd($request->all());
		for($i=1; $i<=count($request->get('id')); $i++)
		{
			$data_presensi = [
				'id'					=> $request->get('id')[$i],
				'id_statuskehadiran'	=> $request->get('id_statuskehadiran')[$i],
				'catatan'				=> $request->get('catatan')[$i]
			];

			Presensi::update_presensi($data_presensi);
		}

		return redirect()->route('presensi.jurnal.index', $request->get('id_jurnal'))->with('message_success', 'Presensi berhasil disimpan!');
	}

	public function insert_presensi_by_idkelas($id_kelas, $id_jurnal)
	{
		$siswa = Pesertadidik::get_pd_by_idkelas($id_kelas);

		foreach($siswa as $data)
		{
			$data_presensi = [
				'id'					=> Str::uuid(),
				'id_jurnal'				=> $id_jurnal,
				'id_pesertadidik'		=> $data->id,
				'id_statuskehadiran'	=> '5cb7e9bc-79dc-4deb-8bd2-77930dbca9a3',
				'created_by'			=> Auth::id(),
				'created_at'			=> Carbon::now()->toDateTimeString()
			];

			Presensi::insert_presensi($data_presensi);
		}
	}

	public function create(Request $request)
	{
		$ref_jurnal = Jurnal::all()->pluck('id_jadwal','id');
		$ref_pesertadidik = Pesertadidik::all()->pluck('id_semester','id');
		$ref_statuskehadiran = Statuskehadiran::all()->pluck('status_kehadiran','id');
		
		$data['forms'] = array(
			'id_jurnal' => ['Jurnal', Form::select("id_jurnal", $ref_jurnal, null, ["class" => "form-control select2"]) ],
			'id_pesertadidik' => ['Pesertadidik', Form::select("id_pesertadidik", $ref_pesertadidik, null, ["class" => "form-control select2"]) ],
			'id_statuskehadiran' => ['Statuskehadiran', Form::select("id_statuskehadiran", $ref_statuskehadiran, null, ["class" => "form-control select2"]) ],
			'catatan' => ['Catatan', Form::text("catatan", old("catatan"), ["class" => "form-control","placeholder" => ""]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Presensi::presensi_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_jurnal' => 'required',
			'id_pesertadidik' => 'required',
			'id_statuskehadiran' => 'required',
			'catatan' => 'required',
			
		]);

		$presensi = new Presensi();
		$presensi->id_jurnal = $request->input("id_jurnal");
		$presensi->id_pesertadidik = $request->input("id_pesertadidik");
		$presensi->id_statuskehadiran = $request->input("id_statuskehadiran");
		$presensi->catatan = $request->input("catatan");
		
		$presensi->created_by = Auth::id();
		$presensi->save();

		$text = 'membuat '.$this->title; //' baru '.$presensi->what;
		$this->log($request, $text, ['presensi.id' => $presensi->id]);
		return redirect()->route('presensi.index')->with('message_success', 'Presensi berhasil ditambahkan!');
	}

	public function show(Request $request, Presensi $presensi)
	{
		$data['presensi'] = $presensi;

		$text = 'melihat detail '.$this->title;//.' '.$presensi->what;
		$this->log($request, $text, ['presensi.id' => $presensi->id]);
		return view('Presensi::presensi_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Presensi $presensi)
	{
		$data['presensi'] = $presensi;

		$ref_jurnal = Jurnal::all()->pluck('id_jadwal','id');
		$ref_pesertadidik = Pesertadidik::all()->pluck('id_semester','id');
		$ref_statuskehadiran = Statuskehadiran::all()->pluck('status_kehadiran','id');
		
		$data['forms'] = array(
			'id_jurnal' => ['Jurnal', Form::select("id_jurnal", $ref_jurnal, null, ["class" => "form-control select2"]) ],
			'id_pesertadidik' => ['Pesertadidik', Form::select("id_pesertadidik", $ref_pesertadidik, null, ["class" => "form-control select2"]) ],
			'id_statuskehadiran' => ['Statuskehadiran', Form::select("id_statuskehadiran", $ref_statuskehadiran, null, ["class" => "form-control select2"]) ],
			'catatan' => ['Catatan', Form::text("catatan", $presensi->catatan, ["class" => "form-control","placeholder" => "", "id" => "catatan"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$presensi->what;
		$this->log($request, $text, ['presensi.id' => $presensi->id]);
		return view('Presensi::presensi_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_jurnal' => 'required',
			'id_pesertadidik' => 'required',
			'id_statuskehadiran' => 'required',
			'catatan' => 'required',
			
		]);
		
		$presensi = Presensi::find($id);
		$presensi->id_jurnal = $request->input("id_jurnal");
		$presensi->id_pesertadidik = $request->input("id_pesertadidik");
		$presensi->id_statuskehadiran = $request->input("id_statuskehadiran");
		$presensi->catatan = $request->input("catatan");
		
		$presensi->updated_by = Auth::id();
		$presensi->save();


		$text = 'mengedit '.$this->title;//.' '.$presensi->what;
		$this->log($request, $text, ['presensi.id' => $presensi->id]);
		return redirect()->route('presensi.index')->with('message_success', 'Presensi berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$presensi = Presensi::find($id);
		$presensi->deleted_by = Auth::id();
		$presensi->save();
		$presensi->delete();

		$text = 'menghapus '.$this->title;//.' '.$presensi->what;
		$this->log($request, $text, ['presensi.id' => $presensi->id]);
		return back()->with('message_success', 'Presensi berhasil dihapus!');
	}

	public function get_siswa_kehadiran(Request $request)
	{
		$data['siswa'] = Presensi::get_kehadiran_siswa($request->id, get_semester('active_semester_id'), date('Y-m-d'));
		// dd($request);

		return view('Presensi::status_presensi', $data);
	}

}
