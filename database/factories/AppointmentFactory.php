<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lead_id' => Lead::all()->random()->id,
            'doctor_id' => Doctor::all()->random()->id,
            'appointment_date' => Carbon::today()->addDays(random_int(3, 7))->format('Y-m-d')
        ];
    }
}
