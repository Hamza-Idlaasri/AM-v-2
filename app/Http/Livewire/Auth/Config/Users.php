<?php

namespace App\Http\Livewire\Auth\Config;

use Livewire\Component;
use App\Models\User;
use App\Models\Notif;
use Illuminate\Support\Facades\DB;

class Users extends Component
{
    public function upgradeUser($transitionTo,$user_id)
    {
        $user = User::find($user_id);

        switch ($transitionTo) {

            case 'user':
                    $user->detachRole('admin');
                    $user->attachRole('user');

                    $message = $user->name.' now its a normal user';

                break;

            case 'admin':
                    $user->detachRole('user');
                    $user->attachRole('admin');

                    $message = $user->name.' now its admin';

                break;

        }

        session()->flash('message', $message);
    }

    public function deleteUser($user_id)
    {
        // Remove notifs checked by this user
        $notifs_checked = Notif::where('user_id',$user_id)->firstOrFail();
        $notifs_checked->delete();

        // Delete the user
        $user = User::find($user_id);
        $user->delete();

        // Remove his Role from roles table
        $remove_role = DB::connection('am')->table('role_user')->where('user_id',$user_id)->delete();

        session()->flash('message', $user->name.' user is deleted');

    }

    public function notifiedUser($notified,$user_id)
    {
        $user = User::find($user_id);

        switch ($notified) {
            
            case 'notified':
                    $user->update([
                        'notified' => 0
                    ]);

                    $message = $user->name.' not notified anymore';

                break;

            case 'not_notified':
                    $user->update([
                        'notified' => 1
                    ]);

                    $message = $user->name.' will get Email/SMS notifications';

                break;
   
        }
        
        session()->flash('message', $message);
    }

    public function render()
    {
        $users = User::all()->except(1);

        return view('livewire.auth.config.users')
            ->with(['users' => $users])
            ->extends('layouts.app')
            ->section('content');
    }
}
