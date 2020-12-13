<?php

namespace App\Console\Commands;
use App\Helpers\AppHelper;
use App\Http\Controllers\BillingCollectionController;
use App\Importer;
use App\Notifier;
use App\BackupMan;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DBBackup extends Command
{
    protected $signature = 'db:backup {--date= : format dd-mm-YYYY} {--onlybackup=false} {--onlymergecollection=false} {--onlyimportcollection=false}';

    protected $description = 'Backup the database';

    protected $process;
    private $backupdate;
    private $backupname;
    private $backuppass;
    public function __construct()
    {
        parent::__construct();
    }
    public function importCollectionToDB(){
        $x2 = sprintf('mysqlimport --defaults-file=/home/sabyan/.my.cnf --ignore-lines=1 --fields-terminated-by=, --local sabyan_r7s_data %s',
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_Sumatra.csv')
        );

        $this->info('Import Sumatra to DB');
        DB::connection('mysql2')->statement(sprintf('CREATE TABLE %s_Sumatra LIKE 20200802_all',$this->backupdate));
        return shell_exec($x2);
    }

    public function mergeCollectionCSV(){
        $this->info('Making Sumatra CSV');
        $x1 = sprintf('head -1 %s > %s; tail -n +2 -q %s >> %s',
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_xxxxxxxxxxxxxx.csv'),
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_Sumatra.csv'),
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_Sum*.csv'),
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_Sumatra.csv')
        );
        return shell_exec($x1);
    }

    public function backup(){
        if($this->option('date')){
            try {
                $this->backupdate = Carbon::createFromFormat('d-m-Y', $this->option('date'))->format('Ymd');
            }
            catch (\Exception $e){
                $this->error('date is invalid');
            }
        }else{
            $this->backupdate = Carbon::now()->subDays(2)->format('Ymd');
        }

        if(Importer::where('status','finish')->where('tipe','bilcollection:import')->where('filename','like',$this->backupdate.'%')->get()->count() <= 12){
            $this->error('not completed ('.Importer::where('status','finish')->where('tipe','bilcollection:import')->where('filename','like',$this->backupdate.'%')->get()->count().'/12)');
            die();
        }


        $this->backupname = $this->backupdate.'-bilcocsv-'.Str::random(4);
        $this->backuppass = Str::random(125);

        if($this->option('onlymergecollection') != "false"){
            $this->mergeCollectionCSV();
            exit();
        }
        if($this->option('onlyimportcollection') != "false"){
            $this->importCollectionToDB();
            exit();
        }
        Notifier::create([
                    'type' => 'Backup',
                    'subject' => 'Backup',
                    'message' => 'backup : '.$this->backuppass ,
                ]);
        
        $x0 = sprintf('mysqldump --defaults-file=/home/sabyan/.my.cnf --host="%s" %s --skip-lock-tables --single-transaction --quick | gzip > %s',
            config('database.connections.mysql.host'),
            config('database.connections.mysql.database'),
            storage_path('app/backups/backup-' . Carbon::now()->format('Y-m-d') . '.gz')
        );


        $x3 = sprintf('rar a -ep1 -hp%s %s.rar %s ',
            $this->backuppass,
            storage_path('app/bilcollection/archive/'.$this->backupname),
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_*')
        );
        $x = sprintf('%s && gupload %s.rar --config default=.gdriveunli.conf',
            $x3,
            storage_path('app/bilcollection/archive/'.$this->backupname),
            storage_path('app/bilcollection'),
            storage_path('app/bilcollection/archive/'.$this->backupname),
        );
        

        $this->mergeCollectionCSV();
        $this->importCollectionToDB();

//    die();
        $this->info('Dump DB');
        shell_exec($x0);

        $this->process = Process::fromShellCommandline($x)->setTimeout(3600);
        $this->info('Finalising');
        return $this->process->mustRun();
    }
    public function handle()
    {
    try {
            $this->backup();
            BackupMan::create(array(
                'backupName'=> $this->backupname.'.rar',
                'backupPassword' => $this->backuppass
            ));
            $this->info('CleanUp');
            $xc = sprintf('rm %s/csv/* && rm %s.rars',
            storage_path('app/bilcollection'),
            storage_path('app/bilcollection/archive/'.$this->backupname));
            shell_exec($xc);
            $this->info('The backup has been proceed successfully.');
        } catch (ProcessFailedException $exception) {
            echo ($exception);
            $this->error('The backup process has been failed.');
        }
    }
}

?>
