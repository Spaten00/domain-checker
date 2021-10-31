<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Domain;
use App\Models\TanssEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class TanssEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TanssEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'domain_id' => $this->faker->randomElement(Domain::all()->pluck('id')->toArray()),
            'customer_id' => $this->faker->randomElement(Customer::all()->pluck('id')->toArray()),
            'external_id' => $this->faker->randomDigitNotNull(),
            'provider_name' => $this->faker->company(),
            'contract_start' => $this->faker->dateTime(),
            'contract_end' => $this->faker->dateTime(),
        ];
    }
}
