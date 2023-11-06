<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\LeadSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\AnswerSeeder;
use Database\Seeders\RemarkSeeder;
use Database\Seeders\MessageSeeder;
use Database\Seeders\QuestionSeeder;
use Ynotz\AccessControl\Models\Role;
use Database\Seeders\PermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            HospitalSeeder::class,
            // CenterSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            // DoctorSeeder::class,
            // QuestionSeeder::class,
            // LeadSeeder::class,
            // FollowupSeeder::class,
            // InternalChatSeeder::class,
            // RemarkSeeder::class,
            // AnswerSeeder::class,
            // MessageSeeder::class,

        ]);


        // \App\Models\User::factory(5)->create()->assignRole('agent');





    }
}
