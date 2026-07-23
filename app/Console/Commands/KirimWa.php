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
            if ($device->counter >= 9 && $device->last_used > $oneHourAgo) {
                $this->info("Device #{$device->id} masih dalam periode break. Counter: {$device->counter}");
                return 0;
            }

            // Reset counter jika sudah lebih dari 1 jam
            if ($device->last_used < $oneHourAgo && $device->counter > 0) {
                $device->counter = 0;
            }

            // Delay 5-55 detik sebelum mengirim
            sleep(rand(5, 115));

            if ($kirim->id_file != null) {

                $file = FilePesan::find($kirim->id_file);

                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL            => 'https://app.saungwa.com/api/create-message',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING       => '',
                    CURLOPT_MAXREDIRS      => 10,
                    CURLOPT_TIMEOUT        => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST  => 'POST',
                    CURLOPT_POSTFIELDS     => [
                        'appkey'  => $device->app_key,
                        'authkey' => $device->auth_key,
                        'to'      => $kirim->nomor,
                        'message' => $kirim->isi_pesan,
                        'file'    => 'https://apps.smkn2semarang.sch.id/file_pesan/' . $file->nama_file,
                        'sandbox' => 'false',
                    ],
                ]);

                $rawResponse = curl_exec($curl);
                curl_close($curl);

                $this->info("[Device #{$device->id}] Response kirim file ke {$kirim->nomor}: {$rawResponse}");

                $response = json_decode($rawResponse);

                if (isset($response->message_status) && $response->message_status == 'Success') {
                    $update         = Pesan::find($kirim->id);
                    $update->status = 1;
                    $update->save();
                } elseif (isset($response->error) && $response->error == 'Request Failed') {
                    $update             = Pesan::find($kirim->id);
                    $update->created_at = date('Y-m-d H:i:s');
                    $update->save();
                }
            } else {
                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL            => 'https://app.saungwa.com/api/create-message',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING       => '',
                    CURLOPT_MAXREDIRS      => 10,
                    CURLOPT_TIMEOUT        => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST  => 'POST',
                    CURLOPT_POSTFIELDS     => [
                        'appkey'  => $device->app_key,
                        'authkey' => $device->auth_key,
                        'to'      => $kirim->nomor,
                        'message' => $kirim->isi_pesan,
                        'sandbox' => 'false',
                    ],
                ]);

                $rawResponse = curl_exec($curl);
                curl_close($curl);

                $this->info("[Device #{$device->id}] Response kirim teks ke {$kirim->nomor}: {$rawResponse}");

                $response = json_decode($rawResponse);

                if (isset($response->message_status) && $response->message_status == 'Success') {
                    $update         = Pesan::find($kirim->id);
                    $update->status = 1;
                    $update->save();
                } elseif (isset($response->error) && $response->error == 'Request Failed') {
                    $update             = Pesan::find($kirim->id);
                    $update->created_at = date('Y-m-d H:i:s');
                    $update->save();
                }
            }

            // Increment counter dan update last_used device
            $device->counter++;
            $device->last_used = now();
            $device->save();
        }

        return 0;
    }
}
