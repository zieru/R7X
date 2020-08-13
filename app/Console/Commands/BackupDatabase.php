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

    public function __construct()
    {
        parent::__construct();
        $backupdate = Carbon::now()->subDays(2)->format('Ymd');
        $backupname = $backupdate.'-bilcocsv.rar';
        $backuppass = Str::random(125);


        $x = sprintf('mysqldump --host="%s" -u"%s" -p"%s" %s --skip-lock-tables --single-transaction --quick | gzip > %s && rar a -hp%s %s.rar %s  && gupload log.log --config default=~/.gdriveunli.conf && ls %s/csv/*',
            config('database.connections.mysql.host'),
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            storage_path('app/backups/backup-' . Carbon::now()->format('Y-m-d') . '.gz'),
	        $backuppass,
            $backupname,
            storage_path('app/bilcollection/csv/'.$backupdate.'_*'),
	    $backupname,
	    storage_path('app/bilcollection')
        );
        BackupMan::create(array(
            'backupName'=> $backupname,
            'backupPassword' => $backuppass
        ));
        $this->process = Process::fromShellCommandline($x);
    }

    public function handle()
    {
        try {
            $this->process->mustRun();
            $this->info('The backup has been proceed successfully.');
        } catch (ProcessFailedException $exception) {
            echo ($exception);
            $this->error('The backup process has been failed.');
        }
    }
}

?>
