<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Remark;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lead::factory()->count(100)->create();

    }
}
