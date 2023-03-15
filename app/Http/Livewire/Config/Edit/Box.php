<?php

namespace App\Http\Livewire\Config\Edit;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UsersSite;

class Box extends Component
{
    public $box_id;

    public function mount(Request $request)
    {
        $this->box_id = $request->id;
    }

    public function render()
    {
        $box = $this->getBox($this->box_id);

        $box->retry_check_interval = round($box->retry_check_interval * 60,2);
        $box->normal_check_interval = round($box->normal_check_interval * 60,2);

        $parent = $this->Parent_Child();

        return view('livewire.config.edit.box')
            ->with(['box' => $box, 'parent' => $parent])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getBox($box_id)
    {
        return DB::table('nagios_hosts')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->where('nagios_hosts.alias','box')
            ->where('nagios_hosts.host_id', $box_id)
            ->first();
    }

    public function getParentsHost($box_id)
    {
        return DB::table('nagios_host_parenthosts')
            ->join('nagios_hosts','nagios_host_parenthosts.parent_host_object_id','=','nagios_hosts.host_object_id')
            ->where('nagios_host_parenthosts.host_id',$box_id)
            ->select('nagios_hosts.display_name as host_name','nagios_host_parenthosts.parent_host_object_id')
            ->first();
    }

    public function getAllHosts()
    {
        $site_name = UsersSite::where('user_id',auth()->user()->id)->first()->current_site;

        if ($site_name == "All") {
            return DB::table('nagios_hosts')
                ->where('alias','host')
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
                ->get();
        } else {
            return DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue',$site_name)
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.host_object_id')
                ->get();
        }
        
    }

    public function Parent_Child()
    {
        $parent_host = $this->getParentsHost($this->box_id);

        $all_hosts = $this->getAllHosts();

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
