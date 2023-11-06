<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ynotz\AccessControl\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    private $roles = [
        'admin' => [
            'Chat: Enter Own Hospital'
        ],
        'agent' => [
            'Chat: Enter Own Center'
        ]
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->roles as $r => $permissions) {
            /**
             * @var Role
             */
            $role = Role::create([
                'name' => $r
            ]);
            foreach ($permissions as $p) {
                $role->assignPermissions($p);
            }
        }
    }
}
