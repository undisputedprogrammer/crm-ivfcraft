<?php

namespace Database\Seeders;

use App\Models\Followup;
use App\Models\Lead;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FollowupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Lead::all() as $l) {
            $check = random_int(1, 10);
            if ($check > 1) {
                Followup::factory()->create(
                    ['lead_id' => $l->id]
                );
            }
        }
    }
}
