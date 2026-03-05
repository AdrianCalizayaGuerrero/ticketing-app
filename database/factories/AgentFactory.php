<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Agent;
use App\Models\Person;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agent>
 */
class AgentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Agent::class;

    public function definition(): array
    {
        return [
            'id' => Person::doesntHave('agent')
                        ->inRandomOrder()
                        ->first()?->id
                    ?? Person::factory(),

            'employee_code' => 'AG-' . fake()->unique()->numberBetween(1000, 9999),

            'is_available' => fake()->boolean(80), // 80% disponibles
        ];
    }
}
