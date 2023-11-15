<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\UsersSite;

class NetworkMap extends Component
{
    // Filter
    public $status;
    public $box_name;

    public function render()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if ($site_name == 'All') {

            $hosts = DB::table('nagios_hosts')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->select('nagios_hosts.host_object_id','nagios_hosts.display_name','nagios_hosts.address','nagios_hoststatus.current_state');
            
            if (!auth()->user()->hasRole('super_admin')) {
                $hosts = $hosts->where('nagios_hosts.alias','box');
            }
            
            if ($this->status) {
                $hosts = $hosts->where('nagios_hoststatus.current_state', $this->status);  
            }

            $hosts = $hosts->get();

            $parent_hosts = DB::table('nagios_hosts')
                ->join('nagios_host_parenthosts','nagios_hosts.host_id','=','nagios_host_parenthosts.host_id')
                ->select('nagios_hosts.host_object_id','nagios_host_parenthosts.parent_host_object_id');

            if (!auth()->user()->hasRole('super_admin')) {
                $parent_hosts = $parent_hosts->where('nagios_hosts.alias','box');
            }
            
            $parent_hosts = $parent_hosts->get();

            return view('livewire.network-map')
                ->with('hosts', $hosts)
                ->with('parent_hosts', $parent_hosts)
                ->extends('layouts.app')
                ->section('content');
        }
        else
        {
            $hosts = DB::table('nagios_hosts')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->select('nagios_hosts.host_object_id','nagios_hosts.display_name','nagios_hosts.address','nagios_hoststatus.current_state')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->get();

            $parent_hosts = DB::table('nagios_hosts')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_host_parenthosts','nagios_hosts.host_id','=','nagios_host_parenthosts.host_id')
                ->select('nagios_hosts.host_object_id','nagios_host_parenthosts.parent_host_object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->get();

            return view('livewire.network-map')
                ->with('hosts', $hosts)
                ->with('parent_hosts', $parent_hosts)
                ->extends('layouts.app')
                ->section('content');
        }
    
    }
}
