<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
