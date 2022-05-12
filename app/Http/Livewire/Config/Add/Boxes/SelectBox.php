<?php

namespace App\Http\Livewire\Config\Add\Boxes;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class SelectBox extends Component
{
    public function render()
    {
        $boxes = $this->getBoxes();

        return view('livewire.config.add.boxes.select-box')
        ->with(['boxes' => $boxes])
        ->extends('layouts.app')
        ->section('content');
    }

    public function getBoxes()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_hosts')
        ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
        ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
        ->where('nagios_hosts.alias','box')
        ->where('nagios_customvariables.varvalue',$site_name)
        ->get();
    }
}
