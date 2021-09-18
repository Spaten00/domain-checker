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

        Storage::disk('ftp')->put('/export/tanssexport.json', 'test');
        Storage::disk('ftp')->assertExists('/export/tanssexport.json');

        Artisan::call('tanss:import');
        Storage::disk('local')->assertExists('/tanssexports/tanssexport_' . date('Y_m_d') . '.json');

        Storage::disk('local')->assertExists('log.txt');
        $this->assertEquals(now() . ': TANSS-Export-Datei importiert.', Storage::disk('local')->get('log.txt'));
    }

    /** @test */
    public function connection_runtime_exception_gets_thrown_when_ftp_to_tanss_cannot_establish()
    {
        Storage::fake('local');

        $this->expectException(ConnectionRuntimeException::class);
        try {
            Artisan::call('tanss:import', ['--testing' => true]);
        } finally {
            Storage::disk('local')->assertExists('log.txt');
            $this->assertEquals(now() . ': Verbindung zum TANSS-Server konnte nicht hergestellt werden.', Storage::disk('local')->get('log.txt'));
        }

    }

    /** @test */
    public function file_not_found_exception_gets_thrown_when_tanss_export_file_does_not_exist()
    {
        Storage::fake('ftp');
        Storage::fake('local');

        $this->expectException(FileNotFoundException::class);
        try {
            Artisan::call('tanss:import');
        } finally {
            Storage::disk('local')->assertExists('log.txt');
            $this->assertEquals(now() . ': TANSS-Export-Datei existiert nicht auf Server.', Storage::disk('local')->get('log.txt'));
        }
    }

    /** @test */
    public function old_files_get_deleted()
    {
        Storage::fake('ftp');
        Storage::fake('local');

        Storage::disk('ftp')->put('/export/tanssexport.json', 'test');
        Artisan::call('tanss:import');
        $this->assertEquals(1, count(Storage::disk('local')->allFiles('/tanssexports')));

        Storage::put('/tanssexports/tanssexport_2010_01_01.json', 'test');
        Storage::put('/tanssexports/tanssexport_2009_12_31.json', 'test');
        Storage::put('/tanssexports/tanssexport_2010_01_02.json', 'test');
        Storage::put('/tanssexports/tanssexport_2010_01_03.json', 'test');
        Storage::put('/tanssexports/tanssexport_2010_01_04.json', 'test');
        $this->assertEquals(6, count(Storage::disk('local')->allFiles('/tanssexports')));

        Artisan::call('tanss:import');
        $this->assertEquals(5, count(Storage::disk('local')->allFiles('/tanssexports')));

        Storage::disk('local')->assertMissing('/tanssexports/tanssexport_2009_12_31.json');
        Storage::disk('local')->assertExists('/tanssexports/tanssexport_2010_01_01.json');
        Storage::disk('local')->assertExists('/tanssexports/tanssexport_2010_01_02.json');
        Storage::disk('local')->assertExists('/tanssexports/tanssexport_2010_01_03.json');
        Storage::disk('local')->assertExists('/tanssexports/tanssexport_2010_01_04.json');
    }
}