<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Domain;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_forbid_an_unauthenticated_user_to_create_a_contract()
    {
        $response = $this->json('POST', 'create-contract', [
            'domain_id' => '1',
            'customer_id' => '1',
            'contract_number' => '12345',
        ]);
        $response->assertUnauthorized();
    }

    /** @test */
    public function it_should_allow_an_authenticated_user_to_create_a_contract()
    {
        Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        Customer::factory()->create();
        Domain::factory()->create();

        $response = $this->actingAs($user)
            ->json('POST', 'create-contract', [
                'domain_id' => '1',
                'customer_id' => '1',
                'contract_number' => '12345',
            ]);
        $response->assertRedirect();
    }

    /** @test */
    public function it_should_fail_validation_when_creating_a_conctract_without_contract_number()
    {
        Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        Customer::factory()->create();
        Domain::factory()->create();

        $response = $this->actingAs($user)
            ->json('POST', 'create-contract', [
                'domain_id' => '1',
                'customer_id' => '1',
            ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['contract_number']);
    }

    /** @test */
    public function it_should_create_a_contract_successfully()
    {
        Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        Customer::factory()->create();
        Domain::factory()->create();

        $this->actingAs($user)
            ->json('POST', 'create-contract', [
                'domain_id' => '1',
                'customer_id' => '1',
                'contract_number' => '12345',
            ]);

        $this->assertDatabaseHas('contracts', [
            'customer_id' => '1',
            'contract_number' => '12345'
        ]);
    }

    /** @test */
    public function it_should_update_a_contract_successfully_when_logged_in()
    {
        Group::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create();

        $this->assertDatabaseHas('contracts', [
            'contract_number' => $contract->contract_number
        ]);

        $response = $this->actingAs($user)
            ->json('POST', '/contract/update/1', [
                'newNumber' => '42',
            ]);
        $response->assertOk();
        $this->assertDatabaseHas('contracts', [
            'contract_number' => '42'
        ]);
    }

    /** @test */
    public function it_should_not_update_a_contract_successfully_when_not_logged_in()
    {
        Customer::factory()->create();
        /** @var Contract $contract */
        $contract = Contract::factory()->create();

        $this->assertDatabaseHas('contracts', [
            'contract_number' => $contract->contract_number
        ]);

        $response = $this->json('POST', '/contract/update/1', [
            'newNumber' => '42',
        ]);
        $response->assertUnauthorized();
        $this->assertDatabaseHas('contracts', [
            'contract_number' => $contract->contract_number
        ]);
    }
}
