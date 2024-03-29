<?php

namespace App\Http\Livewire\Config\Edit;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UsersSite;
use App\Models\Sites;

class Host extends Component
{
    public $host_id;
    public $site_name;

    public function mount(Request $request)
    {
        $this->host_id = $request->id;
    }

    public function render()
    {
        $this->site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        $host = $this->getHost($this->host_id);

        $host->retry_check_interval = round($host->retry_check_interval * 60, 2);
        $host->normal_check_interval = round($host->normal_check_interval * 60, 2);
        
        $parent = $this->Parent_Child();

        $sites = Sites::all()->except(1);

        return view('livewire.config.edit.host')
            ->with(['host' => $host, 'parent' => $parent, 'sites' => $sites])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getHost($host_id)
    {
        return DB::table('nagios_hosts')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->where('nagios_hosts.alias','host')
            ->where('nagios_hosts.host_id', $host_id)
            ->first();
    }

    public function getParentsHost($host_id)
    {
        return DB::table('nagios_host_parenthosts')
            ->join('nagios_hosts','nagios_host_parenthosts.parent_host_object_id','=','nagios_hosts.host_object_id')
            ->where('nagios_host_parenthosts.host_id',$host_id)
            ->select('nagios_hosts.display_name as host_name','nagios_host_parenthosts.parent_host_object_id')
            ->first();
    }

    public function getAllHosts($host_id)
    {
        return DB::table('nagios_hosts')
            ->where('alias','host')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varvalue',$this->site_name)
            ->where('host_id','!=',$host_id)
            ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
            ->get();
    }

    public function Parent_Child()
    {
        $parent_host = $this->getParentsHost($this->host_id);

        $all_hosts = $this->getAllHosts($this->host_id);

        $elements = [];

        foreach ($all_hosts as $host) {

            if($parent_host)
            {
                if($host->host_object_id == $parent_host->parent_host_object_id)
                    array_push($elements, ['relation' => 'parent','host_name' => $host->host_name]);
                else
                    array_push($elements, ['relation' => 'none','host_name' => $host->host_name]);
            }
            else {
                array_push($elements, ['relation' => 'none','host_name' => $host->host_name]);
            }

        }

        return $elements;
    }
}
