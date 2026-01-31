<?php
namespace App\Modules\StatusIjin\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\StatusIjin\Models\StatusIjin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StatusIjinController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Status Ijin";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = StatusIjin::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('StatusIjin::statusijin', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		
		$data['forms'] = array(
			'status_ijin' => ['Status Ijin', Form::text("status_ijin", old("status_ijin"), ["class" => "form-control","placeholder" => ""]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('StatusIjin::statusijin_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'status_ijin' => 'required',
			
		]);

		$statusijin = new StatusIjin();
		$statusijin->status_ijin = $request->input("status_ijin");
		
		$statusijin->created_by = Auth::id();
		$statusijin->save();

		$text = 'membuat '.$this->title; //' baru '.$statusijin->what;
		$this->log($request, $text, ['statusijin.id' => $statusijin->id]);
		return redirect()->route('statusijin.index')->with('message_success', 'Status Ijin berhasil ditambahkan!');
	}

	public function show(Request $request, StatusIjin $statusijin)
	{
		$data['statusijin'] = $statusijin;

		$text = 'melihat detail '.$this->title;//.' '.$statusijin->what;
		$this->log($request, $text, ['statusijin.id' => $statusijin->id]);
		return view('StatusIjin::statusijin_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, StatusIjin $statusijin)
	{
		$data['statusijin'] = $statusijin;

		
		$data['forms'] = array(
			'status_ijin' => ['Status Ijin', Form::text("status_ijin", $statusijin->status_ijin, ["class" => "form-control","placeholder" => "", "id" => "status_ijin"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$statusijin->what;
		$this->log($request, $text, ['statusijin.id' => $statusijin->id]);
		return view('StatusIjin::statusijin_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'status_ijin' => 'required',
			
		]);
		
		$statusijin = StatusIjin::find($id);
		$statusijin->status_ijin = $request->input("status_ijin");
		
		$statusijin->updated_by = Auth::id();
		$statusijin->save();


		$text = 'mengedit '.$this->title;//.' '.$statusijin->what;
		$this->log($request, $text, ['statusijin.id' => $statusijin->id]);
		return redirect()->route('statusijin.index')->with('message_success', 'Status Ijin berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$statusijin = StatusIjin::find($id);
		$statusijin->deleted_by = Auth::id();
		$statusijin->save();
		$statusijin->delete();

		$text = 'menghapus '.$this->title;//.' '.$statusijin->what;
		$this->log($request, $text, ['statusijin.id' => $statusijin->id]);
		return back()->with('message_success', 'Status Ijin berhasil dihapus!');
	}

}
