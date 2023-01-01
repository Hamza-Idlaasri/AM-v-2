<?php

namespace App\Http\Livewire\Monitoring;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Models\UsersSite;

class Equips extends Component
{
    use WithPagination;

    public $search;
 
    protected $queryString = ['search'];

    public function render()
    {
        if($this->search)
        {
            $equips =$this->getEquips()
                ->where('nagios_hosts.display_name','like', '%'.$this->search.'%')
                ->paginate(30);
            
            $equips_problems =$this->getEquipsProblems()
                ->where('nagios_hosts.display_name','like', '%'.$this->search.'%')
                ->get();

        } else {

            $equips = $this->getEquips()->paginate(30);
            $equips_problems = $this->getEquipsProblems()->get();

        }

        if(!empty($equips))
            $this->fixInputNbr($equips);

        if(!empty($equips_problems))
            $this->fixInputNbr($equips_problems);

        $msg = $this->description();

        return view('livewire.monitoring.equips')
            ->with(['equips' => $equips,'equips_problems' => $equips_problems,'msg' => $msg,'search' => $this->search])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getEquips()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if ($site_name == 'All') {
            
            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_servicestatus.current_state','nagios_servicestatus.is_flapping','nagios_servicestatus.last_check','nagios_servicestatus.output','nagios_servicestatus.check_command');
                // ->orderBy('nagios_hosts.display_name');
        }
        else 
        {
            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_servicestatus.current_state','nagios_servicestatus.is_flapping','nagios_servicestatus.last_check','nagios_servicestatus.output','nagios_servicestatus.check_command');
                // ->orderBy('nagios_hosts.display_name');
        }
    }
   
    public function getEquipsProblems()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if ($site_name == 'All') {
            
            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_servicestatus.current_state','nagios_servicestatus.is_flapping','nagios_servicestatus.last_check','nagios_servicestatus.output','nagios_servicestatus.check_command')
                ->where('current_state','<>','0');
                // ->orderBy('nagios_hosts.display_name');
        }
        else 
        {
            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.display_name as equip_name','nagios_services.service_object_id','nagios_servicestatus.current_state','nagios_servicestatus.is_flapping','nagios_servicestatus.last_check','nagios_servicestatus.output','nagios_servicestatus.check_command')
                ->where('current_state','<>','0');
                // ->orderBy('nagios_hosts.display_name');
        }
    }

    public function fixInputNbr($equips)
    {
        foreach ($equips as $equip) {
            $equip->check_command = substr($equip->check_command,7,-2);
        }
    }

    public function description()
    {
        return ['l\'équipement fonctionne normalement','l\'équipement est OFF','difficulté à reconnaître l\'état de l\'équipement, vérifier si le box parent est ON'];
    }
}
