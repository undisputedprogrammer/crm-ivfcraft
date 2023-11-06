<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\User;
use App\Models\Center;
use App\Models\Hospital;
use App\Models\Appointment;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Lead::class;

    public function definition(): array
    {
        // $customer_segment = ['hot','warm','cold'];
        // $status = ['Created'];
        $hospital = Hospital::all()->random();

        $users = $hospital->users()->havingRoles(['admin'])->get();

        $center = $hospital->centers->random();
        $agents = $center->agents();
        $check = random_int(1,10);
        $isvalid = $check > 1;
        $isgenuine = $isvalid && $check > 2;

        $ag = count($agents) > 1 ? $agents[random_int(0, count($agents) - 1)] : $agents[0];
        return [
            'hospital_id' => $hospital->id,
            'center_id' => $center->id,
            'name'=>fake()->name(),
            'phone'=>8137033348,
            'email'=>fake()->email(),
            'city'=>fake()->city(),
            'is_valid'=> false,
            'is_genuine'=> false,
            'history'=>fake()->paragraph(),
            'customer_segment'=> null,
            'status'=> 'Created',
            'followup_created'=>false,
            'assigned_to'=> $ag->id,
            'created_by'=> $users[0],
            'created_at' => Carbon::now()->startOfMonth()->format('Y-m-d')
        ];
    }

    public function getAgent(){
        $user = User::all()->random();
        if($user->hasRole('agent')){
            return $user->id;
        }
        else{
            return $this->getAgent();
        }
    }

    // public function configure(): static
    // {
    //     return $this->afterCreating(function (Lead $lead) {
    //         $n = random_int(0, 1);
    //         if ($n == 1) {
    //             Appointment::factory()->create([
    //                 'lead_id' => $lead->id
    //             ]);
    //         }
    //     });
    // }
}
