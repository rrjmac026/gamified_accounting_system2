<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'first_name' => 'System',
            'last_name'  => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'role' => 'admin',

            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => encrypt(json_encode([
                'abcd-efgh-1234',
                'ijkl-mnop-5678',
            ])),
        ]);


        \App\Models\Student::factory(20)->create();
        \App\Models\Course::factory(5)->create();
        \App\Models\Instructor::factory(5)->create();
        \App\Models\Subject::factory(10)->create();
        
    }
}
