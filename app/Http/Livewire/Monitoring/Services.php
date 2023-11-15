<?php

namespace App\Http\Livewire\Monitoring;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use App\Models\UsersSite;

class Services extends Component
{
    use WithPagination;

    public $search;
    public $site_name;

    protected $queryString = ['search'];

    public function render()
    {
        $this->site_name = UsersSite::where('user_id', auth()->user()->id)->first()->current_site;

        if ($this->search) {
            $services = $this->getServices()
                ->where('nagios_hosts.display_name', 'like', '%' . $this->search . '%')
                ->paginate(30);
        } else {

            $services = $this->getServices()->paginate(30);
        }

        return view('livewire.monitoring.services')
            ->with(['services' => $services, 'search' => $this->search])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getServices()
    {
        if ($this->site_name == 'All') {

            return DB::table('nagios_hosts')
                ->where('alias', 'host')
                ->join('nagios_services', 'nagios_hosts.host_object_id', '=', 'nagios_services.host_object_id')
                ->join('nagios_servicestatus', 'nagios_services.service_object_id', '=', 'nagios_servicestatus.service_object_id')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->where('nagios_customvariables.varname', 'SITE')
                ->select('nagios_hosts.display_name as host_name', 'nagios_hosts.host_object_id', 'nagios_services.display_name as service_name', 'nagios_services.service_object_id', 'nagios_servicestatus.current_state', 'nagios_servicestatus.is_flapping', 'nagios_servicestatus.last_check', 'nagios_servicestatus.output','nagios_customvariables.varvalue as site_name');
            // ->orderBy('nagios_hosts.display_name');

        } else {
            return DB::table('nagios_hosts')
                ->where('alias', 'host')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->join('nagios_services', 'nagios_hosts.host_object_id', '=', 'nagios_services.host_object_id')
                ->join('nagios_servicestatus', 'nagios_services.service_object_id', '=', 'nagios_servicestatus.service_object_id')
                ->select('nagios_hosts.display_name as host_name', 'nagios_hosts.host_object_id', 'nagios_services.display_name as service_name', 'nagios_services.service_object_id', 'nagios_servicestatus.current_state', 'nagios_servicestatus.is_flapping', 'nagios_servicestatus.last_check', 'nagios_servicestatus.output')
                ->where('nagios_customvariables.varvalue', $this->site_name);
            // ->orderBy('nagios_hosts.display_name');
        }
    }
}
