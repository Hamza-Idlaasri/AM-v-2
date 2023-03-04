<?php

namespace App\Http\Livewire\Config\Add\Equips;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UsersSite;
use App\Models\Sites;

class Equip extends Component
{
    public $box_id;

    public function mount(Request $request)
    {
        $this->box_id = $request->id;
    }

    public function render()
    {
        $sites = Sites::all()->except(1);

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
            $inputs_not_used = ['1','2','3','4','5','6','7','8','9','10'];
        }
        
        if ($box->box_type == 'bf2300') {
            $inputs_not_used = ['1','2','3','4','5','6','7','8','9','10','11','12'];
        }

        foreach ($inputs_used as $input) {

            $input->input_nbr = substr($input->input_nbr,9,-2);
        
            if(in_array($input->input_nbr, $inputs_not_used))
            {
                unset($inputs_not_used[array_search($input->input_nbr, $inputs_not_used)]);
            }
            
        }

        $inputs_not_used = array_values($inputs_not_used);

        return view('livewire.config.add.equips.equip')
            ->with(['inputs_used' => $inputs_used, 'inputs_not_used' => $inputs_not_used, 'box' => $box, 'sites' => $sites])
            ->extends('layouts.app')
            ->section('content');
    }
}
