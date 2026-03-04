<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void {
    \App\Models\Ticket::create([
        'codigo_ticket' => 'TK-001',
        'asunto' => 'Error al iniciar sesión',
        'descripcion' => 'El usuario no puede entrar a la plataforma desde la mañana.',
        'estado' => 'abierto',
        'prioridad' => 'alta'
    ]);
}
}
