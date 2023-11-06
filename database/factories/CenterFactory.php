<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Center;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Center>
 */
class CenterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public $model = Center::class;
    public function definition(): array
    {
        $hospital = Hospital::get()->random();
        $name = fake()->company();
        return [
            'name'=>$name,
            'hospital_id'=>$hospital->id,
            'location'=>fake()->city(),
            'email'=>fake()->email(),
            'phone'=> '918075473813',
            'phone_number_id' => '123563487508047'
        ];
    }

    // public function configure(): static
    // {
    //     return $this->afterCreating(function (Center $c) {
    //         $hid = $c->hosptial->id;
    //         $u = User::factory()->create(
    //             [
    //                 'hospital_id' => $hid
    //             ]
    //             );
    //         $c->users()->save($u);
    //     });
    // }
}
