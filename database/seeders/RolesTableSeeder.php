<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    protected $connection = 'am';
    
    public function run()
    {
        $super_admin = Role::create([
            'name' => 'super_admin',
            'display_name' => 'super_admin',
            'description' => 'can do every thing',
        ]);

        $admin = Role::create([
            'name' => 'admin',
            'display_name' => 'admin',
            'description' => 'can see all sites',
        ]);
        
        $user = Role::create([
            'name' => 'user',
            'display_name' => 'user',
            'description' => 'just can see specific site',
        ]);
    }
}
