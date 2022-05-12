<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Login extends Component
{
    public $name;
    public $password;
    public $remember;

    protected $rules = [
        'name' => 'required|min:3|max:15|regex:/^[a-zA-Z][a-zA-Z0-9-_(). ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]/',
        'password' => 'required|string|min:4|max:12|regex:/^[a-zA-Z0-9-_().@$=%&#+{}*ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]/',
    ];

    // public function updated($propertyName)
    // {
    //     $this->validateOnly($propertyName);
    // }

    public function login()
    {
        // Validate user info
        $this->validate();

        // Login 
        if(!auth()->attempt(array('name' => $this->name, 'password' => $this->password), $this->remember)){ 
            return back()->with('status','Invalid login details');
        }

        if (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin')) 
            return redirect()->route('sites');    
        
        return redirect()->route('overview');     
        
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->extends('layouts.auth')
            ->section('content');
    }
}
