<?php

namespace App\Http\Livewire\Auth\User;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class EditUserInfo extends Component
{
    public $username;
    public $email;
    public $phone_number;
    public $notified = [];

    protected $rules = [
        'username' => 'required|min:3|max:15|regex:/^[a-zA-Z][a-zA-Z0-9-_(). ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]/',
        'email' => 'required|email|max:100|',
        'phone_number' => 'required|regex:/[0-9]{9}/',
    ];

    public function mount()
    {
        $user = auth()->user();

        $this->username = $user->name;
        $this->email = $user->email;
        $this->phone_number = substr($user->phone_number,4,13);
        $this->notified = $user->notified;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    
    public function edit()
    {
        // Validate user info
        $this->validate();

        // Update user 
        auth()->user()->update([
            'name' => $this->username,
            'email' => $this->email,
            'phone_number' => '+212'.$this->phone_number,
        ]);

        if ($this->notified) {
            auth()->user()->update([
                'notified' => 1
            ]);
            
        } else {
            auth()->user()->update([
                'notified' => 0
            ]);
        }

        return redirect()->route('profile');

    }

    public function render()
    {
        return view('livewire.auth.user.edit-user-info')
            ->with(['user_id' => auth()->user()->id])
            ->extends('layouts.app')
            ->section('content');
    }
}