<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Person;
use App\Models\Employee;
use App\Models\Agent;
use App\Models\User;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Ticked;
use App\Models\Message;
use App\Models\StatusHistory;

use App\Enums\TickedStatus;


class SistemTickedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        /*
        |-----------------------------
        | Categories
        |-----------------------------
        */

        Category::factory()->createMany([
            [
                'name' => 'Hardware',
                'description' => 'Hardware issues',
                'expected_response_time' => 2,
                'expected_resolution_time' => 24
            ],
            [
                'name' => 'Software',
                'description' => 'Software errors or bugs',
                'expected_response_time' => 1,
                'expected_resolution_time' => 12
            ],
            [
                'name' => 'Network',
                'description' => 'Network connectivity issues',
                'expected_response_time' => 1,
                'expected_resolution_time' => 8
            ],
            [
                'name' => 'Access',
                'description' => 'Account or permissions',
                'expected_response_time' => 2,
                'expected_resolution_time' => 24
            ]
        ]);

        /*
        |-----------------------------
        | Priorities
        |-----------------------------
        */

        Priority::factory()->createMany([
            ['level' => 'LOW', 'weight' => 1],
            ['level' => 'MEDIUM', 'weight' => 2],
            ['level' => 'HIGH', 'weight' => 3],
            ['level' => 'CRITICAL', 'weight' => 4],
        ]);

        /*
        |-----------------------------
        | People
        |-----------------------------
        */

        $people = Person::factory()->count(20)->create();

        /*
        |-----------------------------
        | Employees
        |-----------------------------
        */

        $employees = $people->take(12)->map(function ($person) {
            return Employee::factory()->create([
                'id' => $person->id
            ]);
        });

        /*
        |-----------------------------
        | Agents
        |-----------------------------
        */

        $agents = $people->skip(12)->take(5)->map(function ($person) {
            return Agent::factory()->create([
                'id' => $person->id
            ]);
        });

        /*
        |-----------------------------
        | Users
        |-----------------------------
        */

        $people->each(function ($person) {
            User::factory()->create([
                'person_id' => $person->id
            ]);
        });

        /*
        |-----------------------------
        | Tickeds
        |-----------------------------
        */

        $tickeds = Ticked::factory()
            ->count(30)
            ->create();

        /*
        |-----------------------------
        | Status Histories
        |-----------------------------
        */

        $tickeds->each(function ($ticked) use ($agents) {

            StatusHistory::create([
                'ticked_id' => $ticked->id,
                'previous_status' => TickedStatus::OPEN->value,
                'new_status' => $ticked->status,
                'changed_by' => $agents->random()->id,
                'changed_at' => now(),
                'comment' => fake()->sentence()
            ]);
        });

        /*
        |-----------------------------
        | Messages
        |-----------------------------
        */

        $users = User::all();

        $tickeds->each(function ($ticked) use ($users) {

            Message::factory()
                ->count(rand(2, 5))
                ->create([
                    'ticked_id' => $ticked->id,
                    'author_id' => $users->random()->id
                ]);
        });
    }
}
