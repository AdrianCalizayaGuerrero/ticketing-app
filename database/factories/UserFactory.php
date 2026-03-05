<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Person;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'person_id' => Person::doesntHave('user')
                                ->inRandomOrder()
                                ->first()?->id
                            ?? Person::factory(),

            'username' => fake()->unique()->userName(),

            'password' => Hash::make('password'), // contraseña por defecto

            'remember_token' => fake()->optional()->regexify('[A-Za-z0-9]{10}'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    
}
