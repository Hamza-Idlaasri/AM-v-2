<?php

namespace App\Http\Livewire\Config\Add\Boxes;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

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
        return DB::table('nagios_hosts')
        ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
        ->where('nagios_hosts.alias','box')
        ->get();
    }
}
