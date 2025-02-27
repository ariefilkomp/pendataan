<?php

namespace App\Console\Commands;

use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendAnoman extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anoman:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengirim pesan yang ada di tabel message ke wa Dengan Anoman';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $messages = Message::whereNotNull('to')->where('sent_time', null)->get();
        if ($messages->count() == 0) {
            echo "Tidak ada pesan yang perlu dikirim";
            return;
        }

        foreach ($messages as $message) {

            if ($message->sending_time == null) {
                $this->send($message->to, $message);
            } else {
                $now = Carbon::now()->setTimezone('Asia/Jakarta');
                if($now->gte($message->sending_time)) {
                    $this->send($message->to, $message);
                }
            }
            
        }
    }

    private function send(String $no_hp, Message $message)
    {
        $url = env('ANOMAN_SEND_URL','http://localhost:5001/send-message');
        $token = env('ANOMAN_API_TOKEN','kmzway87aa');

        if (substr($no_hp, 0, 1) == '0') {
            $no_hp = '62' . substr($no_hp, 1);
        } elseif (substr($no_hp, 0, 1) == '+') {
            $no_hp = substr($no_hp, 1);
        }

        $pp = [
            'no_hp' => $no_hp,
            'pesan' => $message->message,
        ];
        
        $response = Http::accept('application/json')->withToken($token)->post($url, $pp);
        // echo 'Url: '.$url.'\n';
        // echo 'Token: '.$token.'\n';
        // echo 'Pesan: '.$message->message.'\n';
        // echo 'No HP: '.$no_hp.'\n';
        // echo $response->body();
        if ($response->successful()) {
            $message->sent_time = now();
            $message->save();
            echo "Berhasil send message to $no_hp. status: ".$response->status()."\n";
        } else {
            echo "Gagal send message to $no_hp. status: ".$response->status()."\n";
        }
    }
}
