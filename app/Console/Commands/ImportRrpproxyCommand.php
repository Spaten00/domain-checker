<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportRrpproxyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rrpproxy:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the data from rrpproxy into the database.';

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
     * @return void
     */
    public function handle(): void
    {
        //TODO import into the database
    }
}
