<?php

namespace App\Http\Livewire\Problems;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Boxes extends Component
{
    protected $boxs;

    public $search;
 
    protected $queryString = ['search'];

    public function render()
    {
        if($this->search)
        {
            $this->boxs = $this->getBoxes()
                ->where('nagios_hosts.display_name','like', '%'.$this->search.'%')
                ->paginate(10);

        } else {

            $this->boxs = $this->getBoxes()->paginate(10);

        }

        return view('livewire.problems.boxes')
        ->with(['boxs'=>$this->boxs])
        ->extends('layouts.app')
        ->section('content');
    }

    public function getBoxes()
    {
        return DB::table('nagios_hosts')
        ->where('alias','box')
        ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
        ->where('current_state','<>','0')
        ->orderBy('display_name');
    }
}
