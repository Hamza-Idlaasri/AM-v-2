<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notif;
use Illuminate\Support\Facades\DB;

class NotifsCheck extends Seeder
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

            $notifs = Notif::create([
                'user_id' => $user->id,
                'hosts' => 0,
                'services' => 0,
                'boxes' => 0,
                'equips' => 0,
            ]);

        }

    }

}
