<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Domain;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_forbid_an_unauthenticated_user_to_create_a_bill()
    {
        $response = $this->json('POST', 'create-bill', [
            'contract_id' => '1',
            'bill_number' => '1',
            'date' => '2020-10-15',
        ]);
        $response->assertUnauthorized();
    }

    /** @test */
    public function it_should_allow_an_authenticated_user_to_create_a_bill()
    {
        Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        Customer::factory()->create();
        Domain::factory()->create();
        Contract::factory()->create();

        $response = $this->actingAs($user)
            ->json('POST', 'create-bill', [
                'contract_id' => '1',
                'bill_number' => '1',
                'date' => '2020-10-15',
            ]);
        $response->assertRedirect();
    }

    /** @test */
    public function it_should_fail_validation_when_creating_a_conctract_without_bill_number()
    {
        Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        Customer::factory()->create();
        Domain::factory()->create();
        Contract::factory()->create();

        $response = $this->actingAs($user)
            ->json('POST', 'create-bill', [
                'contract_id' => '1',
                'date' => '2020-10-15',
            ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['bill_number']);
    }

    /** @test */
    public function it_should_create_a_bill_successfully()
    {
        Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        Customer::factory()->create();
        Domain::factory()->create();
        Contract::factory()->create();

        $this->actingAs($user)
            ->json('POST', 'create-bill', [
                'contract_id' => '1',
                'bill_number' => '42',
                'date' => '2020-10-15',
            ]);

        $this->assertDatabaseHas('bills', [
            'contract_id' => '1',
            'bill_number' => '42',
            'date' => '2020-10-15'
        ]);
    }

    /** @test */
    public function it_should_update_a_bill_successfully_when_logged_in()
    {
        Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        Customer::factory()->create();
        Contract::factory()->create();
        /** @var Bill $bill */
        $bill = Bill::factory()->create();

        $this->assertDatabaseHas('bills', [
            'bill_number' => $bill->bill_number
        ]);

        $response = $this->actingAs($user)
            ->json('POST', '/bill/update/1', [
                'newNumber' => '42',
            ]);
        $response->assertOk();
        $this->assertDatabaseHas('bills', [
            'bill_number' => '42'
        ]);
    }

    /** @test */
    public function it_should_not_update_a_bill_successfully_when_not_logged_in()
    {
        Customer::factory()->create();
        Contract::factory()->create();
        /** @var Bill $bill */
        $bill = Bill::factory()->create();

        $this->assertDatabaseHas('bills', [
            'bill_number' => $bill->bill_number
        ]);

        $response = $this->json('POST', '/bill/update/1', [
            'newNumber' => '42',
        ]);
        $response->assertUnauthorized();
        $this->assertDatabaseHas('bills', [
            'bill_number' => $bill->bill_number
        ]);
    }
}
