<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Hardware',
                'Software',
                'Network',
                'Access Request',
                'Maintenance',
                'Security Incident',
                'Technical Support'
            ]),
            'description' => fake()->optional()->sentence(12),
            'expected_response_time' => fake()->numberBetween(1, 24), // horas
            'expected_resolution_time' => fake()->optional()->numberBetween(4, 72),
            'is_active' => fake()->boolean(90), // 90% activas
        ];
    }

    /**
     * Estado: categoría inactiva
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
