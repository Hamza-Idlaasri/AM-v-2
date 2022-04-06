<?php

namespace App\Http\Livewire\Config\Add\Equips;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Equip extends Component
{
    public $box_id;

    public function mount(Request $request)
    {
        $this->box_id = $request->id;
    }

    public function render()
    {
        $box = DB::table('nagios_hosts')
            ->where('alias', 'box')
            ->where('host_object_id', $this->box_id)
            ->select('nagios_hosts.display_name as box_name', 'nagios_hosts.host_object_id as box_id')
            ->get();

        $inputs_used = DB::table('nagios_hosts')
            ->where('alias','box')
            ->where('nagios_hosts.host_object_id', $this->box_id)
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->orderBy('nagios_servicestatus.check_command')
            ->select('nagios_hosts.display_name as box_name','nagios_services.display_name as equip_name','nagios_servicestatus.check_command as input_nbr')
            ->get();

        $inputs_not_used = ['IN1','IN2','IN3','IN4','IN5','IN6','IN7','IN8','IN9','IN10'];

        foreach ($inputs_used as $input) {
        
            if(in_array($input->input_nbr, $inputs_not_used))
                unset($inputs_not_used[array_search($input->input_nbr, $inputs_not_used)]);

        }

        $inputs_not_used = array_values($inputs_not_used);

        return view('livewire.config.add.equips.equip')
        ->with(['inputs_used' => $inputs_used, 'inputs_not_used' => $inputs_not_used, 'box' => $box])
        ->extends('layouts.app')
        ->section('content');
    }
}
