<?php

namespace App\Http\Livewire\Auth\User;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ChangePassword extends Component
{
    public $old_password;
    public $password;
    public $password_confirmation;

    protected $rules = [
        'old_password' => 'required|string|min:4|max:12|regex:/^[a-zA-Z0-9-_().@$=%&#+{}*ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]/',
        'password' => 'required|string|confirmed|min:4|max:12|regex:/^[a-zA-Z0-9-_().@$=%&#+{}*ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]/',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function changePWD()
    {
        // Validate user info
        $this->validate();
        
        if (Hash::check($this->old_password, auth()->user()->password)) {

            // Update user 
            auth()->user()->update([

                'password' => Hash::make($this->password),

            ]);

        } else {
            return back()->with('status','Invalid Old Password');
        }

        return redirect()->route('profile')->with('message','Passwor changed properly');
    }

    public function render()
    {
        return view('livewire.auth.user.change-password')
            ->extends('layouts.app')
            ->section('content');
    }
}
