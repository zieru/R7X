<?php

namespace App\Console\Commands;
use App\BackupMan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabase extends Command
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
        $this->backupdate = Carbon::now()->subDays(2)->format('Ymd');
        $this->backupname = $this->backupdate.'-bilcocsv';
        $this->backuppass = Str::random(125);
        $x0 = sprintf('mysqldump --host="%s" -u"%s" -p"%s" %s --skip-lock-tables --single-transaction --quick | gzip > %s',
            config('database.connections.mysql.host'),
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            storage_path('app/backups/backup-' . Carbon::now()->format('Y-m-d') . '.gz')
        );
        $x1 = sprintf('head -1 %s > %s; tail -n +2 -q %s >> %s',
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_xxxxxxxxxxxxxx.csv'),
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_Sumatra.csv'),
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_Sum*.csv'),
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_Sumatra.csv')
        );
        $x2 = sprintf('mysqlimport --ignore-lines=1 --fields-terminated-by=, --local -u sabyan -p"zierong7" sabyan_r7s_data %s',
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_Sumatra.csv')
        );
        $x3 = sprintf('rar a -ep1 -hp%s %s.rar %s ',
            $this->backuppass,
            $this->backupname,
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_*')
        );
        $x = sprintf('%s && %s && %s && %s && gupload %s.rar --config default=.gdriveunli.conf && ls %s/csv/* && ls %s',
            $x0,
	        $x1,
	        $x2,
            $x3,
            $this->backupname,
            storage_path('app/bilcollection'),
            $this->backupname,
        );

        echo $x;
            $this->process = Process::fromShellCommandline($x)->setTimeout(3600);
    }

    public function handle()
    {
        try {
            $this->process->mustRun();
            BackupMan::create(array(
                'backupName'=> $this->backupname.'.rar',
                'backupPassword' => $this->backuppass
            ));
            $this->info('The backup has been proceed successfully.');
        } catch (ProcessFailedException $exception) {
            echo ($exception);
            $this->error('The backup process has been failed.');
        }
    }
}

?>
