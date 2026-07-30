<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PwaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id'         => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

        ];
        DB::table('pwas')->insert($data);
    }
}
