<?php

namespace App\Http\Livewire\Config\Display;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class Boxes extends Component
{
    public $search;
 
    protected $queryString = ['search'];

    public function render()
    {
        if($this->search)
        {
            $boxes = $this->getBoxes()
                ->where('nagios_hosts.display_name','like', '%'.$this->search.'%')
                ->get();

        } else {

            $boxes = $this->getBoxes()->get();

        }

        if (!empty($boxes)) 
            $this->convertRetryTime($boxes);

        return view('livewire.config.display.boxes')
            ->with('boxes', $boxes)
            ->extends('layouts.app')
            ->section('content');
    }

    public function getBoxes()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if ($site_name) {
            return DB::table('nagios_hosts')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->select('nagios_hosts.host_id','nagios_hosts.host_object_id','nagios_hosts.display_name','nagios_hosts.address','nagios_hoststatus.current_state','nagios_hosts.retry_interval','nagios_hoststatus.max_check_attempts','nagios_hoststatus.has_been_checked','nagios_hoststatus.notifications_enabled')
                ->where('nagios_hosts.alias','box')
                ->orderBy('nagios_hosts.display_name');

        } else {
            return DB::table('nagios_hosts')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->select('nagios_hosts.host_id','nagios_hosts.host_object_id','nagios_hosts.display_name','nagios_hosts.address','nagios_hoststatus.current_state','nagios_hosts.retry_interval','nagios_hoststatus.max_check_attempts','nagios_hoststatus.has_been_checked','nagios_hoststatus.notifications_enabled')
                ->where('nagios_hosts.alias','box')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->orderBy('nagios_hosts.display_name');
        }
        
    }

    public function convertRetryTime($boxes)
    {
        foreach ($boxes as $box) {
            $box->retry_interval = round($box->retry_interval * 60,2);
        }
    }
}
