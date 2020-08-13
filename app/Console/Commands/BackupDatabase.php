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
        $x = sprintf('mysqldump --host="%s" -u"%s" -p"%s" %s --skip-lock-tables --single-transaction --quick | gzip > %s && rar a -hp%s %s.rar %s  && gupload %s.rar --config default=~/.gdriveunli.conf && ls %s/csv/*',
            config('database.connections.mysql.host'),
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            storage_path('app/backups/backup-' . Carbon::now()->format('Y-m-d') . '.gz'),
	        $this->backuppass,
            $this->backupname,
            storage_path('app/bilcollection/csv/'.$this->backupdate.'_*'),
	    $this->backupname,
	    storage_path('app/bilcollection')
        );
        echo $x;

        $this->process = Process::fromShellCommandline($x);
    }

    public function handle()
    {
        try {
            $this->process->setTimeout(3600);
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
