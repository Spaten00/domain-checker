<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Hosting;
use Illuminate\Database\Eloquent\Factories\Factory;

class HostingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Hosting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'contract_id' => $this->faker->randomElement(Contract::all()->pluck('id')->toArray()),
            'hosting_type' => $this->faker->mimeType(),
        ];
    }
}
