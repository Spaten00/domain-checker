<?php

namespace Tests\Feature;

use App\Models\Domain;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\ConnectionRuntimeException;
use Tests\TestCase;

class ImportTanssTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tanss_export_file_can_be_imported()
    {
        Storage::fake('ftp');
        Storage::fake('local');
        Storage::disk('ftp')->put('/export/tanssexport.json',
            '[{
            "id":"1",
            "kdnr":"100000",
            "name":"aks Service GmbH",
            "domain":"aks-service.de",
            "provider_name":"aks Service GmbH",
            "contract_duration_start":"2013-07-20",
            "contract_duration_end":"2015-05-21"
            },
            {"id":"2",
            "kdnr":"100000",
            "name":"aks Service GmbH",
            "domain":"aks-service",
            "provider_name":"aks Service GmbH",
            "contract_duration_start":"0000-07-20",
            "contract_duration_end":"0000-00-00"
            }]');
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
            $this->assertEquals(now() . ': Verbindung zum Server konnte nicht hergestellt werden.', Storage::disk('local')->get('log.txt'));
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

        Storage::disk('ftp')->put('/export/tanssexport.json',
            '[{
            "id":"1",
            "kdnr":"100000",
            "name":"aks Service GmbH",
            "domain":"aks-service.de",
            "provider_name":"aks Service GmbH",
            "contract_duration_start":"2013-07-20",
            "contract_duration_end":"2015-05-21"
            }]');
        Artisan::call('tanss:import');
        $this->assertCount(1, Storage::disk('local')->allFiles('/tanssexports'));

        Storage::put('/tanssexports/tanssexport_2010_01_01.json', 'test');
        Storage::put('/tanssexports/tanssexport_2009_12_31.json', 'test');
        Storage::put('/tanssexports/tanssexport_2010_01_02.json', 'test');
        Storage::put('/tanssexports/tanssexport_2010_01_03.json', 'test');
        Storage::put('/tanssexports/tanssexport_2010_01_04.json', 'test');
        $this->assertCount(6, Storage::disk('local')->allFiles('/tanssexports'));

        Artisan::call('tanss:import');
        $this->assertCount(5, Storage::disk('local')->allFiles('/tanssexports'));

        Storage::disk('local')->assertMissing('/tanssexports/tanssexport_2009_12_31.json');
        Storage::disk('local')->assertExists('/tanssexports/tanssexport_2010_01_01.json');
        Storage::disk('local')->assertExists('/tanssexports/tanssexport_2010_01_02.json');
        Storage::disk('local')->assertExists('/tanssexports/tanssexport_2010_01_03.json');
        Storage::disk('local')->assertExists('/tanssexports/tanssexport_2010_01_04.json');
    }

    /** @test */
    public function new_entries_will_be_added_to_the_database()
    {
        Storage::fake('ftp');
        Storage::fake('local');
        Storage::disk('ftp')->put('/export/tanssexport.json',
            '[{
            "id":"1",
            "kdnr":"100000",
            "name":"aks Service GmbH",
            "domain":"aks-service.de",
            "provider_name":"aks Service GmbH",
            "contract_duration_start":"2013-07-20",
            "contract_duration_end":"2015-05-21"
            },
            {"id":"2",
            "kdnr":"100000",
            "name":"aks Service GmbH",
            "domain":"aks-service",
            "provider_name":"aks Service GmbH",
            "contract_duration_start":"0000-07-20",
            "contract_duration_end":"0000-00-00"
            }]');
        $domain = Domain::find(1);
        $this->assertModelMissing($domain);
        Artisan::call('tanss:import');
        $this->assertModelExists(Domain::find(1));
        $this->assertModelExists(Domain::find(2));
    }

//    /** @test */
//    public function existing_tanss_entries_can_be_updated()
//    {
//        Storage::fake('ftp');
//        Storage::fake('local');
//        Storage::disk('ftp')->put('/export/tanssexport.json',
//            '[{
//            "id":"1",
//            "kdnr":"100000",
//            "name":"aks Service GmbH",
//            "domain":"aks-service.de",
//            "provider_name":"aks Service GmbH",
//            "contract_duration_start":"2013-07-20",
//            "contract_duration_end":"2015-05-21"
//            },
//            {"id":"2",
//            "kdnr":"100000",
//            "name":"aks Service GmbH",
//            "domain":"aks-service",
//            "provider_name":"aks Service GmbH",
//            "contract_duration_start":"0000-07-20",
//            "contract_duration_end":"0000-00-00"
//            }]');
//        Artisan::call('tanss:import');
//    }
}
