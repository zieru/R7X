<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\BilcodataserahCekBayarController;
use App\Models\BilcodataserahCekBayar;
use Illuminate\Console\Command;

class SyncBilcoDataserahCekBayar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncBilcoDataserah:CekBayar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek Bayar Bilco DataSerah';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $controller = new BilcodataserahCekBayarController();
        $x = $controller->fetch();
        foreach ($x->get()->toArray() as $row){
            $row = (array) $row;
            $row['import_batch'] = 0;
            BilcodataserahCekBayar::insert($row);
        }
        return 0;
    }
}
