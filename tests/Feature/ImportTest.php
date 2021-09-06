<?php

namespace Tests\Feature;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\ConnectionRuntimeException;
use Tests\TestCase;

class ImportTest extends TestCase
{
    /** @test */
    public function tanss_export_file_can_be_imported()
    {
        Storage::fake('ftp');
        Storage::fake('local');
        Storage::disk('ftp')->put('/export/tanssexport.json', 'LOL');
        Storage::disk('ftp')->assertExists('/export/tanssexport.json');
        Artisan::call('tanss:import');
        Storage::disk('local')->assertExists('/tanssexports/tanssexport_' . date('Y_m_d') . '.json');
    }

    /** @test */
    public function connection_runtime_exception_gets_thrown_when_ftp_to_tanss_cannot_establish()
    {
        $this->expectException(ConnectionRuntimeException::class);
        Artisan::call('tanss:import');
    }

    /** @test */
    public function file_not_found_exception_gets_thrown_when_tanss_export_file_does_not_exist()
    {
        Storage::fake('ftp');
        $this->expectException(FileNotFoundException::class);
        Artisan::call('tanss:import');
    }
}
