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
        $pesan = Pesan::whereStatus(0)->orderBy('created_at', 'asc')->limit(3)->get();

        foreach ($pesan as $kirim) {
            $device = Device::orderBy('last_used', 'asc')->first();

            if ($kirim->id_file != null) {

                $file = FilePesan::find($kirim->id_file);

                // $curl = curl_init();

                // curl_setopt_array($curl, array(
                //     CURLOPT_URL => 'https://app.saungwa.com/api/create-message',
                //     CURLOPT_RETURNTRANSFER => true,
                //     CURLOPT_ENCODING => '',
                //     CURLOPT_MAXREDIRS => 10,
                //     CURLOPT_TIMEOUT => 0,
                //     CURLOPT_FOLLOWLOCATION => true,
                //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //     CURLOPT_CUSTOMREQUEST => 'POST',
                //     CURLOPT_POSTFIELDS => array(
                //         'appkey' => $device->app_key,
                //         'authkey' => $device->auth_key,
                //         'to' => $kirim->nomor,
                //         'message' => $kirim->isi_pesan,
                //         'file' => 'https://apps.smkn2semarang.sch.id/file_pesan/' . $file->nama_file,
                //         'sandbox' => 'false'
                //     ),
                // ));

                // $response = json_decode(curl_exec($curl));

                // curl_close($curl);
                // echo $response;

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

                echo $response;
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

                echo $response;

                // $curl = curl_init();

                // curl_setopt_array($curl, array(
                //     CURLOPT_URL => 'https://app.saungwa.com/api/create-message',
                //     CURLOPT_RETURNTRANSFER => true,
                //     CURLOPT_ENCODING => '',
                //     CURLOPT_MAXREDIRS => 10,
                //     CURLOPT_TIMEOUT => 0,
                //     CURLOPT_FOLLOWLOCATION => true,
                //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //     CURLOPT_CUSTOMREQUEST => 'POST',
                //     CURLOPT_POSTFIELDS => array(
                //             'appkey' => $device->app_key,
                //             'authkey' => $device->auth_key,
                //             'to' => $kirim->nomor,
                //             'message' => $kirim->isi_pesan,
                //             'sandbox' => 'false'
                //         ),
                // ));

                // $response = json_decode(curl_exec($curl));

                // curl_close($curl);
                // dd($response);
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

            $update_device            = Device::find($device->id);
            $update_device->last_used = date('Y-m-d H:i:s');
            $update_device->save();
        }

        // $this->info('Hellyeah! ' . $module . ' module was successfully created.');
    }
}
