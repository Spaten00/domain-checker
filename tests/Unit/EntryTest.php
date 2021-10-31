<?php

namespace Tests\Unit;

use App\Models\Entry;
use PHPUnit\Framework\TestCase;

class EntryTest extends TestCase
{
    /** @test */
    public function fillable_can_be_mass_assigned()
    {
        $stub = $this->getMockForAbstractClass(Entry::class);

        $this->assertTrue($stub->isFillable('domain_id'));
        $this->assertTrue($stub->isFillable('contract_start'));
        $this->assertTrue($stub->isFillable('contract_end'));
    }

    /** @test */
    public function a_valid_date_can_be_checked()
    {
        $this->assertEquals('1980-00-00', Entry::getValidDate("1980-00-00"));
        $this->assertEquals('2020-01-31', Entry::getValidDate("2020-01-31"));
        $this->assertEquals(null, Entry::getValidDate("2020-01-32"));
        $this->assertEquals(null, Entry::getValidDate("0000-00-00"));
    }

    /** @test */
    public function it_can_check_if_the_contract_is_expired()
    {
        $stub = $this->getMockForAbstractClass(Entry::class);

        // Before now
        $stub->contract_end = now()->subDay();
        $this->assertTrue($stub->isExpired());

        $stub->contract_end = now()->subCentury();
        $this->assertTrue($stub->isExpired());

        // After now
        $stub->contract_end = now()->addDay();
        $this->assertFalse($stub->isExpired());

        $stub->contract_end = now()->addCentury();
        $this->assertFalse($stub->isExpired());
    }

    /** @test */
    public function it_can_check_if_the_contract_will_expire_soon()
    {
        $stub = $this->getMockForAbstractClass(Entry::class);

        $stub->contract_end = now()->subDay();
        $this->assertTrue($stub->willExpireSoon());

        $stub->contract_end = now()->subCentury();
        $this->assertTrue($stub->willExpireSoon());

        $stub->contract_end = now()->addDays(30);
        $this->assertTrue($stub->willExpireSoon());

        $stub->contract_end = now()->addDays(31);
        $this->assertFalse($stub->willExpireSoon());

        $stub->contract_end = now()->addCentury();
        $this->assertFalse($stub->willExpireSoon());
    }
}
