<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            'Cabut',
            'Molding/perapian',
            'Pemanasan',
            'Admin',
            'Grading / Bk',
            'Packing',
            'Kebersihan',
            'QA',
            'Supervisor',
        ];
        foreach($divisions as $division) {
            \App\Models\Divisi::create([
                'divisi' => $division,
            ]);
        }
    }
}
