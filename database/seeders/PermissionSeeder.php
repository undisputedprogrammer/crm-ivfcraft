<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ynotz\AccessControl\Models\Permission;

class PermissionSeeder extends Seeder
{
    private $permissions = [
        'Chat: Enter Own Hospital',
        'Chat: Enter Own Center',
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->permissions as $p) {
            Permission::create([
                'name' => $p
            ]);
        }
    }
}
