<?php

namespace App\Http\Livewire\Grid;

use Livewire\Component;
use App\Models\Sites;
use App\Models\UsersSite;

class Sidebar extends Component
{
    public function changeSite($site_id)
    {
        // Get the site that the user selected
        $site_name = Sites::find($site_id)->site_name;

        // Get the current user id
        $user_id = auth()->user()->id;

        // Get the current site the user wnat to see
        $current_site = UsersSite::where('user_id', $user_id)->first();

        $current_site->update([
            'current_site' => $site_name,
        ]);

        return redirect()->route('overview');    
    }

    public function render()
    {
        $current_site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return view('livewire.grid.sidebar')->with(['sites' => Sites::all(),'current_site_name' => $current_site_name]);
    }

}
