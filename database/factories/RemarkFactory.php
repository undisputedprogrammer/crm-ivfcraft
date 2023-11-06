<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\Remark;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Remark>
 */
class RemarkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Remark::class;

    public function definition(): array
    {
        return [
            'remarkable_type'=>Lead::class,
            'remarkable_id'=>Lead::all()->random(),

            'remark'=>fake()->sentence(),
            'user_id'=>User::all()->random()->id,
        ];
    }
}
