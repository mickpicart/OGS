<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SupervisionDatasController;

class loadAllWebsiteExtDatasAndErrorsToDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load external datas and errors to DataBase for all websites';

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
        SupervisionDatasController::loadAllWebsiteExtDatasAndErrorsToDb();
    }
}
