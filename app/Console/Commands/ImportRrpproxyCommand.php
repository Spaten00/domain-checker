<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\RrpproxyEntry;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\ConnectionRuntimeException;

class ImportRrpproxyCommand extends Command
{
    public const FTP_FILE_PATH = '/export/rrpproxyexport.json';
    public const MAX_FILES = 5;
    public const RRPPROXYEXPORTS_FOLDER = '/rrpproxyexports/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rrpproxy:import {--testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the data from rrpproxy into the database.';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $newFilePath = $this->importRrpproxyFile();
        $this->deleteOldFiles();
        $this->addNewEntriesToDatabase($newFilePath);
    }

    /**
     * @return string
     * @throws FileNotFoundException
     */
    private function importRrpproxyFile(): string
    {
        try {
            if ($this->option('testing')) {
                throw new ConnectionRuntimeException();
            }
            $ftpContent = Storage::disk('ftp')->get(self::FTP_FILE_PATH);
            $newFilePath = self::RRPPROXYEXPORTS_FOLDER . 'rrpproxyexport_' . date('Y_m_d') . '.json';
            Storage::put($newFilePath, $ftpContent);
            Storage::append('log.txt', now() . ': RRPproxy-Export-Datei importiert.');
            return $newFilePath;
        } catch (ConnectionRuntimeException $e) {
            Storage::append('log.txt', now() . ': Verbindung zum Server konnte nicht hergestellt werden.');
            throw new ConnectionRuntimeException();
        } catch (FileNotFoundException $e) {
            Storage::append('log.txt', now() . ': RRPproxy-Export-Datei existiert nicht auf Server.');
            throw new FileNotFoundException();
        }
    }

    private function deleteOldFiles(): void
    {
        while (count($files = Storage::allFiles(self::RRPPROXYEXPORTS_FOLDER)) > self::MAX_FILES) {
            Storage::append('log.txt', now() . ': Alte Datei ' . $files[0] . ' wurde gelöscht.');
            Storage::delete($files[0]);
        }
    }

    private function addNewEntriesToDatabase(string $filePath): void
    {
        $processedEntries = $this->processRrpproxyEntries($filePath);

        foreach ($processedEntries as $entry) {
            $domain = Domain::createDomain($entry['domain']);
            RrpproxyEntry::createOrUpdateRrpproxyEntry($entry, $domain);
        }
    }

    /**
     * @throws FileNotFoundException
     */
    private function processRrpproxyEntries(string $filePath): array
    {
        $json = json_decode(Storage::get($filePath));
        $processedEntries = [];

        foreach ($json as $entry) {
            $attributes = [
                'domain' => $entry->IDN,
                'rrpproxyContractStart' => $entry->{'Domain created date'},
                'rrpproxyContractEnd' => $entry->{'Domain registration expiration date'},
                'rrpproxyContractRenewal' => $entry->{'Domain renewal date'},
            ];
            $processedEntries[$entry->IDN] = $attributes;
        }
        return $processedEntries;
    }
}
