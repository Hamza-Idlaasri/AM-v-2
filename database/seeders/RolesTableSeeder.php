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
        $agent = Role::create([
            'name' => 'agent',
            'display_name' => 'agent',
            'description' => 'it s an admin',
        ]);
        
        $superviseur = Role::create([
            'name' => 'superviseur',
            'display_name' => 'superviseur',
            'description' => 'just can see',
        ]);
    }
}
