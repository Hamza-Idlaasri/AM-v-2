<?php

namespace App\Http\Livewire\Config\Add\Equips;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UsersSite;

class Equip extends Component
{
    public $box_id;

    public function mount(Request $request)
    {
        $this->box_id = $request->id;
    }

    public function render()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $box = DB::table('nagios_hosts')
            ->where('alias', 'box')
            ->where('host_object_id', $this->box_id)
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varname','BOXTYPE')
            ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id as box_id','nagios_customvariables.varvalue as box_type')
            ->first();

        $inputs_used = DB::table('nagios_hosts')
            ->where('alias','box')
            // ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            // ->where('nagios_customvariables.varvalue',$site_name)
            ->where('nagios_hosts.host_object_id', $this->box_id)
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->orderBy('nagios_servicestatus.check_command')
            ->select('nagios_hosts.display_name as box_name','nagios_services.display_name as equip_name','nagios_servicestatus.check_command as input_nbr')
            ->get();

        if ($box->box_type == 'bf1010') {
            $inputs_not_used = ['bf1010_IN1','bf1010_IN2','bf1010_IN3','bf1010_IN4','bf1010_IN5','bf1010_IN6','bf1010_IN7','bf1010_IN8','bf1010_IN9','bf1010_IN10'];
        }
        
        if ($box->box_type == 'bf2300') {
            $inputs_not_used = ['bf2300_IN1','bf2300_IN2','bf2300_IN3','bf2300_IN4','bf2300_IN5','bf2300_IN6','bf2300_IN7','bf2300_IN8','bf2300_IN9','bf2300_IN10','bf2300_IN11','bf2300_IN12'];
        }

        foreach ($inputs_used as $input) {
        
            if(in_array($input->input_nbr, $inputs_not_used))
                unset($inputs_not_used[array_search($input->input_nbr, $inputs_not_used)]);

            $input->input_nbr = str_replace($box->box_type.'_','',$input->input_nbr);
        }

        $inputs_not_used = array_values($inputs_not_used);

        $inputs_not_used = str_replace($box->box_type.'_','',$inputs_not_used);

        return view('livewire.config.add.equips.equip')
            ->with(['inputs_used' => $inputs_used, 'inputs_not_used' => $inputs_not_used, 'box' => $box])
            ->extends('layouts.app')
            ->section('content');
    }
}
