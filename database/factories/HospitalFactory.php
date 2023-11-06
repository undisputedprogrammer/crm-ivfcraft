<?php

namespace Database\Factories;

use App\Models\Hospital;
use Illuminate\Support\Str;
use Illuminate\Support\Testing\Fakes\Fake;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hospital>
 */
class HospitalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public $model = Hospital::class;
    public function definition(): array
    {
        $name = fake()->company();
        return [
            'name'=> $name,
            'code'=> Str::snake($name),
            'ho_location'=>fake()->city(),
            'email'=>fake()->email(),
            'phone'=>fake()->phoneNumber(),
            'authkey' => '405736ABdKIenjmHR6501a01aP1',
            'bearer_token' => 'EAAMk25QApioBO6SvBdiMsr5HQPmivzZA5r50OwmoQqdEGVegEk4pgNIZAWJZAWg05WM1ZCqbod3TIuI3zUrXFVykJg2BkM5UVGha67SpVkDdeCz1vF9yg6Mb6JvFtY9GzsKtZBpKmMMMtZBo0otRnc5mlzszAHYtCUtfw21vwz086LuR1YaJdVYwthNTZBCgkFpp2ZA8R2I2TgX9',
            'main_cols' => json_encode([])
        ];
    }
}
