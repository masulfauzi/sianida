<?php
namespace App\Modules\Snbp\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Snbp\Models\Snbp;
use App\Modules\Semester\Models\Semester;
use App\Modules\Siswa\Models\Siswa;

use App\Http\Controllers\Controller;
use App\Modules\Jurusan\Models\Jurusan;
use App\Modules\Nilai\Models\Nilai;
use App\Modules\Pesertadidik\Models\Pesertadidik;
use Illuminate\Support\Facades\Auth;
use Svg\Tag\Rect;

class SnbpController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Snbp";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$data['data'] = Jurusan::all();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Snbp::snbp', array_merge($data, ['title' => $this->title]));
	}

	public function index_jurusan(Request $request, Jurusan $jurusan)
	{
		$data['data'] = Snbp::join('pesertadidik as p', 'p.id_siswa', '=', 'snbp.id_siswa')
							->join('kelas as k', 'k.id','=', 'p.id_kelas')
							->where('k.id_jurusan', $jurusan->id)
							->where('snbp.id_semester', session('active_semester')['id'])
							->where('p.id_semester', session('active_semester')['id'])
							->orderBy('rata_rata')->get();
		$data['jurusan'] = $jurusan;

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Snbp::snbp_jurusan', array_merge($data, ['title' => $this->title]));
	}

	public function generate_jurusan(Request $request, Jurusan $jurusan)
	{
		// dd($jurusan->id);

		$id_semester = session('active_semester')['id'];

		$siswa = Siswa::select('siswa.id')
						->join('pesertadidik as b', 'siswa.id', '=', 'b.id_siswa')
						->join('kelas as c', 'b.id_kelas', '=', 'c.id')
						->join('tingkat as d', 'c.id_tingkat','=', 'd.id')
						->where('b.id_semester', $id_semester)
						->where('c.id_jurusan', $jurusan->id)
						->where('d.tingkat', 'XII')
						->get();
		
		// dd($siswa);

		$hapus = Snbp::whereIn('id_siswa',$siswa)->delete();

		// dd($hapus);

		foreach($siswa as $item)
		{
			$semester = Nilai::whereIdSiswa($item->id)->groupBy('id_semester')->get();

			$data_nilai = array();
			$counter = 1;
			foreach($semester as $data_semester)
			{
				$nilai = Nilai::whereIdSemester($data_semester->id_semester)->whereIdSiswa($item->id)->get();

				$total_nilai = $nilai->sum('nilai');

				$pembagi = count($nilai);

				$data_nilai[$counter] = $total_nilai/$pembagi;

				// dd($pembagi);
				$counter ++;
			}

			$data_nilai_collection = collect($data_nilai);

			// dd($data_nilai_collection->sum()/count($data_nilai_collection));

			$snbp = new Snbp();
			$snbp->id_semester = session('active_semester')['id'];
			$snbp->id_siswa = $item->id;
			$snbp->rata_rata = $data_nilai_collection->sum()/count($data_nilai_collection);
			$snbp->is_berminat = 1;
			
			$snbp->created_by = Auth::id();
			$snbp->save();

			// dd($snbp);
			
			
		}

		$text = 'membuat '.$this->title; //' baru '.$snbp->what;
		$this->log($request, $text, ['snbp.id' => $snbp->id]);
		return redirect()->back()->with('message_success', 'Snbp berhasil ditambahkan!');
	}

	public function create(Request $request)
	{
		$ref_semester = Semester::all()->pluck('semester','id');
		$ref_siswa = Siswa::all()->pluck('nama_siswa','id');
		
		$data['forms'] = array(
			'id_semester' => ['Semester', Form::select("id_semester", $ref_semester, null, ["class" => "form-control select2"]) ],
			'id_siswa' => ['Siswa', Form::select("id_siswa", $ref_siswa, null, ["class" => "form-control select2"]) ],
			'rata_rata' => ['Rata Rata', Form::text("rata_rata", old("rata_rata"), ["class" => "form-control","placeholder" => ""]) ],
			'is_berminat' => ['Is Berminat', Form::text("is_berminat", old("is_berminat"), ["class" => "form-control","placeholder" => ""]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Snbp::snbp_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_semester' => 'required',
			'id_siswa' => 'required',
			'rata_rata' => 'required',
			'is_berminat' => 'required',
			
		]);

		$snbp = new Snbp();
		$snbp->id_semester = $request->input("id_semester");
		$snbp->id_siswa = $request->input("id_siswa");
		$snbp->rata_rata = $request->input("rata_rata");
		$snbp->is_berminat = $request->input("is_berminat");
		
		$snbp->created_by = Auth::id();
		$snbp->save();

		$text = 'membuat '.$this->title; //' baru '.$snbp->what;
		$this->log($request, $text, ['snbp.id' => $snbp->id]);
		return redirect()->route('snbp.index')->with('message_success', 'Snbp berhasil ditambahkan!');
	}

	public function show(Request $request, Snbp $snbp)
	{
		$data['snbp'] = $snbp;

		$text = 'melihat detail '.$this->title;//.' '.$snbp->what;
		$this->log($request, $text, ['snbp.id' => $snbp->id]);
		return view('Snbp::snbp_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Snbp $snbp)
	{
		$data['snbp'] = $snbp;

		$ref_semester = Semester::all()->pluck('semester','id');
		$ref_siswa = Siswa::all()->pluck('nama_siswa','id');
		
		$data['forms'] = array(
			'id_semester' => ['Semester', Form::select("id_semester", $ref_semester, null, ["class" => "form-control select2"]) ],
			'id_siswa' => ['Siswa', Form::select("id_siswa", $ref_siswa, null, ["class" => "form-control select2"]) ],
			'rata_rata' => ['Rata Rata', Form::text("rata_rata", $snbp->rata_rata, ["class" => "form-control","placeholder" => "", "id" => "rata_rata"]) ],
			'is_berminat' => ['Is Berminat', Form::text("is_berminat", $snbp->is_berminat, ["class" => "form-control","placeholder" => "", "id" => "is_berminat"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$snbp->what;
		$this->log($request, $text, ['snbp.id' => $snbp->id]);
		return view('Snbp::snbp_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_semester' => 'required',
			'id_siswa' => 'required',
			'rata_rata' => 'required',
			'is_berminat' => 'required',
			
		]);
		
		$snbp = Snbp::find($id);
		$snbp->id_semester = $request->input("id_semester");
		$snbp->id_siswa = $request->input("id_siswa");
		$snbp->rata_rata = $request->input("rata_rata");
		$snbp->is_berminat = $request->input("is_berminat");
		
		$snbp->updated_by = Auth::id();
		$snbp->save();


		$text = 'mengedit '.$this->title;//.' '.$snbp->what;
		$this->log($request, $text, ['snbp.id' => $snbp->id]);
		return redirect()->route('snbp.index')->with('message_success', 'Snbp berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$snbp = Snbp::find($id);
		$snbp->deleted_by = Auth::id();
		$snbp->save();
		$snbp->delete();

		$text = 'menghapus '.$this->title;//.' '.$snbp->what;
		$this->log($request, $text, ['snbp.id' => $snbp->id]);
		return back()->with('message_success', 'Snbp berhasil dihapus!');
	}

}
