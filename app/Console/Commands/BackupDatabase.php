<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use Illuminate\Console\Command;
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
        $x = sprintf('mysqldump --host="%s" -u"%s" -p"%s" %s --skip-lock-tables --single-transaction --quick | gzip > %s && rm %s/csv/*',
            config('database.connections.mysql.host'),
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            storage_path('app/backups/backup-' . Carbon::now()->format('Y-m-d') . '.gz'),
	    storage_path('app/bilcollection')
        );
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
