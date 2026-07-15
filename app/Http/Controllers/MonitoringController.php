<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        $rows = PresensiSholat::selectRaw('Angkatan, Kelas, COUNT(*) as jumlah')
            ->where('jenis_presensi', 'Sholat Dzuhur')
            ->whereDate('Waktu_Presensi', $tgl)
            ->groupBy('Angkatan', 'Kelas')
            ->orderBy('Kelas')
            ->get();

        $charts = [];
        foreach ($angkatan as $tahun) {
            $perAngkatan = $rows->where('Angkatan', $tahun)->values();
            $categories  = $perAngkatan->pluck('Kelas')->values();

            $charts[] = [
                'angkatan'   => $tahun,
                'categories' => $categories,
                'series'     => $categories->isEmpty() ? [] : [[
                    'name' => 'Sholat Dzuhur',
                    'data' => $perAngkatan->pluck('jumlah')->map(fn ($j) => (int) $j)->values(),
                ]],
            ];
        }

        $data['tgl']    = $tgl;
        $data['charts'] = $charts;

        return view('monitoring_sholat', array_merge($data, ['title' => 'Presensi Sholat Dzuhur']));
    }

    private function buildChartData($tingkat, $id_semester, $tgl = null)
    {
        $rows = PresensiHarian::rekap_kehadiran_per_kelas($tingkat, $id_semester, $tgl);

        $categories = $rows->pluck('nama_kelas')->unique()->values();
        $statuses   = $rows->pluck('status_kehadiran')->unique()->values();

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
