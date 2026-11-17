<?php
namespace App\Modules\UjianSoal\Controllers;

use App\Helpers\KunciSoal;
use App\Helpers\Logger;
use App\Http\Controllers\Controller;
use App\Modules\Log\Models\Log;
use App\Modules\Semester\Models\Semester;
use App\Modules\UjianSoal\Models\UjianBankSoal;
use App\Modules\UjianSoal\Models\UjianSoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

/**
 * Import soal pilihan ganda (format soal semester lama) dari Excel ke bank soal.
 * Khusus admin: nama route berprefix "ujiansoalmonitoring." sehingga AuthorizeRequest
 * memakai privilege menu monitoring, bukan menu "ujiansoal" milik guru.
 */
class UjianSoalImportController extends Controller
{
    use Logger;
    protected $log;
    protected $title = 'Import Soal Excel';

    /** Nama header (lowercase, tanpa spasi/underscore) => kunci internal. */
    private const KOLOM = [
        'nosoal'  => 'no',
        'soal'    => 'soal',
        'pila'    => 'a',
        'pilb'    => 'b',
        'pilc'    => 'c',
        'pild'    => 'd',
        'pile'    => 'e',
        'jawaban' => 'jawaban',
    ];

    private const KOLOM_FILE = ['file1', 'file2', 'filea', 'fileb', 'filec', 'filed', 'filee'];

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    public function create(Request $request)
    {
        $semesterAktif = Semester::get_semester_aktif();

        $bankSoal = UjianBankSoal::with(['mapel', 'tingkat', 'jurusan', 'guru'])
            ->withCount('soal')
            ->get()
            ->sortBy(fn ($b) => [
                $b->id_semester === optional($semesterAktif)->id ? 0 : 1,
                optional($b->mapel)->mapel,
                optional($b->tingkat)->tingkat,
                $b->nama_bank_soal,
            ]);

        $refBank = [];
        foreach ($bankSoal as $b) {
            $refBank[$b->id] = implode(' · ', array_filter([
                $b->nama_bank_soal,
                optional($b->mapel)->mapel,
                $b->tingkat ? 'Kelas ' . $b->tingkat->tingkat : null,
                optional($b->jurusan)->jurusan ?: 'Semua Jurusan',
                optional($b->guru)->nama,
            ])) . " ({$b->soal_count} soal)";
        }

        $data['refBank']  = $refBank;
        $data['selected'] = old('id_bank_soal', $request->query('bank'));

        $this->log($request, 'melihat halaman ' . $this->title);
        return view('UjianSoal::ujian_soal_import', array_merge($data, ['title' => $this->title]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_bank_soal' => 'required|exists:ujian_bank_soal,id',
            'file'         => 'required|file|mimes:xls,xlsx|max:10240',
            'konfirmasi'   => 'accepted',
        ], [
            'id_bank_soal.required' => 'Bank soal wajib dipilih.',
            'id_bank_soal.exists'   => 'Bank soal tidak ditemukan.',
            'file.required'         => 'File Excel wajib diunggah.',
            'file.mimes'            => 'File harus berformat .xls atau .xlsx.',
            'file.max'              => 'Ukuran file maksimal 10 MB.',
            'konfirmasi.accepted'   => 'Centang konfirmasi bahwa soal dengan nomor yang sama akan ditimpa.',
        ]);

        $bankSoal = UjianBankSoal::findOrFail($request->input('id_bank_soal'));

        [$soalPerNomor, $duplikat, $nomorBergambar] = $this->bacaExcel($request->file('file')->getRealPath());

        $baru = $diperbarui = 0;
        DB::transaction(function () use ($bankSoal, $soalPerNomor, &$baru, &$diperbarui) {
            $lama = UjianSoal::where('id_bank_soal', $bankSoal->id)->get()->keyBy('no_soal');

            foreach ($soalPerNomor as $no => $row) {
                $soal = $lama->get($no);
                if ($soal) {
                    $soal->updated_by = Auth::id();
                    $diperbarui++;
                } else {
                    $soal               = new UjianSoal();
                    $soal->id_bank_soal = $bankSoal->id;
                    $soal->no_soal      = $no;
                    $soal->created_by   = Auth::id();
                    $baru++;
                }

                // Kolom gambar sengaja tidak disentuh: Excel tidak membawa file gambar.
                $soal->jenis  = UjianSoal::JENIS_PG;
                $soal->soal   = $row['soal'];
                $soal->opsi_a = $row['a'];
                $soal->opsi_b = $row['b'];
                $soal->opsi_c = $row['c'];
                $soal->opsi_d = $row['d'];
                $soal->opsi_e = $row['e'];
                $soal->kunci  = $row['kunci'];
                $soal->save();
            }
        });

        $this->log($request, 'mengimport soal dari Excel', [
            'ujian_bank_soal.id' => $bankSoal->id,
            'baru'               => $baru,
            'diperbarui'         => $diperbarui,
        ]);

        return redirect()
            ->route('ujiansoalmonitoring.import.create', ['bank' => $bankSoal->id])
            ->with('message_success', "Import selesai ke bank soal \"{$bankSoal->nama_bank_soal}\": {$baru} soal baru, {$diperbarui} soal diperbarui.")
            ->with('import_hasil', [
                'id_bank_soal'    => $bankSoal->id,
                'duplikat'        => $duplikat,
                'nomor_bergambar' => $nomorBergambar,
            ]);
    }

    /**
     * Baca & validasi seluruh isi Excel. Jika ada satu baris salah, lempar
     * ValidationException berisi semua error (tidak ada data yang disimpan).
     *
     * @return array{0: array<int, array>, 1: array<int, int>, 2: array<int, int>}
     *         [soal per nomor (baris terakhir menang), nomor duplikat => jumlah, nomor yang punya gambar]
     */
    private function bacaExcel(string $path): array
    {
        try {
            $rows = IOFactory::load($path)->getActiveSheet()->toArray(null, true, false, false);
        } catch (Throwable $e) {
            throw ValidationException::withMessages(['file' => 'File Excel tidak bisa dibaca: ' . $e->getMessage()]);
        }

        $header = array_map(
            fn ($h) => strtolower(str_replace([' ', '_'], '', trim((string) $h))),
            array_shift($rows) ?? []
        );

        $idx = [];
        foreach (self::KOLOM as $nama => $key) {
            $pos = array_search($nama, $header, true);
            if ($pos === false) {
                $kurang[] = $nama;
                continue;
            }
            $idx[$key] = $pos;
        }
        if (! empty($kurang)) {
            throw ValidationException::withMessages([
                'file' => 'Kolom wajib tidak ditemukan di baris pertama: ' . implode(', ', $kurang)
                    . '. Gunakan header: No Soal, Soal, PilA, PilB, PilC, PilD, PilE, Jawaban.',
            ]);
        }
        $idxFile = array_keys(array_intersect($header, self::KOLOM_FILE));

        $hasil = $hitung = $bergambar = $errors = [];
        foreach ($rows as $i => $row) {
            $baris = $i + 2; // +1 header, +1 karena Excel mulai dari 1
            $ambil = fn ($key) => trim((string) ($row[$idx[$key]] ?? ''));

            if ($ambil('soal') === '') {
                continue; // baris kosong
            }

            $noMentah = $ambil('no');
            if (! preg_match('/^\d+(\.0+)?$/', $noMentah) || (int) $noMentah < 1) {
                $errors[] = "Baris {$baris}: No Soal \"{$noMentah}\" harus bilangan bulat ≥ 1.";
                continue;
            }
            $no = (int) $noMentah;

            $opsi = [$ambil('a'), $ambil('b'), $ambil('c'), $ambil('d'), $ambil('e')];
            try {
                $kunci = KunciSoal::normalize(UjianSoal::JENIS_PG, $ambil('jawaban'));
                KunciSoal::validateOpsi(UjianSoal::JENIS_PG, $kunci, $opsi, []);
            } catch (InvalidArgumentException $e) {
                $errors[] = "Baris {$baris} (No Soal {$no}): jawaban \"{$ambil('jawaban')}\" tidak valid, {$e->getMessage()}.";
                continue;
            }

            $hitung[$no] = ($hitung[$no] ?? 0) + 1;
            // Baris terakhir dengan nomor yang sama menimpa baris sebelumnya.
            $hasil[$no] = [
                'soal'  => $ambil('soal'),
                'a'     => $opsi[0] !== '' ? $opsi[0] : null,
                'b'     => $opsi[1] !== '' ? $opsi[1] : null,
                'c'     => $opsi[2] !== '' ? $opsi[2] : null,
                'd'     => $opsi[3] !== '' ? $opsi[3] : null,
                'e'     => $opsi[4] !== '' ? $opsi[4] : null,
                'kunci' => $kunci,
            ];

            $punyaGambar = false;
            foreach ($idxFile as $p) {
                if (trim((string) ($row[$p] ?? '')) !== '') {
                    $punyaGambar = true;
                }
            }
            $bergambar[$no] = $punyaGambar;
        }

        if ($errors) {
            throw ValidationException::withMessages(['file' => $errors]);
        }
        if (! $hasil) {
            throw ValidationException::withMessages(['file' => 'Tidak ada soal yang bisa diimport dari file ini.']);
        }

        ksort($hasil);
        $duplikat       = array_filter($hitung, fn ($n) => $n > 1);
        $nomorBergambar = array_keys(array_filter($bergambar));
        sort($nomorBergambar);

        return [$hasil, $duplikat, $nomorBergambar];
    }
}
