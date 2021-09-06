<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\ConnectionRuntimeException;

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
     * Copy the tanss-export from the server to the local directory via FTP with the date as a filename.
     *
     * @return void
     * @throws ConnectionRuntimeException
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        try {
            echo "NOICE";
            $ftpPath = Storage::disk('ftp')->get('/export/tanssexport.json');
            Storage::put('/tanssexports/tanssexport_' . date('Y_m_d') . '.json', $ftpPath);
        } catch (ConnectionRuntimeException $e) {
            echo "Connection could not be established";
            throw new ConnectionRuntimeException();
        } catch (FileNotFoundException $e) {
            echo "File not found";
            throw new FileNotFoundException();
        }
    }
}
