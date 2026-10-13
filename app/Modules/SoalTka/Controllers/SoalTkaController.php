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
