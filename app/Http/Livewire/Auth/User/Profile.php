<?php

namespace App\Http\Livewire\Auth\User;

use Livewire\Component;

class Profile extends Component
{
    public function render()
    {
        return view('livewire.auth.user.profile')
            ->extends('layouts.app')
            ->section('content');
    }
}
