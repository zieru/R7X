<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncBilcollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncBilcollection
                            {url : The URL to check}
                            {status=200 : The expected status code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Billco Collection CSV';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function file_get_contents_curl( $url ) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
    public function downloadFile( $imgName, $url, $path )
    {
        $data = $this->file_get_contents_curl( $url );
        file_put_contents( $path.$imgName, $data );
        echo "File downloaded!";

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->downloadFile("0200606_all.csv",'http://10.250.191.103/collection/consumer/0200606_all.csv')

        //
    }
}
