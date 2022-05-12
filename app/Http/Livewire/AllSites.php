<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Sites;
use App\Models\UsersSite;

class AllSites extends Component
{
    public $site;

    protected $rules = [
        'site' => 'required|min:3|max:15|unique:am.all_sites,site_name|regex:/^[a-zA-Z][a-zA-Z0-9-_(). ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]/',
    ];

    public function addSite()
    {
        // Validate user info
        $this->validate();

        $add_site = Sites::create([
            'site_name' => $this->site,
        ]);

        $this->reset();
    }

    public function site($site_id)
    {
        // Get the site that the user selected
        $site_name = Sites::find($site_id)->site_name;

        // Get the current user id
        $user_id = auth()->user()->id;

        // Get the current site the user wnat to see
        $current_site = UsersSite::where('user_id', $user_id)->first();

        // Update current site
        $current_site->update([
            'current_site' => $site_name,
        ]);

        return redirect()->route('overview');

    }

    public function render()
    {
        $all_sites = Sites::all();

        return view('livewire.all-sites')
            ->with(['all_sites' => $all_sites])
            ->extends('layouts.template')
            ->section('content');
    }
}
