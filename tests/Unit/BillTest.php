<?php

namespace Tests\Unit;

use App\Models\Bill;
use App\Models\Contract;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BillTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function bills_table_has_expected_columns()
    {
        $expectedColumns = [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'contract_id',
            'bill_number',
            'date',
        ];
        $this->assertTrue(Schema::hasColumns('bills', $expectedColumns));
        // check that no other columns are created
        $this->assertSame(Schema::getColumnListing('bills'), $expectedColumns);
    }

    /** @test */
    public function a_bill_belongs_to_a_contract()
    {
        /** @var Customer $customer */
        $customer = Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create(['customer_id' => $customer->id]);
        /** @var Bill $bill */
        $bill = Bill::factory()->create();

        $this->assertEquals(1, $bill->contract()->count());
        $this->assertInstanceOf(Contract::class, $bill->contract);
    }
}
