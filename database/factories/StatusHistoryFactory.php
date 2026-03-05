<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\StatusHistory;
use App\Models\Ticked;
use App\Models\Agent;
use App\Enums\TickedStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StatusHistory>
 */
class StatusHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = StatusHistory::class;

    public function definition(): array
    {
        $statuses = TickedStatus::cases();

        $previous = fake()->randomElement($statuses);

        // Elegir un estado diferente al anterior
        $new = fake()->randomElement(
            array_filter($statuses, fn ($status) => $status !== $previous)
        );

        return [
            'ticked_id' => Ticked::inRandomOrder()->first()?->id
                            ?? Ticked::factory(),

            'previous_status' => $previous->value,

            'new_status' => $new->value,

            'comment' => fake()->optional()->sentence(),

            'changed_by' => Agent::inRandomOrder()->first()?->id
                            ?? Agent::factory(),

            'changed_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
