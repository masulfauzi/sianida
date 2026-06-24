<?php
namespace App\Modules\VerifikasiAtp\Controllers;

use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\VerifikasiAtp\Models\VerifikasiAtp;
use App\Modules\Guru\Models\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerifikasiAtpController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Verifikasi Atp";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = VerifikasiAtp::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('VerifikasiAtp::verifikasiatp', array_merge($data, ['title' => $this->title]));
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
		return view('VerifikasiAtp::verifikasiatp_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'alokasi_waktu' => 'required',
			'cp' => 'required',
			'id_guru' => 'required',
			'id_jurusan' => 'required',
			'id_mapel' => 'required',
			'id_semester' => 'required',
			'id_tingkat' => 'required',
			'identitas' => 'required',
			'materi' => 'required',
			'metode' => 'required',
			'tp' => 'required',
			'catatan' => 'nullable',

		]);

		$verifikasiatp = new VerifikasiAtp();
		$verifikasiatp->alokasi_waktu = $request->input("alokasi_waktu");
		$verifikasiatp->cp = $request->input("cp");
		$verifikasiatp->id_guru = $request->input("id_guru");
		$verifikasiatp->id_jurusan = $request->input("id_jurusan");
		$verifikasiatp->id_mapel = $request->input("id_mapel");
		$verifikasiatp->id_semester = $request->input("id_semester");
		$verifikasiatp->id_tingkat = $request->input("id_tingkat");
		$verifikasiatp->identitas = $request->input("identitas");
		$verifikasiatp->materi = $request->input("materi");
		$verifikasiatp->metode = $request->input("metode");
		$verifikasiatp->tp = $request->input("tp");
		$verifikasiatp->catatan = $request->input("catatan");

		$verifikasiatp->created_by = Auth::id();
		$verifikasiatp->save();

		$text = 'membuat '.$this->title; //' baru '.$verifikasiatp->what;
		$this->log($request, $text, ['verifikasiatp.id' => $verifikasiatp->id]);
		return redirect()->route('jammengajar.index')->with('message_success', 'Verifikasi Atp berhasil ditambahkan!');
	}

	public function detail(Request $request, VerifikasiAtp $verifikasiatp)
	{
		$verifikasiatp->load('guru', 'mapel', 'tingkat', 'jurusan', 'semester');

		$components = [
			'identitas' => (int) $verifikasiatp->identitas,
			'cp' => (int) $verifikasiatp->cp,
			'tp' => (int) $verifikasiatp->tp,
			'alokasi_waktu' => (int) $verifikasiatp->alokasi_waktu,
			'materi' => (int) $verifikasiatp->materi,
			'metode' => (int) $verifikasiatp->metode,
		];

		$totalSkor = array_sum($components);
		$maxSkor = count($components) * 4;
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

		$this->log($request, 'melihat detail '.$this->title, ['verifikasiatp.id' => $verifikasiatp->id]);

		return response()->json([
			'success' => true,
			'data' => [
				'nama_sekolah' => env('APP_NAME', 'Nama Sekolah'),
				'nama_guru' => $verifikasiatp->guru->nama ?? '-',
				'mapel' => $verifikasiatp->mapel->mapel ?? '-',
				'tingkat' => $verifikasiatp->tingkat->tingkat ?? '-',
				'jurusan' => $verifikasiatp->jurusan->jurusan ?? '-',
				'semester' => $verifikasiatp->semester->semester ?? '-',
				'catatan' => $verifikasiatp->catatan,
				'created_at' => optional($verifikasiatp->created_at)->toIso8601String(),
				'components' => $components,
				'total_skor' => $totalSkor,
				'max_skor' => $maxSkor,
				'nilai' => $nilai,
				'predikat' => $predikat,
			],
		]);
	}

	public function show(Request $request, VerifikasiAtp $verifikasiatp)
	{
		$data['verifikasiatp'] = $verifikasiatp;

		$text = 'melihat detail '.$this->title;//.' '.$verifikasiatp->what;
		$this->log($request, $text, ['verifikasiatp.id' => $verifikasiatp->id]);
		return view('VerifikasiAtp::verifikasiatp_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, VerifikasiAtp $verifikasiatp)
	{
		$data['verifikasiatp'] = $verifikasiatp;
		$data['selected_id_guru'] = $verifikasiatp->id_guru;
		$data['selected_id_semester'] = $verifikasiatp->id_semester;

		$data['ref_guru'] = Guru::all()->pluck('nama','id');
		$data['ref_jurusan'] = DB::table('jam_mengajar as jm')
			->join('kelas as k', 'jm.id_kelas', '=', 'k.id')
			->join('jurusan as j', 'k.id_jurusan', '=', 'j.id')
			->where('jm.id_guru', $verifikasiatp->id_guru)
			->where('jm.id_semester', $verifikasiatp->id_semester)
			->groupBy('j.id', 'j.jurusan')
			->pluck('j.jurusan', 'j.id');
		$data['ref_mapel'] = DB::table('jam_mengajar as jm')
			->join('mapel as m', 'jm.id_mapel', '=', 'm.id')
			->where('jm.id_guru', $verifikasiatp->id_guru)
			->where('jm.id_semester', $verifikasiatp->id_semester)
			->groupBy('m.id', 'm.mapel')
			->pluck('m.mapel', 'm.id');
		$data['ref_tingkat'] = DB::table('jam_mengajar as jm')
			->join('kelas as k', 'jm.id_kelas', '=', 'k.id')
			->join('tingkat as t', 'k.id_tingkat', '=', 't.id')
			->where('jm.id_guru', $verifikasiatp->id_guru)
			->where('jm.id_semester', $verifikasiatp->id_semester)
			->groupBy('t.id', 't.tingkat')
			->pluck('t.tingkat', 't.id');

		$text = 'membuka form edit '.$this->title;//.' '.$verifikasiatp->what;
		$this->log($request, $text, ['verifikasiatp.id' => $verifikasiatp->id]);
		return view('VerifikasiAtp::verifikasiatp_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'alokasi_waktu' => 'required',
			'cp' => 'required',
			'id_guru' => 'required',
			'id_jurusan' => 'required',
			'id_mapel' => 'required',
			'id_semester' => 'required',
			'id_tingkat' => 'required',
			'identitas' => 'required',
			'materi' => 'required',
			'metode' => 'required',
			'tp' => 'required',
			'catatan' => 'nullable',

		]);

		$verifikasiatp = VerifikasiAtp::find($id);
		$verifikasiatp->alokasi_waktu = $request->input("alokasi_waktu");
		$verifikasiatp->cp = $request->input("cp");
		$verifikasiatp->id_guru = $request->input("id_guru");
		$verifikasiatp->id_jurusan = $request->input("id_jurusan");
		$verifikasiatp->id_mapel = $request->input("id_mapel");
		$verifikasiatp->id_semester = $request->input("id_semester");
		$verifikasiatp->id_tingkat = $request->input("id_tingkat");
		$verifikasiatp->identitas = $request->input("identitas");
		$verifikasiatp->materi = $request->input("materi");
		$verifikasiatp->metode = $request->input("metode");
		$verifikasiatp->tp = $request->input("tp");
		$verifikasiatp->catatan = $request->input("catatan");

		$verifikasiatp->updated_by = Auth::id();
		$verifikasiatp->save();


		$text = 'mengedit '.$this->title;//.' '.$verifikasiatp->what;
		$this->log($request, $text, ['verifikasiatp.id' => $verifikasiatp->id]);
		return redirect()->route('jammengajar.index')->with('message_success', 'Verifikasi Atp berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$verifikasiatp = VerifikasiAtp::find($id);
		$verifikasiatp->deleted_by = Auth::id();
		$verifikasiatp->save();
		$verifikasiatp->delete();

		$text = 'menghapus '.$this->title;//.' '.$verifikasiatp->what;
		$this->log($request, $text, ['verifikasiatp.id' => $verifikasiatp->id]);
		return back()->with('message_success', 'Verifikasi Atp berhasil dihapus!');
	}

}
