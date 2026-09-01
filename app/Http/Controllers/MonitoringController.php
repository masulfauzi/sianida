<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\IjinSholat;
use App\Models\PresensiSholat;
use App\Modules\Agenda\Models\Agenda;
use App\Modules\PresensiHarian\Models\PresensiHarian;
use App\Modules\Semester\Models\Semester;

class MonitoringController extends Controller
{
    public function monitoring_1(Request $request)
    {
        $semester    = Semester::get_semester_aktif();
        $id_semester = $semester?->id;
        $tgl = $request->get('tgl', today()->format('Y-m-d'));

        $data['tgl']       = $tgl;
        $data['chart_x']   = $this->buildChartData('X', $id_semester, $tgl);
        $data['chart_xi']  = $this->buildChartData('XI', $id_semester, $tgl);
        $data['chart_xii'] = $this->buildChartData('XII', $id_semester, $tgl);

        return view('PresensiHarian::presensiharian_monitoring', array_merge($data, ['title' => 'Presensi Harian']));
    }

    public function monitoring_2(Request $request)
    {
        $tgl = $request->get('tgl', today()->format('Y-m-d'));

        $semester    = Semester::get_semester_aktif();
        $id_semester = $semester?->id;

        $roster = DB::table('pesertadidik as p')
            ->join('siswa as s', 's.id', '=', 'p.id_siswa')
            ->join('kelas as k', 'k.id', '=', 'p.id_kelas')
            ->join('tingkat as t', 't.id', '=', 'k.id_tingkat')
            ->where('p.id_semester', $id_semester)
            ->where('p.is_magang', 0)
            ->whereNull('p.deleted_at')
            ->select('s.nisn', 't.tingkat', 'k.kelas as nama_kelas')
            ->get();

        $hadirNisn = PresensiSholat::where('jenis_presensi', 'Sholat Dzuhur')
            ->whereDate('Waktu_Presensi', $tgl)
            ->pluck('NISN')
            ->map(fn ($nisn) => trim((string) $nisn))
            ->flip();

        $ijinNisn = IjinSholat::whereDate('tanggal', $tgl)
            ->pluck('nisn')
            ->map(fn ($nisn) => trim((string) $nisn))
            ->flip();

        $tingkatList = ['X', 'XI', 'XII'];

        $charts = [];
        foreach ($tingkatList as $tingkat) {
            $siswaTingkat = $roster->where('tingkat', $tingkat)->values();

            $categories = $siswaTingkat->pluck('nama_kelas')->unique()->sort()->values();

            $dataHadir = [];
            $dataIjin  = [];
            $dataBelum = [];

            foreach ($categories as $kelas) {
                $siswaKelas = $siswaTingkat->where('nama_kelas', $kelas);
                $hadir = 0;
                $ijin  = 0;
                $belum = 0;

                foreach ($siswaKelas as $siswa) {
                    $nisn = trim((string) $siswa->nisn);
                    if (isset($hadirNisn[$nisn])) {
                        $hadir++;
                    } elseif (isset($ijinNisn[$nisn])) {
                        $ijin++;
                    } else {
                        $belum++;
                    }
                }

                $dataHadir[] = $hadir;
                $dataIjin[]  = $ijin;
                $dataBelum[] = $belum;
            }

            $charts[] = [
                'tingkat'    => $tingkat,
                'categories' => $categories,
                'series'     => $categories->isEmpty() ? [] : [
                    ['name' => 'Hadir', 'data' => $dataHadir],
                    ['name' => 'Ijin', 'data' => $dataIjin],
                    ['name' => 'Belum Presensi', 'data' => $dataBelum],
                ],
            ];
        }

        $data['tgl']    = $tgl;
        $data['charts'] = $charts;

        return view('monitoring_sholat', array_merge($data, ['title' => 'Presensi Sholat Dzuhur']));
    }

    public function monitoring_3(Request $request)
    {
        $agenda = Agenda::where('tgl_selesai', '>', now())
            ->orderBy('tgl_mulai')
            ->get();

        $data['agenda'] = $agenda;

        return view('monitoring_agenda', array_merge($data, ['title' => 'Agenda']));
    }

    private function buildChartData($tingkat, $id_semester, $tgl = null)
    {
        $urutanStatus = ['Tidak Hadir', 'Hadir', 'Terlambat', 'Sakit', 'Ijin'];

        $rows = PresensiHarian::rekap_kehadiran_per_kelas($tingkat, $id_semester, $tgl);

        $categories = $rows->pluck('nama_kelas')->unique()->values();
        $statuses   = $rows->pluck('status_kehadiran')->unique()
            ->sortBy(function ($status) use ($urutanStatus) {
                $index = array_search($status, $urutanStatus);
                return $index === false ? count($urutanStatus) : $index;
            })
            ->values();

        $series = [];
        foreach ($statuses as $status) {
            $dataPerKelas = [];
            foreach ($categories as $kelas) {
                $match = $rows->first(function ($r) use ($kelas, $status) {
                    return $r->nama_kelas === $kelas && $r->status_kehadiran === $status;
                });
                $dataPerKelas[] = $match ? (int) $match->jumlah : 0;
            }
            $series[] = ['name' => $status, 'data' => $dataPerKelas];
        }

        return [
            'categories' => $categories->values(),
            'series'     => $series,
        ];
    }
}
