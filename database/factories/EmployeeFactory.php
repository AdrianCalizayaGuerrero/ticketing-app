<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Employee;
use App\Models\Person;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    
        protected $model = Employee::class;

    public function definition(): array
    {
        $person = Person::first();

        return [
            // Toma una persona existente que no tenga employee aún
            'id' => Person::doesntHave('employee')->inRandomOrder()->first()?->id
                ?? Person::factory(), // fallback si no hay disponibles

            'department' => fake()->randomElement([
                'IT',
                'HR',
                'Finance',
                'Marketing',
                'Operations'
            ]),
            'position' => fake()->optional()->jobTitle(),
        ];
    }
    
}
