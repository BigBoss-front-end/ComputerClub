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
                'color' => 'green'
            ],
            [
                'name' => 'Занят',
                'alias' => 'occupied',
                'color' => 'red'
            ],
            [
                'name' => 'Забронирован',
                'alias' => 'booked',
                'color' => 'gray'
            ],
            [
                'name' => 'В ремонте',
                'alias' => 'under_repair',
                'color' => 'black'
            ],
        ]);
    }
}
