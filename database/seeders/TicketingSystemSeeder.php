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
use App\Models\Message;
use App\Models\StatusHistory;
use App\Enums\TickedStatus;
use App\Models\Ticked;
use Illuminate\Support\Facades\DB;

class TicketingSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {

            // 1️⃣ Crear personas
            $people = Person::factory()->count(30)->create();

            // 2️⃣ Asignar 20 como empleados
            $employees = $people->take(20)->map(function ($person) {
                return Employee::factory()->create([
                    'id' => $person->id
                ]);
            });

            // 3️⃣ Asignar 5 como agentes (pueden ser parte de empleados)
            $agents = $employees->take(5)->map(function ($employee) {
                return Agent::factory()->create([
                    'id' => $employee->id
                ]);
            });

            // 4️⃣ Crear usuarios para todos los empleados y agentes
            foreach ($people as $person) {
                User::factory()->create([
                    'person_id' => $person->id
                ]);
            }

            // 5️⃣ Crear categorías
            Category::factory()->count(5)->create();

            // 6️⃣ Crear prioridades fijas (mejor que factory)
            Priority::insert([
                ['level' => 'Low', 'weight' => 1, 'is_active' => true],
                ['level' => 'Medium', 'weight' => 2, 'is_active' => true],
                ['level' => 'High', 'weight' => 3, 'is_active' => true],
                ['level' => 'Critical', 'weight' => 4, 'is_active' => true],
            ]);

            // 7️⃣ Crear tickets
            $tickets = Ticked::factory()->count(40)->create([
                'reporter_id' => $employees->random()->id,
                'assigned_agent_id' => $agents->random()->id,
            ]);

            // 8️⃣ Crear historial coherente
            foreach ($tickets as $ticket) {

                StatusHistory::create([
                    'ticked_id' => $ticket->id,
                    'previous_status' => TickedStatus::OPEN->value,
                    'new_status' => $ticket->status->value,
                    'changed_by' => $agents->random()->id,
                    'changed_at' => now()->subDays(rand(1, 10)),
                ]);

                // 9️⃣ Crear mensajes del ticket
                Message::factory()
                    ->count(rand(2, 6))
                    ->create([
                        'ticked_id' => $ticket->id
                    ]);
            }
        });
    }
}
