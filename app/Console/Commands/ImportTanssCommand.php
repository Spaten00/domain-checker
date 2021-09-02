<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportTanssCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tanss:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy the tanss-export from the server to the local directory via FTP';

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
     * Copy the tanss-export from the server to the local directory via FTP.
     *
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(): void
    {
        Storage::put('/tanssexport.json', Storage::disk('ftp')->get('/export/tanssexport.json'));
    }
}
