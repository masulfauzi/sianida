<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IjinSholat;
use App\Models\PresensiSholat;
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

        $angkatan = PresensiSholat::where('jenis_presensi', 'Sholat Dzuhur')
            ->distinct()
            ->orderBy('Angkatan', 'desc')
            ->limit(3)
            ->pluck('Angkatan');

        $hadirRows = PresensiSholat::selectRaw('Angkatan, Kelas, COUNT(*) as jumlah')
            ->where('jenis_presensi', 'Sholat Dzuhur')
            ->whereDate('Waktu_Presensi', $tgl)
            ->groupBy('Angkatan', 'Kelas')
            ->get();

        $ijinRows = IjinSholat::join('siswa', 'siswa.NISN', '=', 'ijin_sholat.nisn')
            ->whereDate('ijin_sholat.tanggal', $tgl)
            ->selectRaw('siswa.Angkatan as Angkatan, siswa.Kelas as Kelas, COUNT(*) as jumlah')
            ->groupBy('siswa.Angkatan', 'siswa.Kelas')
            ->get();

        $charts = [];
        foreach ($angkatan as $tahun) {
            $hadirPerAngkatan = $hadirRows->where('Angkatan', $tahun)->values();
            $ijinPerAngkatan  = $ijinRows->where('Angkatan', $tahun)->values();

            $categories = $hadirPerAngkatan->pluck('Kelas')
                ->merge($ijinPerAngkatan->pluck('Kelas'))
                ->unique()
                ->sort()
                ->values();

            $dataHadir = $categories->map(function ($kelas) use ($hadirPerAngkatan) {
                return (int) optional($hadirPerAngkatan->firstWhere('Kelas', $kelas))->jumlah;
            })->values();

            $dataIjin = $categories->map(function ($kelas) use ($ijinPerAngkatan) {
                return (int) optional($ijinPerAngkatan->firstWhere('Kelas', $kelas))->jumlah;
            })->values();

            $charts[] = [
                'angkatan'   => $tahun,
                'categories' => $categories,
                'series'     => $categories->isEmpty() ? [] : [
                    ['name' => 'Hadir', 'data' => $dataHadir],
                    ['name' => 'Ijin', 'data' => $dataIjin],
                ],
            ];
        }

        $data['tgl']    = $tgl;
        $data['charts'] = $charts;

        return view('monitoring_sholat', array_merge($data, ['title' => 'Presensi Sholat Dzuhur']));
    }

    private function buildChartData($tingkat, $id_semester, $tgl = null)
    {
        $urutanStatus = ['Tidak Hadir', 'Hadir', 'Sakit', 'Ijin'];

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
