<?php

namespace Database\Factories;

use App\Models\Domain;
use App\Models\RrpproxyEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class RrpproxyEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RrpproxyEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'domain_id' => $this->faker->randomElement(Domain::all()->pluck('id')->toArray()),
            'contract_start' => $this->faker->dateTime(),
            'contract_end' => $this->faker->dateTime(),
            'contract_renewal' => $this->faker->dateTime(),
        ];
    }
}
