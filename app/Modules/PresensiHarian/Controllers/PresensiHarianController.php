<?php
namespace App\Modules\PresensiHarian\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\PresensiHarian\Models\PresensiHarian;
use App\Modules\Siswa\Models\Siswa;
use App\Modules\Statuskehadiran\Models\Statuskehadiran;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PresensiHarianController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Presensi Harian";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = PresensiHarian::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('PresensiHarian::presensiharian', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$ref_siswa = Siswa::all()->pluck('nama_siswa','id');
		$ref_statuskehadiran = Statuskehadiran::all()->pluck('status_kehadiran','id');
		
		$data['forms'] = array(
			'id_siswa' => ['Siswa', Form::select("id_siswa", $ref_siswa, null, ["class" => "form-control select2"]) ],
			'id_status_kehadiran' => ['Status Kehadiran', Form::select("id_status_kehadiran", $ref_statuskehadiran, null, ["class" => "form-control select2"]) ],
			'tgl' => ['Tgl', Form::text("tgl", old("tgl"), ["class" => "form-control datepicker"]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('PresensiHarian::presensiharian_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_siswa' => 'required',
			'id_status_kehadiran' => 'required',
			'tgl' => 'required',
			
		]);

		$presensiharian = new PresensiHarian();
		$presensiharian->id_siswa = $request->input("id_siswa");
		$presensiharian->id_status_kehadiran = $request->input("id_status_kehadiran");
		$presensiharian->tgl = $request->input("tgl");
		
		$presensiharian->created_by = Auth::id();
		$presensiharian->save();

		$text = 'membuat '.$this->title; //' baru '.$presensiharian->what;
		$this->log($request, $text, ['presensiharian.id' => $presensiharian->id]);
		return redirect()->route('presensiharian.index')->with('message_success', 'Presensi Harian berhasil ditambahkan!');
	}

	public function show(Request $request, PresensiHarian $presensiharian)
	{
		$data['presensiharian'] = $presensiharian;

		$text = 'melihat detail '.$this->title;//.' '.$presensiharian->what;
		$this->log($request, $text, ['presensiharian.id' => $presensiharian->id]);
		return view('PresensiHarian::presensiharian_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, PresensiHarian $presensiharian)
	{
		$data['presensiharian'] = $presensiharian;

		$ref_siswa = Siswa::all()->pluck('nama_siswa','id');
		$ref_statuskehadiran = Statuskehadiran::all()->pluck('status_kehadiran','id');
		
		$data['forms'] = array(
			'id_siswa' => ['Siswa', Form::select("id_siswa", $ref_siswa, null, ["class" => "form-control select2"]) ],
			'id_status_kehadiran' => ['Status Kehadiran', Form::select("id_status_kehadiran", $ref_statuskehadiran, null, ["class" => "form-control select2"]) ],
			'tgl' => ['Tgl', Form::text("tgl", $presensiharian->tgl, ["class" => "form-control datepicker", "id" => "tgl"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$presensiharian->what;
		$this->log($request, $text, ['presensiharian.id' => $presensiharian->id]);
		return view('PresensiHarian::presensiharian_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_siswa' => 'required',
			'id_status_kehadiran' => 'required',
			'tgl' => 'required',
			
		]);
		
		$presensiharian = PresensiHarian::find($id);
		$presensiharian->id_siswa = $request->input("id_siswa");
		$presensiharian->id_status_kehadiran = $request->input("id_status_kehadiran");
		$presensiharian->tgl = $request->input("tgl");
		
		$presensiharian->updated_by = Auth::id();
		$presensiharian->save();


		$text = 'mengedit '.$this->title;//.' '.$presensiharian->what;
		$this->log($request, $text, ['presensiharian.id' => $presensiharian->id]);
		return redirect()->route('presensiharian.index')->with('message_success', 'Presensi Harian berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$presensiharian = PresensiHarian::find($id);
		$presensiharian->deleted_by = Auth::id();
		$presensiharian->save();
		$presensiharian->delete();

		$text = 'menghapus '.$this->title;//.' '.$presensiharian->what;
		$this->log($request, $text, ['presensiharian.id' => $presensiharian->id]);
		return back()->with('message_success', 'Presensi Harian berhasil dihapus!');
	}

}
