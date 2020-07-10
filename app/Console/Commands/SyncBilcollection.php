<?php

namespace App\Console\Commands;
use App\Notifier;
use Carbon\Carbon;
use App\Http\Controllers\BillingCollectionController;
use Storage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
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

            try {
                $response = $guzzle->request('GET', $url /*, ['proxy' => 'http://10.59.82.1:8080']*/);
                Storage::put($path.$imgName, $response->getBody());
                $response = sprintf('file %s downloaded, size:%d kb',$imgName, Storage::size($path.$imgName));
            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                // This is will catch all connection timeouts
                // Handle accordinly
                $response =  $e->getMessage();
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                // This will catch all 400 level errors.
                $response =  $e->getMessage();
            }catch (Guzzle\Http\Exception\BadResponseException $e) {
                $response =  $e->getMessage();
            }
            return $response;
    }

    public function proses($filename){
        $controller = new BillingCollectionController();
        //var_dump($this->option('testing'));
        if(is_array($filename)){
            foreach ($filename as $name){
                echo shell_exec(sprintf("php artisan Syncbilcollection --file=%s >> log.log",$name));
            }
        }else{
            $this->info('proses download :'.$filename);
            if($this->option('testing') == "false"){

                $user = Notifier::create([
                    'type' => 'CollectionImport',
                    'subject' => 'Collection Import file',
                    'message' => substr($this->guzzleDownload($filename,'http://10.250.191.103/collection/consumer/'.$filename,'/'), 0, 128),
                ]);
            }
            $this->info('Downloaded :'.$filename);
            if($this->option('testing') == "false")
            {
                $this->info('proses sum '. PHP_EOL);
                $controller->create($filename,null,1);

            }

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
            $date = Carbon::now()->subDays(2)->format('Ymd');
            $user = Notifier::create([
                'type' => 'CollectionImport',
                'subject' => 'Collection Import',
                'message' => 'Importing Collection at '.$date,
            ]);
            foreach($areas as $area){
                $filename[] = sprintf('%s_%s.csv',$date,$area);
            }
        }
        $this->proses($filename);
    }
}
