<?php
namespace App\Modules\Magang\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Magang\Models\Magang;
use App\Modules\PeriodeMagang\Models\PeriodeMagang;
use App\Modules\Pesertadidik\Models\Pesertadidik;
use App\Modules\Dudi\Models\Dudi;
use App\Modules\Guru\Models\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MagangController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Magang";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = Magang::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Magang::magang', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$ref_pesertadidik = Pesertadidik::join('siswa', 'pesertadidik.id_siswa', '=', 'siswa.id')
								->join('kelas', 'pesertadidik.id_kelas', '=', 'kelas.id')
								->where('id_semester', get_semester('active_semester_id'))
								->selectRaw("pesertadidik.id, CONCAT(siswa.nama_siswa, ' - ', kelas.kelas) as nama_lengkap")
								->pluck('nama_lengkap', 'id');
		$ref_dudi = Dudi::all()->pluck('nama_dudi','id');
		$ref_guru = Guru::all()->pluck('nama','id');

		$ref_dudi->prepend('-PILIH SALAH SATU-', '');
		$ref_guru->prepend('-PILIH SALAH SATU-', '');
		$ref_pesertadidik->prepend('-PILIH MURID-', '');

		$data['forms'] = array(
			'id_dudi' => ['Dudi', Form::select("id_dudi", $ref_dudi, null, ["class" => "form-control select2"]) ],
			'id_pesertadidik' => ['Pesertadidik', Form::select("id_pesertadidik[]", $ref_pesertadidik, null, ["class" => "form-control select2", "multiple" => "multiple"]) ],
			'id_pembimbing' => ['Pembimbing', Form::select("id_pembimbing", $ref_guru, null, ["class" => "form-control select2"]) ],
			'id_periode_magang' => ['', Form::hidden("id_periode_magang", $request->input('id_periode_magang')) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Magang::magang_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_periode_magang' => 'required',
			'id_pesertadidik' => 'required|array',
			'id_pesertadidik.*' => 'required|exists:pesertadidik,id',
			'id_dudi' => 'required',
			'id_pembimbing' => 'required',

		]);

		foreach ($request->input('id_pesertadidik') as $id_pesertadidik) {
			$magang = new Magang();
			$magang->id_periode_magang = $request->input("id_periode_magang");
			$magang->id_pesertadidik = $id_pesertadidik;
			$magang->id_dudi = $request->input("id_dudi");
			$magang->id_pembimbing = $request->input("id_pembimbing");

			$magang->created_by = Auth::id();
			$magang->save();

			$text = 'membuat '.$this->title; //' baru '.$magang->what;
			$this->log($request, $text, ['magang.id' => $magang->id]);
		}

		return redirect()->route('periodemagang.show', $request->input("id_periode_magang"))->with('message_success', 'Magang berhasil ditambahkan!');
	}

	public function show(Request $request, Magang $magang)
	{
		$data['magang'] = $magang;

		$text = 'melihat detail '.$this->title;//.' '.$magang->what;
		$this->log($request, $text, ['magang.id' => $magang->id]);
		return view('Magang::magang_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Magang $magang)
	{
		$data['magang'] = $magang;

		$ref_periode_magang = PeriodeMagang::all()->pluck('id_semester','id');
		$ref_pesertadidik = Pesertadidik::all()->pluck('id_semester','id');
		$ref_dudi = Dudi::all()->pluck('nama_dudi','id');
		$ref_guru = Guru::all()->pluck('nama','id');
		
		$data['forms'] = array(
			'id_periode_magang' => ['Periode Magang', Form::select("id_periode_magang", $ref_periode_magang, null, ["class" => "form-control select2"]) ],
			'id_pesertadidik' => ['Pesertadidik', Form::select("id_pesertadidik", $ref_pesertadidik, null, ["class" => "form-control select2"]) ],
			'id_dudi' => ['Dudi', Form::select("id_dudi", $ref_dudi, null, ["class" => "form-control select2"]) ],
			'id_pembimbing' => ['Pembimbing', Form::select("id_pembimbing", $ref_guru, null, ["class" => "form-control select2"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$magang->what;
		$this->log($request, $text, ['magang.id' => $magang->id]);
		return view('Magang::magang_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_periode_magang' => 'required',
			'id_pesertadidik' => 'required',
			'id_dudi' => 'required',
			'id_pembimbing' => 'required',
			
		]);
		
		$magang = Magang::find($id);
		$magang->id_periode_magang = $request->input("id_periode_magang");
		$magang->id_pesertadidik = $request->input("id_pesertadidik");
		$magang->id_dudi = $request->input("id_dudi");
		$magang->id_pembimbing = $request->input("id_pembimbing");
		
		$magang->updated_by = Auth::id();
		$magang->save();


		$text = 'mengedit '.$this->title;//.' '.$magang->what;
		$this->log($request, $text, ['magang.id' => $magang->id]);
		return redirect()->route('magang.index')->with('message_success', 'Magang berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$magang = Magang::find($id);
		$magang->deleted_by = Auth::id();
		$magang->save();
		$magang->delete();

		$text = 'menghapus '.$this->title;//.' '.$magang->what;
		$this->log($request, $text, ['magang.id' => $magang->id]);
		return back()->with('message_success', 'Magang berhasil dihapus!');
	}

}
