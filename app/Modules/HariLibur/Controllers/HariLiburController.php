<?php
namespace App\Modules\HariLibur\Controllers;

use Form;
use Carbon\Carbon;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\HariLibur\Models\HariLibur;
use App\Modules\Kelas\Models\Kelas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HariLiburController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Hari Libur";

	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = HariLibur::with('kelas');
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('HariLibur::harilibur', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{

		$data['forms'] = array(
			'ketarangan' => ['Ketarangan', Form::text("ketarangan", old("ketarangan"), ["class" => "form-control","placeholder" => ""]) ],
			'tanggal_mulai' => ['Tanggal Mulai', Form::text("tanggal_mulai", old("tanggal_mulai"), ["class" => "form-control datepicker"]) ],
			'tanggal_selesai' => ['Tanggal Selesai', Form::text("tanggal_selesai", old("tanggal_selesai"), ["class" => "form-control datepicker"]) ],

		);
		$data['ref_kelas']      = $this->formKelas();
		$data['berlaku']        = old('berlaku', 'semua');
		$data['kelas_terpilih'] = old('kelas', []);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('HariLibur::harilibur_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, array_merge([
			'ketarangan' => 'required',
			'tanggal_mulai' => 'required',
			'tanggal_selesai' => 'required|after_or_equal:tanggal_mulai',

		], $this->rulesKelas()));

		$ketarangan = $request->input("ketarangan");
		$tanggal = Carbon::parse($request->input("tanggal_mulai"));
		$tanggalSelesai = Carbon::parse($request->input("tanggal_selesai"));
		$kelasOff = $this->resolveKelasOff($request);

		$lastId = null;
		DB::transaction(function () use ($ketarangan, $tanggal, $tanggalSelesai, $kelasOff, &$lastId) {
			while ($tanggal->lte($tanggalSelesai)) {
				$harilibur = new HariLibur();
				$harilibur->ketarangan = $ketarangan;
				$harilibur->tanggal = $tanggal->toDateString();
				$harilibur->created_by = Auth::id();
				$harilibur->save();
				$harilibur->kelas()->sync($kelasOff);

				$lastId = $harilibur->id;
				$tanggal->addDay();
			}
		});

		$text = 'membuat '.$this->title; //' baru '.$harilibur->what;
		$this->log($request, $text, ['harilibur.id' => $lastId]);
		return redirect()->route('harilibur.index')->with('message_success', 'Hari Libur berhasil ditambahkan!');
	}

	public function show(Request $request, HariLibur $harilibur)
	{
		$data['harilibur'] = $harilibur->load('kelas');

		$text = 'melihat detail '.$this->title;//.' '.$harilibur->what;
		$this->log($request, $text, ['harilibur.id' => $harilibur->id]);
		return view('HariLibur::harilibur_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, HariLibur $harilibur)
	{
		$data['harilibur'] = $harilibur;


		$data['forms'] = array(
			'ketarangan' => ['Ketarangan', Form::text("ketarangan", $harilibur->ketarangan, ["class" => "form-control","placeholder" => "", "id" => "ketarangan"]) ],
			'tanggal' => ['Tanggal', Form::text("tanggal", $harilibur->tanggal, ["class" => "form-control datepicker", "id" => "tanggal"]) ],

		);
		$kelasOff = $harilibur->kelas()->pluck('kelas.id')->all();
		$data['ref_kelas']      = $this->formKelas();
		$data['berlaku']        = old('berlaku', count($kelasOff) ? 'off' : 'semua');
		$data['kelas_terpilih'] = old('kelas', $kelasOff);

		$text = 'membuka form edit '.$this->title;//.' '.$harilibur->what;
		$this->log($request, $text, ['harilibur.id' => $harilibur->id]);
		return view('HariLibur::harilibur_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, array_merge([
			'ketarangan' => 'required',
			'tanggal' => 'required',

		], $this->rulesKelas()));

		$harilibur = HariLibur::find($id);
		$harilibur->ketarangan = $request->input("ketarangan");
		$harilibur->tanggal = $request->input("tanggal");

		$harilibur->updated_by = Auth::id();
		$kelasOff = $this->resolveKelasOff($request);
		DB::transaction(function () use ($harilibur, $kelasOff) {
			$harilibur->save();
			$harilibur->kelas()->sync($kelasOff);
		});


		$text = 'mengedit '.$this->title;//.' '.$harilibur->what;
		$this->log($request, $text, ['harilibur.id' => $harilibur->id]);
		return redirect()->route('harilibur.index')->with('message_success', 'Hari Libur berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$harilibur = HariLibur::find($id);
		$harilibur->deleted_by = Auth::id();
		$harilibur->save();
		$harilibur->delete();

		$text = 'menghapus '.$this->title;//.' '.$harilibur->what;
		$this->log($request, $text, ['harilibur.id' => $harilibur->id]);
		return back()->with('message_success', 'Hari Libur berhasil dihapus!');
	}

	private function formKelas()
	{
		return Kelas::with(['tingkat', 'jurusan'])->orderBy('kelas')->get();
	}

	private function rulesKelas()
	{
		return [
			'berlaku' => 'required|in:semua,off,masuk',
			'kelas'   => 'exclude_if:berlaku,semua|required|array|min:1',
			'kelas.*' => 'exclude_if:berlaku,semua|exists:kelas,id',
		];
	}

	/**
	 * Daftar id kelas yang OFF. Array kosong berarti libur untuk semua kelas.
	 */
	private function resolveKelasOff(Request $request)
	{
		$berlaku = $request->input('berlaku');
		$dipilih = (array) $request->input('kelas', []);

		if ($berlaku === 'off') {
			return array_values(array_unique($dipilih));
		}

		if ($berlaku === 'masuk') {
			return Kelas::whereNotIn('id', $dipilih)->pluck('id')->all();
		}

		return [];
	}

}
