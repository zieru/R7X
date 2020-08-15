<?php

namespace App\Console\Commands;
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
    protected $signature = 'db:backup';

    protected $description = 'Backup the database';

    protected $process;
    private $backupdate;
    private $backupname;
    private $backuppass;
    public function __construct()
    {
        parent::__construct();
    }

    public function backup(){

        $this->backupdate = Carbon::now()->subDays(2)->format('Ymd');
        $this->backupname = $this->backupdate.'-bilcocsv-'.Str::random(4);
        $this->backuppass = Str::random(125);
    
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
        $x1 = sprintf('head -1 %s > %s; tail -n +2 -q %s >> %s',
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_xxxxxxxxxxxxxx.csv'),
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_Sumatra.csv'),
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_Sum*.csv'),
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_Sumatra.csv')
        );
        $x2 = sprintf('mysqlimport --defaults-file=/home/sabyan/.my.cnf --ignore-lines=1 --fields-terminated-by=, --local sabyan_r7s_data %s',
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_Sumatra.csv')
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
        
        $this->info('Making Sumatra CSV');
        shell_exec($x1);
        $this->info('Import Sumatra to DB');
        DB::connection('mysql2')->statement(sprintf('CREATE TABLE %s_Sumatra LIKE 20200802_all',$this->backupdate));
        shell_exec($x2);
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
