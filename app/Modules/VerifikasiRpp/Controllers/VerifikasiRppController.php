<?php
namespace App\Modules\VerifikasiRpp\Controllers;

use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\VerifikasiRpp\Models\VerifikasiRpp;
use App\Modules\Guru\Models\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerifikasiRppController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Verifikasi Rpp";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = VerifikasiRpp::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('VerifikasiRpp::verifikasirpp', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$data['selected_id_guru'] = old('id_guru', $request->get('id_guru'));

		$data['ref_guru'] = Guru::all()->pluck('nama','id');
		$data['ref_jurusan'] = DB::table('jam_mengajar as jm')
			->join('kelas as k', 'jm.id_kelas', '=', 'k.id')
			->join('jurusan as j', 'k.id_jurusan', '=', 'j.id')
			->where('jm.id_guru', $data['selected_id_guru'])
			->where('jm.id_semester', session('active_semester')['id'])
			->groupBy('j.id', 'j.jurusan')
			->pluck('j.jurusan', 'j.id');
		$data['ref_mapel'] = DB::table('jam_mengajar as jm')
			->join('mapel as m', 'jm.id_mapel', '=', 'm.id')
			->where('jm.id_guru', $data['selected_id_guru'])
			->where('jm.id_semester', session('active_semester')['id'])
			->groupBy('m.id', 'm.mapel')
			->pluck('m.mapel', 'm.id');
		$data['selected_id_semester'] = session('active_semester')['id'];
		$data['ref_tingkat'] = DB::table('jam_mengajar as jm')
			->join('kelas as k', 'jm.id_kelas', '=', 'k.id')
			->join('tingkat as t', 'k.id_tingkat', '=', 't.id')
			->where('jm.id_guru', $data['selected_id_guru'])
			->where('jm.id_semester', session('active_semester')['id'])
			->groupBy('t.id', 't.tingkat')
			->pluck('t.tingkat', 't.id');

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('VerifikasiRpp::verifikasirpp_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_guru' => 'required',
			'id_semester' => 'required',
			'id_mapel' => 'required',
			'id_tingkat' => 'required',
			'id_jurusan' => 'required',
			'identitas' => 'required',
			'tp' => 'required',
			'pembelajaran' => 'required',
			'assesmen' => 'required',
			'lampiran' => 'required',
			'catatan' => 'nullable',

		]);

		$verifikasirpp = new VerifikasiRpp();
		$verifikasirpp->id_guru = $request->input("id_guru");
		$verifikasirpp->id_semester = $request->input("id_semester");
		$verifikasirpp->id_mapel = $request->input("id_mapel");
		$verifikasirpp->id_tingkat = $request->input("id_tingkat");
		$verifikasirpp->id_jurusan = $request->input("id_jurusan");
		$verifikasirpp->identitas = $request->input("identitas");
		$verifikasirpp->tp = $request->input("tp");
		$verifikasirpp->pembelajaran = $request->input("pembelajaran");
		$verifikasirpp->assesmen = $request->input("assesmen");
		$verifikasirpp->lampiran = $request->input("lampiran");
		$verifikasirpp->catatan = $request->input("catatan");

		$verifikasirpp->created_by = Auth::id();
		$verifikasirpp->save();

		$text = 'membuat '.$this->title; //' baru '.$verifikasirpp->what;
		$this->log($request, $text, ['verifikasirpp.id' => $verifikasirpp->id]);
		return redirect()->route('jammengajar.index')->with('message_success', 'Verifikasi Rpp berhasil ditambahkan!');
	}

	public function detail(Request $request, VerifikasiRpp $verifikasirpp)
	{
		$verifikasirpp->load('guru', 'mapel', 'tingkat', 'jurusan', 'semester');

		$components = [
			'identitas' => (int) $verifikasirpp->identitas,
			'tp' => (int) $verifikasirpp->tp,
			'pembelajaran' => (int) $verifikasirpp->pembelajaran,
			'assesmen' => (int) $verifikasirpp->assesmen,
			'lampiran' => (int) $verifikasirpp->lampiran,
		];

		$totalSkor = array_sum($components);
		$maxSkor = count($components) * 2;
		$nilai = $maxSkor > 0 ? round(($totalSkor / $maxSkor) * 100) : 0;

		if ($nilai >= 91) {
			$predikat = 'Amat Baik';
		} elseif ($nilai >= 81) {
			$predikat = 'Baik';
		} elseif ($nilai >= 71) {
			$predikat = 'Cukup';
		} else {
			$predikat = 'Kurang';
		}

		$this->log($request, 'melihat detail '.$this->title, ['verifikasirpp.id' => $verifikasirpp->id]);

		return response()->json([
			'success' => true,
			'data' => [
				'nama_guru' => $verifikasirpp->guru->nama ?? '-',
				'mapel' => $verifikasirpp->mapel->mapel ?? '-',
				'tingkat' => $verifikasirpp->tingkat->tingkat ?? '-',
				'jurusan' => $verifikasirpp->jurusan->jurusan ?? '-',
				'semester' => $verifikasirpp->semester->semester ?? '-',
				'catatan' => $verifikasirpp->catatan,
				'created_at' => optional($verifikasirpp->created_at)->toIso8601String(),
				'components' => $components,
				'total_skor' => $totalSkor,
				'max_skor' => $maxSkor,
				'nilai' => $nilai,
				'predikat' => $predikat,
			],
		]);
	}

	public function show(Request $request, VerifikasiRpp $verifikasirpp)
	{
		$data['verifikasirpp'] = $verifikasirpp;

		$text = 'melihat detail '.$this->title;//.' '.$verifikasirpp->what;
		$this->log($request, $text, ['verifikasirpp.id' => $verifikasirpp->id]);
		return view('VerifikasiRpp::verifikasirpp_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, VerifikasiRpp $verifikasirpp)
	{
		$data['verifikasirpp'] = $verifikasirpp;
		$data['selected_id_guru'] = $verifikasirpp->id_guru;
		$data['selected_id_semester'] = $verifikasirpp->id_semester;

		$data['ref_guru'] = Guru::all()->pluck('nama','id');
		$data['ref_jurusan'] = DB::table('jam_mengajar as jm')
			->join('kelas as k', 'jm.id_kelas', '=', 'k.id')
			->join('jurusan as j', 'k.id_jurusan', '=', 'j.id')
			->where('jm.id_guru', $verifikasirpp->id_guru)
			->where('jm.id_semester', $verifikasirpp->id_semester)
			->groupBy('j.id', 'j.jurusan')
			->pluck('j.jurusan', 'j.id');
		$data['ref_mapel'] = DB::table('jam_mengajar as jm')
			->join('mapel as m', 'jm.id_mapel', '=', 'm.id')
			->where('jm.id_guru', $verifikasirpp->id_guru)
			->where('jm.id_semester', $verifikasirpp->id_semester)
			->groupBy('m.id', 'm.mapel')
			->pluck('m.mapel', 'm.id');
		$data['ref_tingkat'] = DB::table('jam_mengajar as jm')
			->join('kelas as k', 'jm.id_kelas', '=', 'k.id')
			->join('tingkat as t', 'k.id_tingkat', '=', 't.id')
			->where('jm.id_guru', $verifikasirpp->id_guru)
			->where('jm.id_semester', $verifikasirpp->id_semester)
			->groupBy('t.id', 't.tingkat')
			->pluck('t.tingkat', 't.id');

		$text = 'membuka form edit '.$this->title;//.' '.$verifikasirpp->what;
		$this->log($request, $text, ['verifikasirpp.id' => $verifikasirpp->id]);
		return view('VerifikasiRpp::verifikasirpp_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_guru' => 'required',
			'id_semester' => 'required',
			'id_mapel' => 'required',
			'id_tingkat' => 'required',
			'id_jurusan' => 'required',
			'identitas' => 'required',
			'tp' => 'required',
			'pembelajaran' => 'required',
			'assesmen' => 'required',
			'lampiran' => 'required',
			'catatan' => 'nullable',

		]);

		$verifikasirpp = VerifikasiRpp::find($id);
		$verifikasirpp->id_guru = $request->input("id_guru");
		$verifikasirpp->id_semester = $request->input("id_semester");
		$verifikasirpp->id_mapel = $request->input("id_mapel");
		$verifikasirpp->id_tingkat = $request->input("id_tingkat");
		$verifikasirpp->id_jurusan = $request->input("id_jurusan");
		$verifikasirpp->identitas = $request->input("identitas");
		$verifikasirpp->tp = $request->input("tp");
		$verifikasirpp->pembelajaran = $request->input("pembelajaran");
		$verifikasirpp->assesmen = $request->input("assesmen");
		$verifikasirpp->lampiran = $request->input("lampiran");
		$verifikasirpp->catatan = $request->input("catatan");

		$verifikasirpp->updated_by = Auth::id();
		$verifikasirpp->save();


		$text = 'mengedit '.$this->title;//.' '.$verifikasirpp->what;
		$this->log($request, $text, ['verifikasirpp.id' => $verifikasirpp->id]);
		return redirect()->route('jammengajar.index')->with('message_success', 'Verifikasi Rpp berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$verifikasirpp = VerifikasiRpp::find($id);
		$verifikasirpp->deleted_by = Auth::id();
		$verifikasirpp->save();
		$verifikasirpp->delete();

		$text = 'menghapus '.$this->title;//.' '.$verifikasirpp->what;
		$this->log($request, $text, ['verifikasirpp.id' => $verifikasirpp->id]);
		return back()->with('message_success', 'Verifikasi Rpp berhasil dihapus!');
	}

}
