<?php

namespace App\Http\Livewire\Monitoring;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Models\UsersSite;

class Hosts extends Component
{
    use WithPagination;

    public $search;
    public $site_name;

    protected $queryString = ['search'];

    public function render()
    {
        $this->site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if($this->search)
        {
            $hosts = $this->getHosts()
                ->where('nagios_hosts.display_name','like', '%'.$this->search.'%')
                ->paginate(30);

        } else {

            $hosts = $this->getHosts()->paginate(30);

        }

        return view('livewire.monitoring.hosts')
            ->with(['hosts' => $hosts,'search' => $this->search])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getHosts()
    {

        if($this->site_name == "All")
        {
            return DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varname','SITE')
                ->select('nagios_hosts.host_object_id','nagios_hosts.display_name','nagios_hosts.address','nagios_hoststatus.is_flapping','nagios_hoststatus.current_state','nagios_hoststatus.last_check','nagios_hoststatus.output','nagios_customvariables.varvalue as site_name')
                ->orderBy('display_name');
        }
        else 
        {
            return DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$this->site_name)
                ->select('nagios_hosts.host_object_id','nagios_hosts.display_name','nagios_hosts.address','nagios_hoststatus.is_flapping','nagios_hoststatus.current_state','nagios_hoststatus.last_check','nagios_hoststatus.output')
                ->orderBy('display_name');
        }
    }
}
