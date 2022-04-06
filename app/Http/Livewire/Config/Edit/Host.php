<?php

namespace App\Http\Livewire\Config\Edit;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Host extends Component
{
    public $host_id;

    public function mount(Request $request)
    {
        $this->host_id = $request->id;
    }

    public function render()
    {
        $host = $this->getHost($this->host_id);

        $parent_hosts = $this->getParentsHost($this->host_id);

        $all_hosts = $this->getAllHosts($this->host_id);

        return view('livewire.config.edit.host')
            ->with(['host' => $host, 'parent_hosts' => $parent_hosts, 'all_hosts' => $all_hosts])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getHost($host_id)
    {
        return DB::table('nagios_hosts')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->where('nagios_hosts.alias','host')
            ->where('nagios_hosts.host_object_id', $host_id)
            ->get();
    }

    public function getParentsHost($host_id)
    {
        return DB::table('nagios_hosts')
        ->join('nagios_host_parenthosts','nagios_hosts.host_id','=','nagios_host_parenthosts.host_id')
        ->select('nagios_hosts.display_name as host_name','nagios_host_parenthosts.*')
        ->where('nagios_host_parenthosts.host_id','=', $host_id)
        ->get();
    }

    public function getAllHosts($host_id)
    {
        return DB::table('nagios_hosts')
            ->where('alias','host')
            ->where('host_id','!=',$host_id)
            ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
            ->get();
    }
}
