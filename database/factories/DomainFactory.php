<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Domain;
use Illuminate\Database\Eloquent\Factories\Factory;

class DomainFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Domain::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'contract_id' => $this->faker->randomElement(Contract::all()->pluck('id')->toArray()),
            'domain_name' => $this->faker->domainName(),
        ];
    }
}
