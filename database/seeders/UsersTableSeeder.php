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
        $super_admin = User::create([
            'name' => 'super_admin',
            'email' => 'super_admin@gmail.com',
            'notified' => 0,
            'phone_number' => '+212611111111',
            'password' => Hash::make('super_admin'),

        ]);

        $super_admin->attachRole('super_admin');
      
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'notified' => 0,
            'phone_number' => '+212611111111',
            'password' => Hash::make('admin'),

        ]);

        $admin->attachRole('admin');
    }
}
