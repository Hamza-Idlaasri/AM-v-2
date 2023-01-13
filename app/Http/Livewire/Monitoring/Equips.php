<?php

namespace App\Http\Livewire\Monitoring;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Models\UsersSite;
use App\Models\Sites;
use App\Models\EquipsDetail;
use App\Models\EquipsNames;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class Equips extends Component
{
    use WithPagination;

    public $search;
 
    protected $queryString = ['search'];

    public $site_name;

    public function render()
    {
        $all_sites = Sites::all()->except(1);
        
        $equipements = EquipsNames::all(); 

        foreach ($all_sites as $site) {
            $boxes = DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$site->site_name)
                ->select('nagios_hosts.host_object_id','nagios_hosts.display_name','nagios_hosts.address','nagios_hoststatus.is_flapping','nagios_hoststatus.current_state','nagios_hoststatus.last_check','nagios_hoststatus.output','nagios_customvariables.varvalue as site_name')
                ->orderBy('display_name')
                ->get();

            foreach ($boxes as $box) {
                foreach ($equipements as $equip) {
                    if ($box->display_name == $equip->box_name) {
                        EquipsNames::where('id',$equip->id)->update(['site_name' => $box->site_name]);
                    }
                }
            }
        }        

        $this->site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if($this->search)
        {
            $equips = $this->getEquips()
                ->where('nagios_services.display_name','like', '%'.$this->search.'%')
                ->get();
            
            $equips_problems = $this->getEquipsProblems()
                ->where('nagios_services.display_name','like', '%'.$this->search.'%')
                ->get();

        } else {

            $equips = $this->getEquips()->get();
            $equips_problems = $this->getEquipsProblems()->get();

        }

        if(!empty($equips))
            $this->fixInputNbr($equips);

        if(!empty($equips_problems))
            $this->fixInputNbr($equips_problems);

        $equips = $this->OrganizeData($equips);

        $equips_problems = $this->OrganizeData($equips_problems);

        return view('livewire.monitoring.equips')
            ->with(['equips' => $this->paginate($equips),'equips_problems' => $equips_problems,'msg' => $this->description(),'search' => $this->search])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getEquips()
    {
        if ($this->site_name == 'All') {
            
            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->join('am.equips_details as ed','nagios_services.display_name','=','ed.pin_name')
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.service_object_id','nagios_servicestatus.current_state','nagios_servicestatus.is_flapping','nagios_servicestatus.last_check','nagios_servicestatus.output','nagios_servicestatus.check_command','ed.equip_name','ed.site_name','ed.pin_name','ed.hall_name')
                ->orderBy('nagios_hosts.display_name')
                ->orderBy('nagios_services.display_name');

        } else {

            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->join('am.equips_details as ed','nagios_services.display_name','=','ed.pin_name')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.service_object_id','nagios_servicestatus.current_state','nagios_servicestatus.is_flapping','nagios_servicestatus.last_check','nagios_servicestatus.output','nagios_servicestatus.check_command','ed.equip_name','ed.site_name','ed.pin_name','ed.hall_name')
                ->orderBy('nagios_hosts.display_name')
                ->orderBy('nagios_services.display_name');
        }
    }
   
    public function getEquipsProblems()
    {
        if ($this->site_name == 'All') {
            
            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->join('am.equips_details as ed','nagios_services.display_name','=','ed.pin_name')
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.service_object_id','nagios_servicestatus.current_state','nagios_servicestatus.is_flapping','nagios_servicestatus.last_check','nagios_servicestatus.output','nagios_servicestatus.check_command','ed.equip_name','ed.site_name','ed.pin_name','ed.hall_name')
                ->where('current_state','<>','0')
                ->orderBy('nagios_hosts.display_name')
                ->orderBy('nagios_services.display_name');
        }
        else 
        {
            return DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->join('am.equips_details as ed','nagios_services.display_name','=','ed.pin_name')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id','nagios_services.service_object_id','nagios_servicestatus.current_state','nagios_servicestatus.is_flapping','nagios_servicestatus.last_check','nagios_servicestatus.output','nagios_servicestatus.check_command','ed.equip_name','ed.site_name','ed.pin_name','ed.hall_name')
                ->where('current_state','<>','0')
                ->orderBy('nagios_hosts.display_name')
                ->orderBy('nagios_services.display_name');
        }
    }

    public function fixInputNbr($equips)
    {
        foreach ($equips as $equip) {
            $equip->check_command = substr($equip->check_command,7,-2);
        }
    }

    public function OrganizeData($all_equips)
    {
        
        if ($this->site_name == "All") {

            $equips_names = EquipsNames::all();
        } else {

            $equips_names = EquipsNames::join('nagios.nagios_hosts','equips_names.box_name','=','nagios_hosts.display_name')
                ->join('nagios.nagios_customvariables','nagios.nagios_hosts.host_object_id','=','nagios.nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('equips_names.equip_name','equips_names.box_name','nagios_customvariables.varvalue as site_name')
                ->get();
        }

        $equips_data = [];

        foreach ($equips_names as $name) {

            $pins = [];

            foreach ($all_equips as $equip) {
                if ($equip->equip_name == $name->equip_name && $equip->box_name == $name->box_name) {
                    array_push($pins,$equip);
                } else {
                    continue;
                }
            }

            $data = (object)['equip_name' => $name->equip_name, 'pins' => $pins];
            
            array_push($equips_data, $data);
        }

        return $equips_data;
    }

    public function description()
    {
        return ['fonctionne normalement','Alert!, l\'équipement ne fonctionne pas normalement','l\'équipement est OFF','difficulté à reconnaître l\'état de l\'équipement, vérifier si le box parent est ON'];
    }

    public function paginate($items, $perPage = 30, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
