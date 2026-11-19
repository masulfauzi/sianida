<?php
namespace App\Modules\UjianSoal\Controllers;

use App\Helpers\KunciSoal;
use App\Helpers\Logger;
use App\Http\Controllers\Controller;
use App\Modules\Log\Models\Log;
use App\Modules\UjianSoal\Models\UjianBankSoal;
use App\Modules\UjianSoal\Models\UjianSoal;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class UjianSoalController extends Controller
{
    use Logger;
    protected $log;
    protected $title = "Soal";

    private const KOLOM_GAMBAR = ['gambar_soal', 'gambar_a', 'gambar_b', 'gambar_c', 'gambar_d', 'gambar_e'];

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    private function authorizeOwner(UjianBankSoal $bankSoal)
    {
        abort_unless($bankSoal->id_guru === session('id_guru'), 403, 'Anda tidak berhak mengakses bank soal ini.');
    }

    /**
     * Soal baru hanya boleh diinput setelah kisi-kisi bank soal diunggah.
     * Mengembalikan redirect ke form edit bank soal bila belum ada, atau null bila sudah.
     */
    private function pastikanKisiKisi(UjianBankSoal $bankSoal)
    {
        if ($bankSoal->file_kisi_kisi) {
            return null;
        }

        return redirect()->route('ujiansoal.edit', $bankSoal->id)
            ->with('message_danger', 'Unggah kisi-kisi terlebih dahulu sebelum menginput soal.');
    }

    private function authorizeSoal(UjianBankSoal $bankSoal, UjianSoal $soal)
    {
        $this->authorizeOwner($bankSoal);
        abort_unless($soal->id_bank_soal === $bankSoal->id, 404);
    }

    public function index(Request $request, UjianBankSoal $bankSoal)
    {
        $this->authorizeOwner($bankSoal);

        $data['bankSoal'] = $bankSoal;
        $data['data']     = $bankSoal->soal()->get();

        $this->log($request, 'melihat daftar soal', ['ujian_bank_soal.id' => $bankSoal->id]);
        return view('UjianSoal::ujian_soal', array_merge($data, ['title' => $this->title]));
    }

    /**
     * Pratinjau soal dengan tampilan yang meniru halaman pengerjaan siswa di aplikasi ujian.
     */
    public function preview(Request $request, UjianBankSoal $bankSoal)
    {
        $this->authorizeOwner($bankSoal);

        $this->log($request, 'melihat pratinjau soal', ['ujian_bank_soal.id' => $bankSoal->id]);
        return $this->renderPreview($request, $bankSoal, [
            'label'       => 'Mode Pratinjau Guru',
            'url_kembali' => route('ujiansoal.soal.index', $bankSoal->id),
            'teks_kembali'=> 'Kembali ke Daftar Soal',
            'tampil_guru' => false,
        ]);
    }

    /**
     * Pratinjau untuk admin dari halaman monitoring. Tidak ada cek pemilik: aksesnya sudah dibatasi
     * oleh privilege menu "ujiansoalmonitoring" lewat nama route (lihat routes.php).
     */
    public function previewMonitoring(Request $request, UjianBankSoal $bankSoal)
    {
        $this->log($request, 'melihat pratinjau soal dari monitoring', ['ujian_bank_soal.id' => $bankSoal->id]);
        return $this->renderPreview($request, $bankSoal, [
            'label'       => 'Mode Pratinjau Admin',
            'url_kembali' => route('ujiansoal.monitoring.index'),
            'teks_kembali'=> 'Kembali ke Monitoring',
            'tampil_guru' => true,
        ]);
    }

    private function renderPreview(Request $request, UjianBankSoal $bankSoal, array $mode)
    {
        $data['bankSoal'] = $bankSoal->load(['mapel', 'guru']);
        $data['data']     = $bankSoal->soal()->get();
        $data['mode']     = $mode;

        $no = (int) $request->query('no');
        $data['startIndex'] = max(0, (int) $data['data']->search(fn ($s) => (int) $s->no_soal === $no));

        return view('UjianSoal::ujian_soal_preview', array_merge($data, ['title' => $this->title]));
    }

    public function create(Request $request, UjianBankSoal $bankSoal)
    {
        $this->authorizeOwner($bankSoal);
        if ($redirect = $this->pastikanKisiKisi($bankSoal)) {
            return $redirect;
        }

        $data['bankSoal']   = $bankSoal;
        $data['soal']       = null;
        $data['nextNoSoal'] = ((int) $bankSoal->soal()->max('no_soal')) + 1;

        $this->log($request, 'membuka form tambah soal', ['ujian_bank_soal.id' => $bankSoal->id]);
        return view('UjianSoal::ujian_soal_create', array_merge($data, ['title' => $this->title]));
    }

    public function store(Request $request, UjianBankSoal $bankSoal)
    {
        $this->authorizeOwner($bankSoal);
        if ($redirect = $this->pastikanKisiKisi($bankSoal)) {
            return $redirect;
        }

        $data = $this->validateSoal($request, $bankSoal);

        $soal                = new UjianSoal();
        $soal->id_bank_soal   = $bankSoal->id;
        $soal->no_soal        = $data['no_soal'];
        $soal->jenis          = $data['jenis'];
        $soal->soal           = $data['soal'] ?? '';
        $soal->opsi_a         = $data['opsi_a'];
        $soal->opsi_b         = $data['opsi_b'];
        $soal->opsi_c         = $data['opsi_c'];
        $soal->opsi_d         = $data['opsi_d'];
        $soal->opsi_e         = $data['opsi_e'];
        $soal->kunci          = $data['kunci'];

        $this->simpanGambar($request, $soal);
        $this->kosongkanGambarJikaBs($soal);

        $soal->created_by = Auth::id();
        $soal->save();

        $this->log($request, 'menambah soal', ['ujian_soal.id' => $soal->id]);
        return redirect()->route('ujiansoal.soal.index', $bankSoal->id)->with('message_success', 'Soal berhasil ditambahkan!');
    }

    public function edit(Request $request, UjianBankSoal $bankSoal, UjianSoal $soal)
    {
        $this->authorizeSoal($bankSoal, $soal);

        $data['bankSoal'] = $bankSoal;
        $data['soal']     = $soal;

        $this->log($request, 'membuka form edit soal', ['ujian_soal.id' => $soal->id]);
        return view('UjianSoal::ujian_soal_update', array_merge($data, ['title' => $this->title]));
    }

    public function update(Request $request, UjianBankSoal $bankSoal, UjianSoal $soal)
    {
        $this->authorizeSoal($bankSoal, $soal);

        $data = $this->validateSoal($request, $bankSoal, $soal);

        $soal->no_soal = $data['no_soal'];
        $soal->jenis   = $data['jenis'];
        $soal->soal    = $data['soal'] ?? '';
        $soal->opsi_a  = $data['opsi_a'];
        $soal->opsi_b  = $data['opsi_b'];
        $soal->opsi_c  = $data['opsi_c'];
        $soal->opsi_d  = $data['opsi_d'];
        $soal->opsi_e  = $data['opsi_e'];
        $soal->kunci   = $data['kunci'];

        $this->simpanGambar($request, $soal);
        $this->kosongkanGambarJikaBs($soal);

        $soal->updated_by = Auth::id();
        $soal->save();

        $this->log($request, 'mengedit soal', ['ujian_soal.id' => $soal->id]);
        return redirect()->route('ujiansoal.soal.index', $bankSoal->id)->with('message_success', 'Soal berhasil diubah!');
    }

    public function destroy(Request $request, UjianBankSoal $bankSoal, UjianSoal $soal)
    {
        $this->authorizeSoal($bankSoal, $soal);

        foreach (self::KOLOM_GAMBAR as $field) {
            $this->hapusFileGambar($soal->{$field});
        }

        // Hard delete (bukan soft delete): ada unique index (id_bank_soal, no_soal),
        // supaya no_soal yang sama bisa langsung dipakai ulang.
        $soal->forceDelete();

        $this->log($request, 'menghapus soal', ['ujian_soal.id' => $soal->id, 'ujian_bank_soal.id' => $bankSoal->id]);
        return back()->with('message_success', 'Soal berhasil dihapus!');
    }

    public function hapusGambar(Request $request, UjianBankSoal $bankSoal, UjianSoal $soal, $field)
    {
        $this->authorizeSoal($bankSoal, $soal);
        abort_unless(in_array($field, self::KOLOM_GAMBAR), 404);

        $this->hapusFileGambar($soal->{$field});
        $soal->{$field}    = null;
        $soal->updated_by  = Auth::id();
        $soal->save();

        $this->log($request, 'menghapus gambar soal', ['ujian_soal.id' => $soal->id, 'field' => $field]);
        return back()->with('message_success', 'Gambar berhasil dihapus!');
    }

    /**
     * Validasi dasar + normalisasi kunci (lihat App\Helpers\KunciSoal).
     * Dipakai bersama oleh store() dan update().
     */
    private function validateSoal(Request $request, UjianBankSoal $bankSoal, ?UjianSoal $soal = null): array
    {
        $gambarRule = ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'];

        $data = $request->validate([
            'no_soal' => [
                'required', 'integer', 'min:1',
                Rule::unique('ujian_soal', 'no_soal')
                    ->where('id_bank_soal', $bankSoal->id)
                    ->ignore($soal?->id),
            ],
            'jenis'       => ['required', Rule::in([UjianSoal::JENIS_PG, UjianSoal::JENIS_PGK, UjianSoal::JENIS_BS])],
            'soal'        => ['nullable', 'string'], // boleh kosong: soal bisa berupa gambar saja
            'opsi_a'      => ['nullable', 'string'],
            'opsi_b'      => ['nullable', 'string'],
            'opsi_c'      => ['nullable', 'string'],
            'opsi_d'      => ['nullable', 'string'],
            'opsi_e'      => ['nullable', 'string'],
            'gambar_soal' => $gambarRule,
            'gambar_a'    => $gambarRule,
            'gambar_b'    => $gambarRule,
            'gambar_c'    => $gambarRule,
            'gambar_d'    => $gambarRule,
            'gambar_e'    => $gambarRule,
            'kunci_pg'    => ['nullable', 'string'],
            'kunci_pgk'   => ['nullable', 'array'],
            'kunci_bs'    => ['nullable', 'array'],
        ]);

        $kunciMentah = match ($data['jenis']) {
            UjianSoal::JENIS_PGK => implode('', (array) $request->input('kunci_pgk', [])),
            UjianSoal::JENIS_BS  => implode('', array_map(
                fn ($i) => (string) $request->input("kunci_bs.$i", ''),
                [0, 1, 2]
            )),
            default => (string) $request->input('kunci_pg', ''),
        };

        $opsi      = array_map(fn ($h) => $data["opsi_$h"] ?? null, ['a', 'b', 'c', 'd', 'e']);
        $adaGambar = array_map(
            fn ($h) => $request->hasFile("gambar_$h") || ! empty($soal?->{"gambar_$h"}),
            ['a', 'b', 'c', 'd', 'e']
        );

        try {
            $kunci = KunciSoal::normalize($data['jenis'], $kunciMentah);
            KunciSoal::validateOpsi($data['jenis'], $kunci, $opsi, $adaGambar);
        } catch (InvalidArgumentException $e) {
            throw ValidationException::withMessages(['kunci' => $e->getMessage()]);
        }

        $data['kunci'] = $kunci;

        if ($data['jenis'] === UjianSoal::JENIS_BS) {
            $data['opsi_d'] = $data['opsi_e'] = null;
        }

        return $data;
    }

    /**
     * Soal bs tidak pakai opsi D/E - pastikan gambar D/E ikut dikosongkan & filenya dihapus.
     */
    private function kosongkanGambarJikaBs(UjianSoal $soal)
    {
        if ($soal->jenis !== UjianSoal::JENIS_BS) {
            return;
        }

        foreach (['gambar_d', 'gambar_e'] as $field) {
            $this->hapusFileGambar($soal->{$field});
            $soal->{$field} = null;
        }
    }

    private function simpanGambar(Request $request, UjianSoal $soal)
    {
        foreach (['soal', 'a', 'b', 'c', 'd', 'e'] as $h) {
            $field = "gambar_$h";
            if ($request->hasFile($field)) {
                $this->hapusFileGambar($soal->{$field});
                $soal->{$field} = $this->uploadGambar($request->file($field), $h);
            }
        }
    }

    /**
     * Simpan file ke public_path('ujian/soal/{Y}/{m}/...') - folder publik yang sama
     * dengan yang sudah dipakai modul Soal, dan persis yang diharapkan aplikasi ujian
     * (https://.../ujian/soal/{file}). Hanya path relatif yang disimpan ke kolom.
     */
    private function uploadGambar(UploadedFile $file, string $prefix): string
    {
        $subfolder   = date('Y/m');
        $destination = public_path('ujian/soal/' . $subfolder);
        if (! file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        $filename = $prefix . '_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);

        return $subfolder . '/' . $filename;
    }

    private function hapusFileGambar(?string $relativePath)
    {
        if (! $relativePath) {
            return;
        }

        $fullPath = public_path('ujian/soal/' . $relativePath);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
}
