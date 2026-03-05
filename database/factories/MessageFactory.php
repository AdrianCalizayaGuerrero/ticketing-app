<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Message;
use App\Models\Ticked;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            // UUID se genera automáticamente si usas HasUuids
            'content' => fake()->paragraph(),

            'is_internal' => fake()->boolean(30), // 30% internos

            'ticked_id' => Ticked::inRandomOrder()->first()?->id
                            ?? Ticked::factory(),

            'author_id' => User::inRandomOrder()->first()?->id
                            ?? User::factory(),
        ];
    }
    /**
     * Estado: mensaje interno
     */
    public function internal(): static
    {
        return $this->state(fn () => [
            'is_internal' => true,
        ]);
    }

    /**
     * Estado: mensaje público
     */
    public function public(): static
    {
        return $this->state(fn () => [
            'is_internal' => false,
        ]);
    }
}
