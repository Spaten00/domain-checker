<?php

namespace Database\Factories;

use App\Models\Bill;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

class BillFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bill::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'contract_id' => $this->faker->randomElement(Contract::all()->pluck('id')->toArray()),
            'bill_number' => $this->faker->numberBetween(),
            'date' => $this->faker->dateTime(),
        ];
    }
}
