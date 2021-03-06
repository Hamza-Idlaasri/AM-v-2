<?php

namespace App\Http\Livewire\Monitoring;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Models\UsersSite;

class Boxes extends Component
{
    use WithPagination;

    public $search;
 
    protected $queryString = ['search'];

    public function render()
    {
        if($this->search)
        {
            $boxs = $this->getBoxes()
                ->where('nagios_hosts.display_name','like', '%'.$this->search.'%')
                ->paginate(10);

        } else {

            $boxs = $this->getBoxes()->paginate(10);

        }

        return view('livewire.monitoring.boxes')
        ->with(['boxs'=>$boxs,'search' => $this->search])
        ->extends('layouts.app')
        ->section('content');
    }

    public function getBoxes()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        return DB::table('nagios_hosts')
            ->where('alias','box')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->orderBy('display_name');
    }
}
