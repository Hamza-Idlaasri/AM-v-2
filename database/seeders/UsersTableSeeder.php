<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    protected $connection = 'am';

    public function run()
    {
        $agent = User::create([
            'name' => 'agent',
            'email' => 'agent@gmail.com',
            'notified' => 0,
            'phone_number' => '+212611111111',
            'password' => Hash::make('agent'),

        ]);

        $agent->attachRole('agent');
    }
}
