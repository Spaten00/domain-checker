<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Domain;
use App\Models\TanssEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function customers_table_has_expected_columns()
    {
        $expectedColumns = [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'name',
        ];

        $this->assertTrue(Schema::hasColumns('customers', $expectedColumns));
        // check that no other columns are created
        $this->assertSame(Schema::getColumnListing('customers'), $expectedColumns);
    }

    /** @test */
    public function a_customer_has_many_contracts()
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        $contract = Contract::factory()->create(['customer_id' => $customer->id]);

        $this->assertTrue($customer->contracts->contains($contract));
        $this->assertEquals(1, $customer->contracts->count());
        $this->assertInstanceOf(Collection::class, $customer->contracts);

        $customer->contracts->add(Contract::factory()->create());
        $this->assertEquals(2, $customer->contracts->count());
    }

    /** @test */
    public function a_customer_has_many_tanss_entries()
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        $domain = Domain::factory()->create();
        $tanssEntry = TanssEntry::factory()->create();

        $this->assertTrue($customer->tanssEntries->contains($tanssEntry));
        $this->assertEquals(1, $customer->tanssEntries->count());
        $this->assertInstanceOf(Collection::class, $customer->tanssEntries);

        $customer->tanssEntries->add(TanssEntry::factory()->create());
        $this->assertEquals(2, $customer->tanssEntries->count());
    }
}
