<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Domain;
use App\Models\TanssEntry;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\ConnectionRuntimeException;

class ImportTanssCommand extends Command
{
    const FTP_FILE_PATH = '/export/tanssexport.json';
    const MAX_FILES = 5;
    const TANSSEXPORTS_FOLDER = '/tanssexports/';

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
    protected $description = 'Copy the tanss-export-file from the server to the local directory via FTP and import into the database.';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws ConnectionRuntimeException|FileNotFoundException
     */
    public function handle(): void
    {
        $newFilePath = $this->importTanssFile();
        $this->deleteOldFiles();
        $this->addNewEntriesToDatabase($newFilePath);
    }

    /**
     * Copy the tanss-export-file from the server to the local directory via FTP with the date as a filename.
     *
     * @throws FileNotFoundException
     */
    private function importTanssFile(): string
    {
        try {
            if ($this->option('testing')) {
                throw new ConnectionRuntimeException();
            }
            $ftpContent = Storage::disk('ftp')->get(self::FTP_FILE_PATH);
            $newFilePath = self::TANSSEXPORTS_FOLDER . 'tanssexport_' . date('Y_m_d') . '.json';
            Storage::put($newFilePath, $ftpContent);
            Storage::append('log.txt', now() . ': TANSS-Export-Datei importiert.');
            return $newFilePath;
        } catch (ConnectionRuntimeException $e) {
            Storage::append('log.txt', now() . ': Verbindung zum Server konnte nicht hergestellt werden.');
            throw new ConnectionRuntimeException();
        } catch (FileNotFoundException $e) {
            Storage::append('log.txt', now() . ': TANSS-Export-Datei existiert nicht auf Server.');
            throw new FileNotFoundException();
        }
    }

    /**
     * Delete the oldest files if there are more files than <var>MAX_FILES</var>.
     */
    private function deleteOldFiles(): void
    {
        while (count($files = Storage::allFiles(self::TANSSEXPORTS_FOLDER)) > self::MAX_FILES) {
            Storage::append('log.txt', now() . ': Alte Datei ' . $files[0] . ' wurde gelöscht.');
            Storage::delete($files[0]);
        }
    }

    /**
     * Add all new entries from the tanss-export-file to the database.
     *
     * @throws FileNotFoundException
     */
    private function addNewEntriesToDatabase(string $filePath): void
    {
        $processedEntries = $this->processTanssEntries($filePath);

        foreach ($processedEntries as $entry) {
            $customer = Customer::createCustomer($entry['customerId'], $entry['customerName']);
            $domain = Domain::createDomain($entry['domain']);
            TanssEntry::createTanssEntry($entry, $customer, $domain);
        }
    }

    /**
     * Process all entries of the given file and return an array containing the content.
     *
     * @param string $filePath
     * @return array
     * @throws FileNotFoundException
     */
    private function processTanssEntries(string $filePath): array
    {
        $json = json_decode(Storage::get($filePath));
        $processedEntries = [];

        foreach ($json as $entry) {
            $rootDomain = $this->getRootDomain($entry->domain);
            $attributes = [
                'externalId' => $entry->id,
                'customerId' => $entry->kdnr,
                'customerName' => $entry->name,
                'domain' => $rootDomain,
                'providerName' => $entry->provider_name,
                'tanssContractStart' => $entry->contract_duration_start,
                'tanssContractEnd' => $entry->contract_duration_end,
            ];
            // TODO just push into array, no need of key?
            $processedEntries[$rootDomain] = $attributes;
        }
        return $processedEntries;
    }

    /**
     * Cleans the domain name and gives it back as a string.
     *
     * @param $uncleanDomainName
     * @return string
     */
    private function getRootDomain($uncleanDomainName): string
    {
        $prefixExploded = explode('//', trim($uncleanDomainName));
        $fqdn = trim(end($prefixExploded));

        $fqdnExploded = explode('.', $fqdn);

        // check if at least two parts exists
        if (count($fqdnExploded) >= 2) {
            $rootDomainParts = array_slice($fqdnExploded, -2, 2);
        } else {
            return $fqdnExploded[0];
        }

        return implode('.', [trim($rootDomainParts[0]), trim($rootDomainParts[1])]);
    }

}
