<?php

namespace App\Http\Livewire\Auth\Config;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Users extends Component
{
    public function upgradeUser($transitionTo,$user_id)
    {
        $user = User::find($user_id);

        switch ($transitionTo) {

            case 'superviseur':
                    $user->detachRole('agent');
                    $user->attachRole('superviseur');

                    $message = $user->name.' now its superviseur';

                break;

            case 'agent':
                    $user->detachRole('superviseur');
                    $user->attachRole('agent');

                    $message = $user->name.' now its agent';

                break;

        }

        session()->flash('message', $message);
    }

    public function deleteUser($user_id)
    {
        $user = User::find($user_id);
        $user->delete();

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
