<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Logout extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function logout()
    {
        auth()->logout();

        return redirect()->route('login');
    }
}
