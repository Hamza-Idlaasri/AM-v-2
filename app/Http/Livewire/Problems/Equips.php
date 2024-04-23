<?php

namespace App\Http\Livewire\Problems;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Models\UsersSite;
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
        $this->site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if($this->search)
        {   
            $equips_problems = $this->getEquipsProblems()
                ->where('ed.equip_name','like', '%'.$this->search.'%');

        } else {

            $equips_problems = $this->getEquipsProblems();

        }

        $equips_problems = $this->OrganizeData($equips_problems);

        return view('livewire.problems.equips')
            ->with(['equips_problems' => $this->paginate($equips_problems), 'search' => $this->search, 'msg' => $this->description()])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getEquipsProblems()
    {
        if ($this->site_name == 'All') {
            
            return  $this->getInputNbr(EquipsDetail::all())->where('current_state','<>','0');

        } else {

            return $this->getInputNbr(EquipsDetail::where('site_name', $this->site_name)->get())->where('current_state','<>','0');
        }
    }

    public function OrganizeData($all_equips)
    {
        
        if ($this->site_name == "All") {

            $equips_names = EquipsNames::all();
        } else {

            $equips_names = EquipsNames::where('site_name',$this->site_name)->get();
        }

        $equips_data = [];

        foreach ($equips_names as $name) {

            $pins = [];

            foreach ($all_equips as $equip) {
                if ($equip->equip_name == $name->equip_name && $equip->box_name == $name->box_name && $equip->site_name == $name->site_name) {
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

    public function getInputNbr($equips_details) {

        $pins_names = DB::table('nagios_services')
            ->join('nagios_hosts', 'nagios_services.host_object_id', '=','nagios_hosts.host_object_id')
            ->join('nagios_servicestatus', 'nagios_services.service_object_id','=', 'nagios_servicestatus.service_object_id')
            ->select('nagios_hosts.display_name as box_name', 'nagios_services.display_name as pin_name', 'nagios_servicestatus.check_command','nagios_servicestatus.current_state', 'nagios_servicestatus.last_check')
            ->get();

        foreach ($equips_details as $equip) {
            foreach ($pins_names as $pin) {
                if ($pin->box_name == $equip->box_name && $pin->pin_name == $equip->pin_name) {
                    $equip->check_command = substr($pin->check_command,9,-2);
                    $equip->current_state = $pin->current_state;
                    $equip->last_check = $pin->last_check;
                }
            }
        }

        return $equips_details;
    }
}
