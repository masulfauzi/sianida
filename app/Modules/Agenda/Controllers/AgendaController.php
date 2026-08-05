<?php
namespace App\Modules\Agenda\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Agenda\Models\Agenda;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Agenda";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = Agenda::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Agenda::agenda', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		
		$data['forms'] = array(
			'nama_kegiatan' => ['Nama Kegiatan', Form::text("nama_kegiatan", old("nama_kegiatan"), ["class" => "form-control","placeholder" => ""]) ],
			'tgl_mulai' => ['Tgl Mulai', Form::text("tgl_mulai", old("tgl_mulai"), ["class" => "form-control datepicker"]) ],
			'tgl_selesai' => ['Tgl Selesai', Form::text("tgl_selesai", old("tgl_selesai"), ["class" => "form-control datepicker"]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Agenda::agenda_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'nama_kegiatan' => 'required',
			'tgl_mulai' => 'required',
			'tgl_selesai' => 'required',
			
		]);

		$agenda = new Agenda();
		$agenda->nama_kegiatan = $request->input("nama_kegiatan");
		$agenda->tgl_mulai = $request->input("tgl_mulai");
		$agenda->tgl_selesai = $request->input("tgl_selesai");
		
		$agenda->created_by = Auth::id();
		$agenda->save();

		$text = 'membuat '.$this->title; //' baru '.$agenda->what;
		$this->log($request, $text, ['agenda.id' => $agenda->id]);
		return redirect()->route('agenda.index')->with('message_success', 'Agenda berhasil ditambahkan!');
	}

	public function show(Request $request, Agenda $agenda)
	{
		$data['agenda'] = $agenda;

		$text = 'melihat detail '.$this->title;//.' '.$agenda->what;
		$this->log($request, $text, ['agenda.id' => $agenda->id]);
		return view('Agenda::agenda_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Agenda $agenda)
	{
		$data['agenda'] = $agenda;

		
		$data['forms'] = array(
			'nama_kegiatan' => ['Nama Kegiatan', Form::text("nama_kegiatan", $agenda->nama_kegiatan, ["class" => "form-control","placeholder" => "", "id" => "nama_kegiatan"]) ],
			'tgl_mulai' => ['Tgl Mulai', Form::text("tgl_mulai", $agenda->tgl_mulai, ["class" => "form-control datepicker", "id" => "tgl_mulai"]) ],
			'tgl_selesai' => ['Tgl Selesai', Form::text("tgl_selesai", $agenda->tgl_selesai, ["class" => "form-control datepicker", "id" => "tgl_selesai"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$agenda->what;
		$this->log($request, $text, ['agenda.id' => $agenda->id]);
		return view('Agenda::agenda_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'nama_kegiatan' => 'required',
			'tgl_mulai' => 'required',
			'tgl_selesai' => 'required',
			
		]);
		
		$agenda = Agenda::find($id);
		$agenda->nama_kegiatan = $request->input("nama_kegiatan");
		$agenda->tgl_mulai = $request->input("tgl_mulai");
		$agenda->tgl_selesai = $request->input("tgl_selesai");
		
		$agenda->updated_by = Auth::id();
		$agenda->save();


		$text = 'mengedit '.$this->title;//.' '.$agenda->what;
		$this->log($request, $text, ['agenda.id' => $agenda->id]);
		return redirect()->route('agenda.index')->with('message_success', 'Agenda berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$agenda = Agenda::find($id);
		$agenda->deleted_by = Auth::id();
		$agenda->save();
		$agenda->delete();

		$text = 'menghapus '.$this->title;//.' '.$agenda->what;
		$this->log($request, $text, ['agenda.id' => $agenda->id]);
		return back()->with('message_success', 'Agenda berhasil dihapus!');
	}

}
