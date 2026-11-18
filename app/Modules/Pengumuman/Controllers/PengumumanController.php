<?php
namespace App\Modules\Pengumuman\Controllers;

use App\Helpers\Logger;
use App\Http\Controllers\Controller;
use App\Modules\Log\Models\Log;
use App\Modules\Pengumuman\Models\Pengumuman;
use App\Services\FcmService;
use Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    use Logger;
    protected $log;
    protected $title = 'Pengumuman';

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    public function index(Request $request)
    {
        $query = Pengumuman::query()->latest();
        if ($search = trim((string) $request->get('search'))) {
            $query->where(fn ($q) => $q->where('judul', 'like', "%{$search}%")->orWhere('isi', 'like', "%{$search}%"));
        }
        $data['data'] = $query->paginate(10)->withQueryString();

        $this->log($request, 'melihat halaman manajemen data ' . $this->title);
        return view('Pengumuman::pengumuman', array_merge($data, ['title' => $this->title]));
    }

    public function create(Request $request)
    {
        $data['forms'] = $this->forms();

        $this->log($request, 'membuka form tambah ' . $this->title);
        return view('Pengumuman::pengumuman_create', array_merge($data, ['title' => $this->title]));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules(), $this->pesanValidasi());

        $pengumuman             = new Pengumuman();
        $pengumuman->judul      = trim($request->input('judul'));
        $pengumuman->isi        = trim($request->input('isi'));
        $pengumuman->created_by = Auth::id();
        $pengumuman->save();

        $terkirim = $this->kirimKeSiswa($pengumuman);

        $this->log($request, 'membuat ' . $this->title, ['pengumuman.id' => $pengumuman->id, 'terkirim' => $terkirim]);
        return redirect()->route('pengumuman.index')->with(
            $terkirim ? 'message_success' : 'message_danger',
            $terkirim ? 'Pengumuman tersimpan & terkirim ke siswa.' : 'Pengumuman tersimpan, tetapi GAGAL dikirim. Cek log.'
        );
    }

    public function show(Request $request, Pengumuman $pengumuman)
    {
        $data['pengumuman'] = $pengumuman;

        $this->log($request, 'melihat detail ' . $this->title, ['pengumuman.id' => $pengumuman->id]);
        return view('Pengumuman::pengumuman_detail', array_merge($data, ['title' => $this->title]));
    }

    public function edit(Request $request, Pengumuman $pengumuman)
    {
        $data['pengumuman'] = $pengumuman;
        $data['forms']      = $this->forms($pengumuman);

        $this->log($request, 'membuka form edit ' . $this->title, ['pengumuman.id' => $pengumuman->id]);
        return view('Pengumuman::pengumuman_update', array_merge($data, ['title' => $this->title]));
    }

    /** Edit TIDAK mengirim ulang notifikasi, supaya koreksi typo tidak membuat notifikasi dobel. */
    public function update(Request $request, Pengumuman $pengumuman)
    {
        $this->validate($request, $this->rules(), $this->pesanValidasi());

        $pengumuman->judul      = trim($request->input('judul'));
        $pengumuman->isi        = trim($request->input('isi'));
        $pengumuman->updated_by = Auth::id();
        $pengumuman->save();

        $this->log($request, 'mengedit ' . $this->title, ['pengumuman.id' => $pengumuman->id]);
        return redirect()->route('pengumuman.index')->with('message_success', 'Pengumuman berhasil diubah (notifikasi tidak dikirim ulang).');
    }

    /** Kirim ulang notifikasi pengumuman ke semua siswa (mis. setelah gagal terkirim). */
    public function kirim(Request $request, Pengumuman $pengumuman)
    {
        $terkirim = $this->kirimKeSiswa($pengumuman);

        $this->log($request, 'mengirim ulang ' . $this->title, ['pengumuman.id' => $pengumuman->id, 'terkirim' => $terkirim]);
        return redirect()->route('pengumuman.show', $pengumuman->id)->with(
            $terkirim ? 'message_success' : 'message_danger',
            $terkirim ? 'Pengumuman terkirim ke siswa.' : 'Pengumuman GAGAL dikirim. Cek log.'
        );
    }

    public function destroy(Request $request, Pengumuman $pengumuman)
    {
        $pengumuman->deleted_by = Auth::id();
        $pengumuman->save();
        $pengumuman->delete();

        $this->log($request, 'menghapus ' . $this->title, ['pengumuman.id' => $pengumuman->id]);
        return back()->with('message_success', 'Pengumuman berhasil dihapus!');
    }

    /**
     * Kirim ke topic "siswa" (channel "pengumuman"), sesuai kontrak aplikasi siswa.
     * Isi di data dipotong karena payload FCM maksimal 4 KB.
     */
    private function kirimKeSiswa(Pengumuman $pengumuman): bool
    {
        $isiPolos = trim(html_entity_decode(strip_tags($pengumuman->isi)));

        $terkirim = FcmService::sendToTopic(
            'siswa',
            Str::limit($pengumuman->judul, 60),
            Str::limit($isiPolos, 100),
            [
                'type'  => 'pengumuman',
                'judul' => $pengumuman->judul,
                'isi'   => Str::limit($isiPolos, 2500),
            ],
            'pengumuman'
        );

        if ($terkirim) {
            $pengumuman->dikirim_at = now();
            $pengumuman->save();
        }

        return $terkirim;
    }

    private function forms(?Pengumuman $pengumuman = null): array
    {
        return [
            'judul' => ['Judul', Form::text('judul', old('judul', $pengumuman->judul ?? null), ['class' => 'form-control', 'maxlength' => 150, 'required'])],
            'isi'   => ['Isi', Form::textarea('isi', old('isi', $pengumuman->isi ?? null), ['class' => 'form-control', 'rows' => 8, 'required'])],
        ];
    }

    private function rules(): array
    {
        return [
            'judul' => 'required|string|max:150',
            'isi'   => 'required|string|max:10000',
        ];
    }

    private function pesanValidasi(): array
    {
        return [
            'judul.required' => 'Judul wajib diisi.',
            'judul.max'      => 'Judul maksimal 150 karakter.',
            'isi.required'   => 'Isi pengumuman wajib diisi.',
            'isi.max'        => 'Isi pengumuman maksimal 10.000 karakter.',
        ];
    }
}
