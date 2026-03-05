<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Ticked;
use App\Models\Employee;
use App\Models\Agent;
use App\Models\Category;
use App\Models\Priority;
use App\Enums\TickedStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticked>
 */
class TickedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Ticked::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),

            'reference_code' => 'TCK-' . strtoupper(Str::random(8)),

            'subject' => fake()->sentence(6),

            'description' => fake()->paragraph(3),

            'status' => fake()->randomElement(TickedStatus::cases())->value,

            'reporter_id' => Employee::inRandomOrder()->first()?->id
                            ?? Employee::factory(),

            'assigned_agent_id' => fake()->boolean(70)
                ? Agent::inRandomOrder()->first()?->id
                : null,

            'category_id' => Category::inRandomOrder()->first()?->id
                            ?? Category::factory(),

            'priority_id' => Priority::inRandomOrder()->first()?->id
                            ?? Priority::factory(),
        ];
    }
}
