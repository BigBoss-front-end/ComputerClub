<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('statuses')->insert([
            [
                'name' => 'Свободен',
                'alias' => 'free',
            ],
            [
                'name' => 'Занят',
                'alias' => 'occupied',
            ],
            [
                'name' => 'Забронирован',
                'alias' => 'booked',
            ],
            [
                'name' => 'В ремонте',
                'alias' => 'under_repair',
            ],
        ]);
    }
}
