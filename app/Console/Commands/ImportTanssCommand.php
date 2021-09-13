<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\ConnectionRuntimeException;

class ImportTanssCommand extends Command
{
    const FTP_FILE_PATH = '/export/tanssexport.json';
    const MAX_FILES = 5;

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
     *
     * @return void
     * @throws ConnectionRuntimeException|FileNotFoundException
     */
    public function handle(): void
    {
        $this->importTanssFile();
        $this->deleteOldFiles();
    }

    /**
     * Copy the tanss-export-file from the server to the local directory via FTP with the date as a filename.
     *
     * @throws FileNotFoundException
     */
    public function importTanssFile(): void
    {
        try {
            if ($this->option('testing')) {
                throw new ConnectionRuntimeException();
            }
            $ftpContent = Storage::disk('ftp')->get(self::FTP_FILE_PATH);
            Storage::put('/tanssexports/tanssexport_' . date('Y_m_d') . '.json', $ftpContent);
            Storage::append('log.txt', now() . ': TANSS-Export-Datei importiert.');
        } catch (ConnectionRuntimeException $e) {
            Storage::append('log.txt', now() . ': Verbindung zum TANSS-Server konnte nicht hergestellt werden.');
            throw new ConnectionRuntimeException();
        } catch (FileNotFoundException $e) {
            Storage::append('log.txt', now() . ': TANSS-Export-Datei existiert nicht auf Server.');
            throw new FileNotFoundException();
        }
    }

    /**
     * Delete the oldest files if there are more files than <var>MAX_FILES</var>.
     */
    public function deleteOldFiles(): void
    {
        while (count($files = Storage::allFiles('/tanssexports')) > self::MAX_FILES) {
            Storage::append('log.txt', now() . ': Alte Datei ' . $files[0] . ' wurde gelöscht.');
            Storage::delete($files[0]);
        }
    }

}
