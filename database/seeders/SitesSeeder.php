<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UsersSite;
use App\Models\Sites;
use App\Models\User;

class SitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    protected $connection = 'am';
    
    public function run()
    {
        $users = User::all();
        
        foreach ($users as $user) {
            
            $site = UsersSite::create([
                'user_id' => $user->id,
                'site_name' => 'all',
                'current_site' => 'none',
            ]);

        }

        $create_all_sites = Sites::create([
            'site_name' => 'All'
        ]);
    }
}
