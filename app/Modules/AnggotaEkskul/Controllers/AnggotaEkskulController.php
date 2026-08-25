<?php
namespace App\Modules\AnggotaEkskul\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\AnggotaEkskul\Models\AnggotaEkskul;
use App\Modules\Pesertadidik\Models\Pesertadidik;
use App\Modules\Ekskul\Models\Ekskul;
use App\Modules\Semester\Models\Semester;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AnggotaEkskulController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Anggota Ekskul";

	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = AnggotaEkskul::query()->where('id_ekskul', '=', $request->get('id_ekskul'));
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}

        $id_ekskul = $request->get('id_ekskul');

		$data['data'] = $query->paginate(10)->withQueryString();
        $data['ekskul'] = Ekskul::find($id_ekskul);

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('AnggotaEkskul::anggotaekskul', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$ref_pesertadidik = Pesertadidik::join('siswa', 'pesertadidik.id_siswa','=','siswa.id')
        ->select('pesertadidik.id', 'siswa.nama_siswa as nama_siswa')
        ->where('pesertadidik.id_semester', '=', get_semester('active_semester_id'))
        ->get()
        ->pluck('nama_siswa','id');

		$ref_ekskul = Ekskul::all()->pluck('nama','id');
		$ref_semester = Semester::all()->pluck('semester','id');

		$data['forms'] = array(
			'id_pd' => ['Pd', Form::select("id_pd", $ref_pesertadidik, null, ["class" => "form-control select2"]) ],
			'id_ekskul' => ['', Form::hidden("id_ekskul", $request->get('id_ekskul')) ],
			// 'nilai' => ['Nilai', Form::text("nilai", old("nilai"), ["class" => "form-control","placeholder" => ""]) ],
			'id_semester' => ['', Form::hidden("id_semester", get_semester('active_semester_id')) ],

		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('AnggotaEkskul::anggotaekskul_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_pd' => 'required',
			'id_ekskul' => 'required',
			'id_semester' => 'required',

		]);

		$anggotaekskul = new AnggotaEkskul();
		$anggotaekskul->id_pd = $request->input("id_pd");
		$anggotaekskul->id_ekskul = $request->input("id_ekskul");
		$anggotaekskul->nilai = $request->input("nilai");
		$anggotaekskul->id_semester = $request->input("id_semester");

		$anggotaekskul->created_by = Auth::id();
		$anggotaekskul->save();

		$text = 'membuat '.$this->title; //' baru '.$anggotaekskul->what;
		$this->log($request, $text, ['anggotaekskul.id' => $anggotaekskul->id]);
		return redirect()->route('anggotaekskul.index', ['id_ekskul' => $request->get('id_ekskul')])->with('message_success', 'Anggota Ekskul berhasil ditambahkan!');
	}

	public function show(Request $request, AnggotaEkskul $anggotaekskul)
	{
		$data['anggotaekskul'] = $anggotaekskul;

		$text = 'melihat detail '.$this->title;//.' '.$anggotaekskul->what;
		$this->log($request, $text, ['anggotaekskul.id' => $anggotaekskul->id]);
		return view('AnggotaEkskul::anggotaekskul_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, AnggotaEkskul $anggotaekskul)
	{
		$data['anggotaekskul'] = $anggotaekskul;

		$ref_pesertadidik = Pesertadidik::all()->pluck('id_semester','id');
		$ref_ekskul = Ekskul::all()->pluck('nama','id');
		$ref_semester = Semester::all()->pluck('semester','id');

		$data['forms'] = array(
			'id_pd' => ['Pd', Form::select("id_pd", $ref_pesertadidik, null, ["class" => "form-control select2"]) ],
			'id_ekskul' => ['Ekskul', Form::select("id_ekskul", $ref_ekskul, null, ["class" => "form-control select2"]) ],
			'nilai' => ['Nilai', Form::text("nilai", $anggotaekskul->nilai, ["class" => "form-control","placeholder" => "", "id" => "nilai"]) ],
			'id_semester' => ['Semester', Form::select("id_semester", $ref_semester, null, ["class" => "form-control select2"]) ],

		);

		$text = 'membuka form edit '.$this->title;//.' '.$anggotaekskul->what;
		$this->log($request, $text, ['anggotaekskul.id' => $anggotaekskul->id]);
		return view('AnggotaEkskul::anggotaekskul_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_pd' => 'required',
			'id_ekskul' => 'required',
			'nilai' => 'required',
			'id_semester' => 'required',

		]);

		$anggotaekskul = AnggotaEkskul::find($id);
		$anggotaekskul->id_pd = $request->input("id_pd");
		$anggotaekskul->id_ekskul = $request->input("id_ekskul");
		$anggotaekskul->nilai = $request->input("nilai");
		$anggotaekskul->id_semester = $request->input("id_semester");

		$anggotaekskul->updated_by = Auth::id();
		$anggotaekskul->save();


		$text = 'mengedit '.$this->title;//.' '.$anggotaekskul->what;
		$this->log($request, $text, ['anggotaekskul.id' => $anggotaekskul->id]);
		return redirect()->route('anggotaekskul.index')->with('message_success', 'Anggota Ekskul berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$anggotaekskul = AnggotaEkskul::find($id);
		$anggotaekskul->deleted_by = Auth::id();
		$anggotaekskul->save();
		$anggotaekskul->delete();

		$text = 'menghapus '.$this->title;//.' '.$anggotaekskul->what;
		$this->log($request, $text, ['anggotaekskul.id' => $anggotaekskul->id]);
		return back()->with('message_success', 'Anggota Ekskul berhasil dihapus!');
	}

}
