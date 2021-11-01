<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\RrpproxyEntry;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\ConnectionRuntimeException;
use Tests\TestCase;

class ImportRrpproxyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tanss_export_file_can_be_imported()
    {
        Storage::fake('ftp');
        Storage::fake('local');
        Storage::disk('ftp')->put('/export/rrpproxyexport.json',
            '[{
            "Domain": "aks-service.de",
            "IDN": "aks-service.de",
            "Roid": "3831208518028_DOMAIN-KEYSYS",
            "Domain created date": "2009-07-23 15:08:08",
            "Domain created by": "external",
            "Domain updated date": "2019-04-10 16:21:12",
            "Domain updated by": "aksservicegmbh",
            "Domain registration expiration date": "2021-07-23 15:08:08",
            "Domain renewal date": "2021-07-22 15:08:08",
            "Domain zone": "de",
            "Transfer Date": "2009-07-23 15:08:08",
            "Auth Code": "",
            "Renewalmode": "DEFAULT",
            "Transfermode": "DEFAULT",
            "Status": "ACTIVE",
            "Nameserver": "ns5.kasserver.com;ns6.kasserver.com",
            "Admincontact": "P-TLL1757",
            "Techcontact": "P-TLL1757",
            "Billingcontact": "P-TLL1757",
            "Ownercontact": "P-TLL1757",
            "Domaintags": "aks",
            "Domaincomment": "",
            "Paid until": "2021-07-23 15:08:08",
            "": ""
            }]');
        Storage::disk('ftp')->assertExists('/export/rrpproxyexport.json');

        Artisan::call('rrpproxy:import');
        Storage::disk('local')->assertExists('/rrpproxyexports/rrpproxyexport_' . date('Y_m_d') . '.json');

        Storage::disk('local')->assertExists('log.txt');
        $this->assertEquals(now() . ': RRPproxy-Export-Datei importiert.', Storage::disk('local')->get('log.txt'));
    }

    /** @test */
    public function connection_runtime_exception_gets_thrown_when_ftp_to_tanss_cannot_establish()
    {
        Storage::fake('local');

        $this->expectException(ConnectionRuntimeException::class);
        try {
            Artisan::call('rrpproxy:import', ['--testing' => true]);
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
            Artisan::call('rrpproxy:import');
        } finally {
            Storage::disk('local')->assertExists('log.txt');
            $this->assertEquals(now() . ': RRPproxy-Export-Datei existiert nicht auf Server.', Storage::disk('local')->get('log.txt'));
        }
    }

    /** @test */
    public function old_files_get_deleted()
    {
        Storage::fake('ftp');
        Storage::fake('local');

        Storage::disk('ftp')->put('/export/rrpproxyexport.json',
            '[{
            "Domain": "aks-service.de",
            "IDN": "aks-service.de",
            "Roid": "3831208518028_DOMAIN-KEYSYS",
            "Domain created date": "2009-07-23 15:08:08",
            "Domain created by": "external",
            "Domain updated date": "2019-04-10 16:21:12",
            "Domain updated by": "aksservicegmbh",
            "Domain registration expiration date": "2021-07-23 15:08:08",
            "Domain renewal date": "2021-07-22 15:08:08",
            "Domain zone": "de",
            "Transfer Date": "2009-07-23 15:08:08",
            "Auth Code": "",
            "Renewalmode": "DEFAULT",
            "Transfermode": "DEFAULT",
            "Status": "ACTIVE",
            "Nameserver": "ns5.kasserver.com;ns6.kasserver.com",
            "Admincontact": "P-TLL1757",
            "Techcontact": "P-TLL1757",
            "Billingcontact": "P-TLL1757",
            "Ownercontact": "P-TLL1757",
            "Domaintags": "aks",
            "Domaincomment": "",
            "Paid until": "2021-07-23 15:08:08",
            "": ""
            }]');
        Artisan::call('rrpproxy:import');
        $this->assertCount(1, Storage::disk('local')->allFiles('/rrpproxyexports'));

        Storage::put('/rrpproxyexports/rrpproxyexport_2010_01_01.json', 'test');
        Storage::put('/rrpproxyexports/rrpproxyexport_2009_12_31.json', 'test');
        Storage::put('/rrpproxyexports/rrpproxyexport_2010_01_02.json', 'test');
        Storage::put('/rrpproxyexports/rrpproxyexport_2010_01_03.json', 'test');
        Storage::put('/rrpproxyexports/rrpproxyexport_2010_01_04.json', 'test');
        $this->assertCount(6, Storage::disk('local')->allFiles('/rrpproxyexports'));

        Artisan::call('rrpproxy:import');
        $this->assertCount(5, Storage::disk('local')->allFiles('/rrpproxyexports'));

        Storage::disk('local')->assertMissing('/rrpproxyexports/rrpproxyexport_2009_12_31.json');
        Storage::disk('local')->assertExists('/rrpproxyexports/rrpproxyexport_2010_01_01.json');
        Storage::disk('local')->assertExists('/rrpproxyexports/rrpproxyexport_2010_01_02.json');
        Storage::disk('local')->assertExists('/rrpproxyexports/rrpproxyexport_2010_01_03.json');
        Storage::disk('local')->assertExists('/rrpproxyexports/rrpproxyexport_2010_01_04.json');
    }

    /** @test */
    public function new_entries_will_be_added_to_the_database()
    {
        Storage::fake('ftp');
        Storage::fake('local');
        Storage::disk('ftp')->put('/export/rrpproxyexport.json',
            '[{
            "Domain": "aks-service.de",
            "IDN": "aks-service.de",
            "Roid": "",
            "Domain created date": "2009-07-23 15:08:08",
            "Domain created by": "external",
            "Domain updated date": "2019-04-10 16:21:12",
            "Domain updated by": "aksservicegmbh",
            "Domain registration expiration date": "2021-07-23 15:08:08",
            "Domain renewal date": "2021-07-22 15:08:08",
            "Domain zone": "de",
            "Transfer Date": "2009-07-23 15:08:08",
            "Auth Code": "",
            "Renewalmode": "DEFAULT",
            "Transfermode": "DEFAULT",
            "Status": "ACTIVE",
            "Nameserver": "ns5.kasserver.com;ns6.kasserver.com",
            "Admincontact": "P-TLL1757",
            "Techcontact": "P-TLL1757",
            "Billingcontact": "P-TLL1757",
            "Ownercontact": "P-TLL1757",
            "Domaintags": "aks",
            "Domaincomment": "",
            "Paid until": "2021-07-23 15:08:08",
            "": ""
            },
            {
            "Domain": "aks-service.gmbh",
            "IDN": "aks-service.gmbh",
            "Roid": "",
            "Domain created date": "2016-10-11 11:31:29",
            "Domain created by": "dd24",
            "Domain updated date": "2020-11-15 13:18:37",
            "Domain updated by": "aksservicegmbh",
            "Domain registration expiration date": "2021-10-11 11:31:29",
            "Domain renewal date": "2021-11-15 11:31:29",
            "Domain zone": "gmbh",
            "Transfer Date": "",
            "Auth Code": "",
            "Renewalmode": "DEFAULT",
            "Transfermode": "DEFAULT",
            "Status": "ACTIVE",
            "Nameserver": "ns5.kasserver.com;ns6.kasserver.com",
            "Admincontact": "P-TLL1757",
            "Techcontact": "P-TLL1757",
            "Billingcontact": "P-TLL1757",
            "Ownercontact": "P-TLL1757",
            "Domaintags": "aks",
            "Domaincomment": "",
            "Paid until": "2021-10-11 11:31:29",
            "": ""
            }]');
        $this->assertDatabaseCount('domains', 0);
        $this->assertDatabaseCount('rrpproxy_entries', 0);
        Artisan::call('rrpproxy:import');
        $this->assertModelExists(Domain::find(1));
        $this->assertModelExists(Domain::find(2));
        $this->assertModelExists(RrpproxyEntry::find(1));
        $this->assertModelExists(RrpproxyEntry::find(2));
        $this->assertDatabaseCount('domains', 2);
        $this->assertDatabaseCount('rrpproxy_entries', 2);
    }

    /** @test */
    public function existing_entries_can_be_updated()
    {
        Storage::fake('ftp');
        Storage::fake('local');
        Storage::disk('ftp')->put('/export/rrpproxyexport.json',
            '[{
            "Domain": "aks-service.de",
            "IDN": "aks-service.de",
            "Roid": "",
            "Domain created date": "2009-07-23 15:08:08",
            "Domain created by": "external",
            "Domain updated date": "2019-04-10 16:21:12",
            "Domain updated by": "aksservicegmbh",
            "Domain registration expiration date": "2021-07-23 15:08:08",
            "Domain renewal date": "2021-07-22 15:08:08",
            "Domain zone": "de",
            "Transfer Date": "2009-07-23 15:08:08",
            "Auth Code": "",
            "Renewalmode": "DEFAULT",
            "Transfermode": "DEFAULT",
            "Status": "ACTIVE",
            "Nameserver": "ns5.kasserver.com;ns6.kasserver.com",
            "Admincontact": "P-TLL1757",
            "Techcontact": "P-TLL1757",
            "Billingcontact": "P-TLL1757",
            "Ownercontact": "P-TLL1757",
            "Domaintags": "aks",
            "Domaincomment": "",
            "Paid until": "2021-07-23 15:08:08",
            "": ""
            },
            {
            "Domain": "aks-service.gmbh",
            "IDN": "aks-service.gmbh",
            "Roid": "",
            "Domain created date": "2016-10-11 11:31:29",
            "Domain created by": "dd24",
            "Domain updated date": "2020-11-15 13:18:37",
            "Domain updated by": "aksservicegmbh",
            "Domain registration expiration date": "2021-10-11 11:31:29",
            "Domain renewal date": "2021-11-15 11:31:29",
            "Domain zone": "gmbh",
            "Transfer Date": "",
            "Auth Code": "",
            "Renewalmode": "DEFAULT",
            "Transfermode": "DEFAULT",
            "Status": "ACTIVE",
            "Nameserver": "ns5.kasserver.com;ns6.kasserver.com",
            "Admincontact": "P-TLL1757",
            "Techcontact": "P-TLL1757",
            "Billingcontact": "P-TLL1757",
            "Ownercontact": "P-TLL1757",
            "Domaintags": "aks",
            "Domaincomment": "",
            "Paid until": "2021-10-11 11:31:29",
            "": ""
            }]');
        Artisan::call('rrpproxy:import');

        $this->assertEquals('2021-07-23 15:08:08', RrpproxyEntry::find(1)->contract_end);
        $this->assertEquals('2021-10-11 11:31:29', RrpproxyEntry::find(2)->contract_end);
        $this->assertEquals('aks-service.de', Domain::find(1)->name);

        Storage::disk('ftp')->put('/export/rrpproxyexport.json',
            '[{
            "Domain": "aks-service.de",
            "IDN": "aks-service.de",
            "Roid": "",
            "Domain created date": "2009-07-23 15:08:08",
            "Domain created by": "external",
            "Domain updated date": "2019-04-10 16:21:12",
            "Domain updated by": "aksservicegmbh",
            "Domain registration expiration date": "2030-07-23 15:08:08",
            "Domain renewal date": "2021-07-22 15:08:08",
            "Domain zone": "de",
            "Transfer Date": "2009-07-23 15:08:08",
            "Auth Code": "",
            "Renewalmode": "DEFAULT",
            "Transfermode": "DEFAULT",
            "Status": "ACTIVE",
            "Nameserver": "ns5.kasserver.com;ns6.kasserver.com",
            "Admincontact": "P-TLL1757",
            "Techcontact": "P-TLL1757",
            "Billingcontact": "P-TLL1757",
            "Ownercontact": "P-TLL1757",
            "Domaintags": "aks",
            "Domaincomment": "",
            "Paid until": "2021-07-23 15:08:08",
            "": ""
            },
            {
            "Domain": "aks-service.gmbh",
            "IDN": "aks-service.gmbh",
            "Roid": "",
            "Domain created date": "2016-10-11 11:31:29",
            "Domain created by": "dd24",
            "Domain updated date": "2020-11-15 13:18:37",
            "Domain updated by": "aksservicegmbh",
            "Domain registration expiration date": "2031-10-11 11:31:29",
            "Domain renewal date": "2021-11-15 11:31:29",
            "Domain zone": "gmbh",
            "Transfer Date": "",
            "Auth Code": "",
            "Renewalmode": "DEFAULT",
            "Transfermode": "DEFAULT",
            "Status": "ACTIVE",
            "Nameserver": "ns5.kasserver.com;ns6.kasserver.com",
            "Admincontact": "P-TLL1757",
            "Techcontact": "P-TLL1757",
            "Billingcontact": "P-TLL1757",
            "Ownercontact": "P-TLL1757",
            "Domaintags": "aks",
            "Domaincomment": "",
            "Paid until": "2021-10-11 11:31:29",
            "": ""
            }]');
        Artisan::call('rrpproxy:import');

        $this->assertEquals('2030-07-23 15:08:08', RrpproxyEntry::find(1)->contract_end);
        $this->assertEquals('2031-10-11 11:31:29', RrpproxyEntry::find(2)->contract_end);
    }
}
