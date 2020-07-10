<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use App\Http\Controllers\BillingCollectionController;
use Storage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;

class SyncBilcollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncBilcollection {--file=} {--testing=false}';

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

    public function guzzleDownload( $imgName, $url, $path ){
        $guzzle = new Client();
        $response = $guzzle->request('GET', $url, ['proxy' => 'http://10.59.82.1:8080']);
        Storage::put($path.$imgName, $response->getBody());
    }

    public function proses($filename){
        $controller = new BillingCollectionController();
        //var_dump($this->option('testing'));
        if(is_array($filename)){
            foreach ($filename as $name){
                shell_exec(sprintf("cd /home/sabyan/R7S/ && php artisan Syncbilcollection --file=%s >> /home/sabyan/log.log 2>&1",$name));
            }
        }else{
            $this->info('proses download :'.$filename);
            if($this->option('testing') == "false")$this->guzzleDownload($filename,'http://10.250.191.103/collection/consumer/'.$filename,'/');
            $this->info('Downloaded :'.$filename);
            $this->info('proses sum '. PHP_EOL);
            if($this->option('testing') == "false")$controller->create($filename,null);
        };
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $areas = array('Bali%20Nusra','Jabotabek','Jawa%20Barat','Jawa%20Tengah','Jawa%20Timur','Kalimantan','Puma','Sulawesi','Sumbagsel','Sumbagteng','Sumbagut','xxxxxxxxxxxxxx');
        $filename = $this->option('file');

        if($filename== null){
            $date = Carbon::now()->subHours(48)->format('Ymd');
            foreach($areas as $area){
                $filename[] = sprintf('%s_%s.csv',$date,$area);
            }
        }
        $this->proses($filename);
    }
}
