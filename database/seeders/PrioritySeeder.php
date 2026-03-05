<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Priority;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Priority::insert([
            ['level' => 'Low', 'weight' => 1, 'is_active' => true],
            ['level' => 'Medium', 'weight' => 2, 'is_active' => true],
            ['level' => 'High', 'weight' => 3, 'is_active' => true],
            ['level' => 'Critical', 'weight' => 4, 'is_active' => true],
        ]);
    }
}
