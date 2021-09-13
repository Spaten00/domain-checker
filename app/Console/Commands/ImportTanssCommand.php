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
    protected $signature = 'tanss:import {--testing}';

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
            if ($this->option('testing')) {
                throw new ConnectionRuntimeException();
            }
            $ftpPath = Storage::disk('ftp')->get('/export/tanssexport.json');
            Storage::put('/tanssexports/tanssexport_' . date('Y_m_d') . '.json', $ftpPath);
            Storage::append('log.txt', now() . ': TANSS-Export-Datei importiert.');
        } catch (ConnectionRuntimeException $e) {
            Storage::append('log.txt', now() . ': Verbindung zum TANSS-Server konnte nicht hergestellt werden.');
            throw new ConnectionRuntimeException();
        } catch (FileNotFoundException $e) {
            Storage::append('log.txt', now() . ': TANSS-Export-Datei existiert nicht auf Server.');
            throw new FileNotFoundException();
        }
    }
}
