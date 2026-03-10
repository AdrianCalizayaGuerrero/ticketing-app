<?php

namespace Database\Seeders;

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
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SistemTickedSeeder extends Seeder
{
    public function run(): void
    {
        // ROLES
        $roles = Role::insert([
            ['name' => 'admin'],
            ['name' => 'agent'],
            ['name' => 'support'],
            ['name' => 'client'],
        ]);

        $adminRole   = Role::where('name', 'admin')->first();
        $agentRole   = Role::where('name', 'agent')->first();
        $supportRole = Role::where('name', 'support')->first();
        $clientRole  = Role::where('name', 'client')->first();

        // CATEGORÍAS
        Category::factory()->createMany([
            ['name' => 'Hardware',    'description' => 'Problemas de hardware',    'expected_response_time' => 2, 'expected_resolution_time' => 24],
            ['name' => 'Software',    'description' => 'Errores de software',      'expected_response_time' => 1, 'expected_resolution_time' => 12],
            ['name' => 'Red',         'description' => 'Problemas de conectividad','expected_response_time' => 1, 'expected_resolution_time' => 8],
            ['name' => 'Accesos',     'description' => 'Cuentas y permisos',       'expected_response_time' => 2, 'expected_resolution_time' => 24],
            ['name' => 'Facturación', 'description' => 'Problemas de facturación', 'expected_response_time' => 4, 'expected_resolution_time' => 48],
        ]);

        // PRIORIDADES
        Priority::factory()->createMany([
            ['level' => 'LOW',      'weight' => 1],
            ['level' => 'MEDIUM',   'weight' => 2],
            ['level' => 'HIGH',     'weight' => 3],
            ['level' => 'CRITICAL', 'weight' => 4],
        ]);

        // ADMIN
        $adminPerson = Person::factory()->create([
            'first_name' => 'Admin',
            'last_name'  => 'General',
            'email'      => 'admin@ticketapp.com'
        ]);

        User::create([
            'person_id' => $adminPerson->id,
            'username'  => 'admin',
            'password'  => Hash::make('password'),
            'role_id'   => $adminRole->id,
        ]);

        // AGENTS
        $agentPeople = Person::factory()->count(5)->create();

        $agents = $agentPeople->map(function ($person) use ($agentRole) {

            Employee::factory()->create(['id' => $person->id]);

            $agent = Agent::factory()->create(['id' => $person->id]);

            User::create([
                'person_id' => $person->id,
                'username'  => strtolower($person->first_name . '.' . $person->last_name),
                'password'  => Hash::make('password'),
                'role_id'   => $agentRole->id,
            ]);

            return $agent;
        });

        // SUPPORT
        $soportePeople = Person::factory()->count(5)->create();

        $soportePeople->each(function ($person) use ($supportRole) {

            Employee::factory()->create(['id' => $person->id]);

            User::create([
                'person_id' => $person->id,
                'username'  => strtolower($person->first_name . '.' . $person->last_name . '.s'),
                'password'  => Hash::make('password'),
                'role_id'   => $supportRole->id,
            ]);
        });

        // CLIENTES
        $clientePeople = Person::factory()->count(10)->create();

        $employees = $clientePeople->map(function ($person) use ($clientRole) {

            $emp = Employee::factory()->create(['id' => $person->id]);

            User::create([
                'person_id' => $person->id,
                'username'  => strtolower($person->first_name . '.' . $person->last_name . '.c'),
                'password'  => Hash::make('password'),
                'role_id'   => $clientRole->id,
            ]);

            return $emp;
        });

        // TICKETS
        $tickeds = Ticked::factory()->count(30)->create();

        $users = User::all();

        $tickeds->each(function ($ticked) use ($agents, $users) {

            StatusHistory::create([
                'ticked_id'       => $ticked->id,
                'previous_status' => TickedStatus::OPEN->value,
                'new_status'      => $ticked->getRawOriginal('status') ?? TickedStatus::OPEN->value,
                'changed_by'      => $agents->random()->id,
                'changed_at'      => now(),
                'comment'         => 'Registro inicial',
            ]);

            Message::factory()->count(rand(2,5))->create([
                'ticked_id' => $ticked->id,
                'author_id' => $users->random()->id,
            ]);
        });
    }
}
