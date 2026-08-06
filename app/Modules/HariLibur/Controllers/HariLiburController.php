<?php
namespace App\Modules\HariLibur\Controllers;

use Form;
use Carbon\Carbon;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\HariLibur\Models\HariLibur;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
		$query = HariLibur::query();
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

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('HariLibur::harilibur_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'ketarangan' => 'required',
			'tanggal_mulai' => 'required',
			'tanggal_selesai' => 'required|after_or_equal:tanggal_mulai',

		]);

		$ketarangan = $request->input("ketarangan");
		$tanggal = Carbon::parse($request->input("tanggal_mulai"));
		$tanggalSelesai = Carbon::parse($request->input("tanggal_selesai"));

		$lastId = null;
		while ($tanggal->lte($tanggalSelesai)) {
			$harilibur = new HariLibur();
			$harilibur->ketarangan = $ketarangan;
			$harilibur->tanggal = $tanggal->toDateString();
			$harilibur->created_by = Auth::id();
			$harilibur->save();

			$lastId = $harilibur->id;
			$tanggal->addDay();
		}

		$text = 'membuat '.$this->title; //' baru '.$harilibur->what;
		$this->log($request, $text, ['harilibur.id' => $lastId]);
		return redirect()->route('harilibur.index')->with('message_success', 'Hari Libur berhasil ditambahkan!');
	}

	public function show(Request $request, HariLibur $harilibur)
	{
		$data['harilibur'] = $harilibur;

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

		$text = 'membuka form edit '.$this->title;//.' '.$harilibur->what;
		$this->log($request, $text, ['harilibur.id' => $harilibur->id]);
		return view('HariLibur::harilibur_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'ketarangan' => 'required',
			'tanggal' => 'required',
			
		]);
		
		$harilibur = HariLibur::find($id);
		$harilibur->ketarangan = $request->input("ketarangan");
		$harilibur->tanggal = $request->input("tanggal");
		
		$harilibur->updated_by = Auth::id();
		$harilibur->save();


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

}
