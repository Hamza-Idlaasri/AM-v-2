<?php

namespace App\Http\Livewire\Config\Edit;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Box extends Component
{
    public $box_id;

    public function mount(Request $request)
    {
        $this->box_id = $request->id;
    }

    public function render()
    {
        $box = $this->getBox($this->box_id);

        $parent_boxes = $this->getParentsBox($this->box_id);

        $all_boxes = $this->getAllBoxes($this->box_id);

        return view('livewire.config.edit.box')
            ->with(['box' => $box, 'parent_boxes' => $parent_boxes, 'all_boxes' => $all_boxes])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getBox($box_id)
    {
        return DB::table('nagios_hosts')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->where('nagios_hosts.alias','box')
            ->where('nagios_hosts.host_object_id', $box_id)
            ->get();
    }

    public function getParentsBox($box_id)
    {
        return DB::table('nagios_hosts')
        ->join('nagios_host_parenthosts','nagios_hosts.host_id','=','nagios_host_parenthosts.host_id')
        ->select('nagios_hosts.display_name as box_name','nagios_host_parenthosts.*')
        ->where('nagios_host_parenthosts.host_id','=', $box_id)
        ->get();
    }

    public function getAllBoxes($box_id)
    {
        return DB::table('nagios_hosts')
            ->where('alias','box')
            ->where('host_id','!=',$box_id)
            ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id')
            ->get();
    }
}
