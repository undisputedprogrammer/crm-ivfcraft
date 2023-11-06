<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Center;
use App\Models\Hospital;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HospitalSeeder extends Seeder
{
    private $hospitals = [
        [
            'name' => 'Craft',
            'code' => 'craft',
            'ho_location' => 'Kodungalloor',
            'email' => 'info@craftivf.com',
            'phone' => '1234512345',
            'main_cols' => [
                'name' => 'full_name',
                'email' => 'email',
                'phone' => 'phone_number',
                'city' => 'city',
                'access_token' => 'EAAMk25QApioBO6SvBdiMsr5HQPmivzZA5r50OwmoQqdEGVegEk4pgNIZAWJZAWg05WM1ZCqbod3TIuI3zUrXFVykJg2BkM5UVGha67SpVkDdeCz1vF9yg6Mb6JvFtY9GzsKtZBpKmMMMtZBo0otRnc5mlzszAHYtCUtfw21vwz086LuR1YaJdVYwthNTZBCgkFpp2ZA8R2I2TgX9'
            ],
            'centers' => [
                'Craft_Kodungallur',
                'Craft_Kochi'
            ]
        ],
        [
            'name' => 'AR',
            'code' => 'ar',
            'ho_location' => 'Kodungalloor',
            'email' => 'info@armedicentre.com',
            'phone' => '1234512345',
            'main_cols' => [
                'name' => 'name',
                'email' => 'email',
                'phone' => 'contact_number',
                'city' => 'city',
                'access_token' => 'EAAMk25QApioBO6SvBdiMsr5HQPmivzZA5r50OwmoQqdEGVegEk4pgNIZAWJZAWg05WM1ZCqbod3TIuI3zUrXFVykJg2BkM5UVGha67SpVkDdeCz1vF9yg6Mb6JvFtY9GzsKtZBpKmMMMtZBo0otRnc5mlzszAHYtCUtfw21vwz086LuR1YaJdVYwthNTZBCgkFpp2ZA8R2I2TgX9'
            ],
            'centers' => [
                'AR_Kodungallur'
            ]
        ],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->hospitals as $h) {
            $h['main_cols'] = $h['main_cols'];
            $centers = $h['centers'];
            unset($h['centers']);
            $hosp = Hospital::factory()->create($h);
            foreach ($centers as $c) {
                $cen = Center::factory()->create([
                    'name' => $c,
                    'hospital_id' => $hosp->id,
                ]);
                $cen->users()->save(User::factory()->create([
                    'hospital_id' => $hosp->id
                ]));
            }
        }
    }
}
