<?php
namespace App\Console\Commands;

use App\Modules\Device\Models\Device;
use App\Modules\FilePesan\Models\FilePesan;
use App\Modules\Pesan\Models\Pesan;
use Illuminate\Console\Command;

class KirimWa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pesan:kirim';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim broadcast WA.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pesan = Pesan::whereStatus(0)->orderBy('created_at', 'asc')->limit(1)->get();

        foreach ($pesan as $kirim) {
            $device = Device::orderBy('last_used', 'asc')->first();

            $oneHourAgo = now()->subHour();

            // Cek apakah device perlu break (sudah 10 pesan dalam 1 jam)
            if ($device->counter >= 10 && $device->last_used > $oneHourAgo) {
                $this->info("Device #{$device->id} masih dalam periode break. Counter: {$device->counter}");
                return 0;
            }

            // Reset counter jika sudah lebih dari 1 jam
            if ($device->last_used < $oneHourAgo && $device->counter > 0) {
                $device->counter = 0;
            }

            // Delay 5-55 detik sebelum mengirim
            sleep(rand(5, 55));

            if ($kirim->id_file != null) {

                $file = FilePesan::find($kirim->id_file);

                $url  = "http://112.78.37.70:3000/api/sendFile";
                $data = [
                    "session" => "default",
                    "chatId"  => $kirim->nomor . "@c.us",
                    "caption" => $kirim->isi_pesan,
                    "file"    => [
                        "mimetype" => "application/pdf",
                        "filename" => "document.pdf",
                        "url"      => 'https://apps.smkn2semarang.sch.id/file_pesan/' . $file->nama_file,
                    ],
                ];

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'X-Api-Key: ' . $device->auth_key,
                    'Content-Type: application/json',
                ]);
                $response = curl_exec($ch);
                curl_close($ch);
            } else {
                $url  = "http://112.78.37.70:3000/api/sendText";
                $data = [
                    "session" => "default",
                    "chatId"  => $kirim->nomor . "@c.us",
                    "text"    => $kirim->isi_pesan,
                ];

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'X-Api-Key: ' . $device->auth_key,
                    'Content-Type: application/json',
                ]);
                $response = curl_exec($ch);
                curl_close($ch);
            }

            $response = json_decode($response);

            if (isset($response->id)) {
                $update         = Pesan::find($kirim->id);
                $update->status = 1;
                $update->save();
            } else {
                $update             = Pesan::find($kirim->id);
                $update->created_at = date('Y-m-d H:i:s');
                $update->save();
            }

            // Increment counter dan update last_used device
            $device->counter++;
            $device->last_used = now();
            $device->save();
        }

        return 0;
    }
}
