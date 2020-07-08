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

    public function file_get_contents_curl( $url ) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function guzzleDownload( $imgName, $url, $path ){
        $guzzle = new Client();
        $response = $guzzle->request('GET', $url, ['proxy' => 'http://10.59.82.1:8080']);
        Storage::put($path.$imgName, $response->getBody());
    }
    public function downloadFile( $imgName, $url, $path )
    {
        #$data = $this->file_get_contents_curl( $url );
        #file_put_contents( $path.$imgName, $data );
        #echo "File downloaded!";
	$contents = file_get_contents($url);
	$name = substr($url, strpos($url, '/')+1);

	#$tempfile = tempnam(sys_get_temp_dir(), $imgName);
	#copy($url,$tempfile);
	return Storage::put($imgName, $contents);
    }

    public function proses($filename){
        $controller = new BillingCollectionController();
        if(!$this->option('testing') === false)$this->guzzleDownload($filename,'http://10.250.191.103/collection/consumer/'.$filename,'/');

        $this->info('Downloaded :'.$filename);
        $this->info('proses sum '. PHP_EOL);
        if(!$this->option('testing') === 'false')$controller->create($filename,null);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $areas = array('Bali%20Nusra','Jabodetabek','Jawa%20Barat','Jawa%20Tengah','Jawa%Timur','Kalimantan','Puma','Sulawesi','Sumbagsel','Sumbagteng','Sumbagut','xxxxxxxxxxxxxx');
	    //$filename = $this->arguments()['file'];
        $filename = $this->option('file');

        if($filename== null){
            $date = Carbon::now()->subHours(48)->format('Ymd');
            foreach($areas as $area){
                $filename[] = sprintf('%s_%s.csv',$area,$date);
            }
        }
//echo $filename;
//die();

        if(is_array($filename)){
            foreach ($filename as $name){
                $this->info('proses download '.$name);
                $this->proses($name);
            }

        }else{
            $this->info('proses download '.$filename);
            $this->proses($filename);
        }


        //$controller->compactPOC('2020-06-09');
        //
    }
}
