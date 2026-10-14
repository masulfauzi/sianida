<?php
namespace App\Modules\SoalTka\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\SoalTka\Models\SoalTka;
use App\Modules\Guru\Models\Guru;
use App\Modules\Mapel\Models\Mapel;
use App\Modules\Semester\Models\Semester;
use App\Modules\Tingkat\Models\Tingkat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SoalTkaController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Soal Tka";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$admin_roles = [
			'bf1548f3-295c-4d73-809d-66ab7c240091', // Admin
			'1fe8326c-22c4-4732-9c12-f7b83a16b842', // Super Admin
		];
		$active_role_id = session('active_role')['id'] ?? null;
		if (in_array($active_role_id, $admin_roles) && !session()->has('id_guru') && !$request->has('view_raw')) {
			return redirect()->route('soaltka.monitoring.index');
		}

		$id_guru_aktif = session('id_guru') ?? (Auth::check() ? Guru::get_id_guru_by_id_user(Auth::id())->first()?->id_guru : null);

		$query = SoalTka::query()->with(['guru', 'mapel', 'semester', 'tingkat']);

		if ($id_guru_aktif) {
			$query->where('id_guru', $id_guru_aktif);
		}

		if($request->has('search')){
			$search = $request->get('search');
			$query->where(function($q) use ($search) {
				$q->where('file', 'like', "%$search%")
				  ->orWhereHas('guru', function($qGuru) use ($search) {
					  $qGuru->where('nama', 'like', "%$search%");
				  })
				  ->orWhereHas('mapel', function($qMapel) use ($search) {
					  $qMapel->where('mapel', 'like', "%$search%");
				  });
			});
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('SoalTka::soaltka', array_merge($data, ['title' => $this->title]));
	}

	public function monitoring(Request $request)
	{
		$active_semester_id = session('active_semester.id') ?? (session()->has('active_semester') ? get_semester('active_semester_id') : null) ?? Semester::first()?->id;

		$query = DB::table('jam_mengajar as jm')
			->join('mapel as m', 'jm.id_mapel', '=', 'm.id')
			->join('kelas as k', 'jm.id_kelas', '=', 'k.id')
			->join('tingkat as t', 'k.id_tingkat', '=', 't.id')
			->leftJoin('soal_tka as st', function ($join) use ($active_semester_id) {
				$join->on('st.id_mapel', '=', 'jm.id_mapel')
					->on('st.id_tingkat', '=', 't.id')
					->where('st.id_semester', '=', $active_semester_id)
					->whereNull('st.deleted_at');
			})
			->leftJoin('guru as g', function ($join) {
				$join->on('st.id_guru', '=', 'g.id')
					->whereNull('g.deleted_at');
			})
			->where('jm.id_semester', $active_semester_id)
			->whereNull('jm.deleted_at')
			->whereNull('m.deleted_at')
			->select(
				'jm.id_mapel',
				'm.mapel',
				't.id as id_tingkat',
				't.tingkat',
				DB::raw('GROUP_CONCAT(DISTINCT g.nama SEPARATOR ", ") as nama_guru_pengunggah'),
				DB::raw('MAX(st.id) as id_soal_tka'),
				DB::raw('MAX(st.file) as file_soal_tka')
			)
			->groupBy('jm.id_mapel', 't.id', 'm.mapel', 't.tingkat')
			->orderBy('m.mapel', 'ASC')
			->orderBy('t.tingkat', 'ASC');

		if ($request->has('search') && filled($request->get('search'))) {
			$search = $request->get('search');
			$query->where(function ($q) use ($search) {
				$q->where('m.mapel', 'like', "%{$search}%")
					->orWhere('g.nama', 'like', "%{$search}%")
					->orWhere('t.tingkat', 'like', "%{$search}%");
			});
		}

		if ($request->has('status') && in_array($request->get('status'), ['sudah', 'belum'])) {
			if ($request->get('status') == 'sudah') {
				$query->havingRaw('MAX(st.file) IS NOT NULL');
			} else {
				$query->havingRaw('MAX(st.file) IS NULL');
			}
		}

		$data['data'] = $query->paginate(25)->withQueryString();

		$allStats = DB::table('jam_mengajar as jm')
			->join('mapel as m', 'jm.id_mapel', '=', 'm.id')
			->join('kelas as k', 'jm.id_kelas', '=', 'k.id')
			->join('tingkat as t', 'k.id_tingkat', '=', 't.id')
			->leftJoin('soal_tka as st', function ($join) use ($active_semester_id) {
				$join->on('st.id_mapel', '=', 'jm.id_mapel')
					->on('st.id_tingkat', '=', 't.id')
					->where('st.id_semester', '=', $active_semester_id)
					->whereNull('st.deleted_at');
			})
			->where('jm.id_semester', $active_semester_id)
			->whereNull('jm.deleted_at')
			->whereNull('m.deleted_at')
			->select(
				DB::raw('COUNT(DISTINCT jm.id_mapel, t.id) as total'),
				DB::raw('COUNT(DISTINCT CASE WHEN st.file IS NOT NULL THEN CONCAT(jm.id_mapel, "-", t.id) END) as sudah_upload')
			)
			->first();

		$data['total'] = $allStats->total ?? 0;
		$data['sudah_upload'] = $allStats->sudah_upload ?? 0;
		$data['belum_upload'] = max(0, $data['total'] - $data['sudah_upload']);
		$data['persentase'] = $data['total'] > 0 ? round(($data['sudah_upload'] / $data['total']) * 100, 1) : 0;

		$this->log($request, 'melihat monitoring upload ' . $this->title);
		return view('SoalTka::soaltka_monitoring', array_merge($data, ['title' => 'Monitoring Upload ' . $this->title]));
	}

	public function create(Request $request)
	{
		$active_semester_id = session('active_semester.id') ?? (session()->has('active_semester') ? get_semester('active_semester_id') : null) ?? Semester::first()?->id;
		$id_guru_aktif = session('id_guru') ?? (Auth::check() ? Guru::get_id_guru_by_id_user(Auth::id())->first()?->id_guru : null) ?? Guru::first()?->id;

		$ref_mapel = DB::table('jam_mengajar as jm')
			->join('mapel as m', 'jm.id_mapel', '=', 'm.id')
			->where('jm.id_guru', $id_guru_aktif)
			->where('jm.id_semester', $active_semester_id)
			->whereNull('jm.deleted_at')
			->whereNull('m.deleted_at')
			->distinct()
			->orderBy('m.mapel')
			->pluck('m.mapel', 'm.id');
		$ref_mapel->prepend('-PILIH SALAH SATU-', '');

		$ref_tingkat = Tingkat::all()->sortBy('tingkat')->pluck('tingkat', 'id');
		$ref_tingkat->prepend('-PILIH SALAH SATU-', '');

		$data['forms'] = array(
			'id_guru' => ['', Form::hidden("id_guru", $id_guru_aktif) ],
			'id_semester' => ['', Form::hidden("id_semester", $active_semester_id) ],
			'id_mapel' => ['Mapel', Form::select("id_mapel", $ref_mapel, old("id_mapel"), ["class" => "form-control select2"]) ],
			'id_tingkat' => ['Tingkat', Form::select("id_tingkat", $ref_tingkat, old("id_tingkat"), ["class" => "form-control select2"]) ],
			'file' => ['File', Form::file("file", ["class" => "form-control"]) ],
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('SoalTka::soaltka_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_guru' => 'required',
			'id_mapel' => 'required',
			'id_semester' => 'required',
			'id_tingkat' => 'required',
			'file' => 'required|file|max:20480',
		]);

		$soaltka = new SoalTka();
		$soaltka->id_guru = $request->input("id_guru");
		$soaltka->id_mapel = $request->input("id_mapel");
		$soaltka->id_semester = $request->input("id_semester");
		$soaltka->id_tingkat = $request->input("id_tingkat");
		
		if ($request->hasFile('file')) {
			$destinationPath = public_path('uploads/soal_tka/');
			if (!file_exists($destinationPath)) {
				mkdir($destinationPath, 0777, true);
			}
			$uploadedFile = $request->file('file');
			$fileName = time() . '_' . Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $uploadedFile->getClientOriginalExtension();
			$uploadedFile->move($destinationPath, $fileName);
			$soaltka->file = $fileName;
		}

		$soaltka->created_by = Auth::id();
		$soaltka->save();

		$text = 'membuat '.$this->title; //' baru '.$soaltka->what;
		$this->log($request, $text, ['soaltka.id' => $soaltka->id]);
		return redirect()->route('soaltka.index')->with('message_success', 'Soal Tka berhasil ditambahkan!');
	}

	public function show(Request $request, SoalTka $soaltka)
	{
		$soaltka->load(['guru', 'mapel', 'semester', 'tingkat']);
		$data['soaltka'] = $soaltka;

		$text = 'melihat detail '.$this->title;//.' '.$soaltka->what;
		$this->log($request, $text, ['soaltka.id' => $soaltka->id]);
		return view('SoalTka::soaltka_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, SoalTka $soaltka)
	{
		$data['soaltka'] = $soaltka;

		$active_semester_id = $soaltka->id_semester ?: (session('active_semester.id') ?? (session()->has('active_semester') ? get_semester('active_semester_id') : null) ?? Semester::first()?->id);
		$id_guru_aktif = $soaltka->id_guru ?: (session('id_guru') ?? (Auth::check() ? Guru::get_id_guru_by_id_user(Auth::id())->first()?->id_guru : null) ?? Guru::first()?->id);

		$ref_mapel = DB::table('jam_mengajar as jm')
			->join('mapel as m', 'jm.id_mapel', '=', 'm.id')
			->where('jm.id_guru', $id_guru_aktif)
			->where('jm.id_semester', $active_semester_id)
			->whereNull('jm.deleted_at')
			->whereNull('m.deleted_at')
			->distinct()
			->orderBy('m.mapel')
			->pluck('m.mapel', 'm.id');

		if (!empty($soaltka->id_mapel) && !$ref_mapel->has($soaltka->id_mapel)) {
			$currentMapel = Mapel::find($soaltka->id_mapel);
			if ($currentMapel) {
				$ref_mapel->put($currentMapel->id, $currentMapel->mapel);
			}
		}

		$ref_mapel->prepend('-PILIH SALAH SATU-', '');
		$ref_tingkat = Tingkat::all()->sortBy('tingkat')->pluck('tingkat', 'id');
		$ref_tingkat->prepend('-PILIH SALAH SATU-', '');
		
		$data['forms'] = array(
			'id_guru' => ['', Form::hidden("id_guru", $id_guru_aktif) ],
			'id_semester' => ['', Form::hidden("id_semester", $active_semester_id) ],
			'id_mapel' => ['Mapel', Form::select("id_mapel", $ref_mapel, $soaltka->id_mapel, ["class" => "form-control select2"]) ],
			'id_tingkat' => ['Tingkat', Form::select("id_tingkat", $ref_tingkat, $soaltka->id_tingkat, ["class" => "form-control select2"]) ],
			'file' => ['File', Form::file("file", ["class" => "form-control", "id" => "file"]) ],
		);

		$text = 'membuka form edit '.$this->title;//.' '.$soaltka->what;
		$this->log($request, $text, ['soaltka.id' => $soaltka->id]);
		return view('SoalTka::soaltka_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_guru' => 'required',
			'id_mapel' => 'required',
			'id_semester' => 'required',
			'id_tingkat' => 'required',
			'file' => 'nullable|file|max:20480',
		]);
		
		$soaltka = SoalTka::find($id);
		$soaltka->id_guru = $request->input("id_guru");
		$soaltka->id_mapel = $request->input("id_mapel");
		$soaltka->id_semester = $request->input("id_semester");
		$soaltka->id_tingkat = $request->input("id_tingkat");
		
		if ($request->hasFile('file')) {
			$destinationPath = public_path('uploads/soal_tka/');
			if (!file_exists($destinationPath)) {
				mkdir($destinationPath, 0777, true);
			}
			$uploadedFile = $request->file('file');
			$fileName = time() . '_' . Str::slug(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $uploadedFile->getClientOriginalExtension();
			$uploadedFile->move($destinationPath, $fileName);
			$soaltka->file = $fileName;
		}
		
		$soaltka->updated_by = Auth::id();
		$soaltka->save();

		$text = 'mengedit '.$this->title;//.' '.$soaltka->what;
		$this->log($request, $text, ['soaltka.id' => $soaltka->id]);
		return redirect()->route('soaltka.index')->with('message_success', 'Soal Tka berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$soaltka = SoalTka::find($id);
		$soaltka->deleted_by = Auth::id();
		$soaltka->save();
		$soaltka->delete();

		$text = 'menghapus '.$this->title;//.' '.$soaltka->what;
		$this->log($request, $text, ['soaltka.id' => $soaltka->id]);
		return back()->with('message_success', 'Soal Tka berhasil dihapus!');
	}

}
