<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Sites;
use App\Models\UsersSite;
use Illuminate\Support\Facades\DB;

class AllSites extends Component
{
    public $site;

    protected $rules = [
        'site' => 'required|min:3|max:100|unique:am.all_sites,site_name|regex:/^[a-zA-Z][a-zA-Z0-9-_(). ÀÂÇÉÈÊÎÔÛÙàâçéèêôûù]/',
    ];

    public function addSite()
    {
        // Validate user info
        $this->validate();

        $add_site = Sites::create([
            'site_name' => $this->site,
        ]);

        $this->reset();
    }

    public function site($site_id)
    {
        // Get the site that the user selected
        $site_name = Sites::find($site_id)->site_name;

        // Get the current user id
        $user_id = auth()->user()->id;

        // Get the current site the user want to see
        $current_site = UsersSite::where('user_id', $user_id)->first();

        // Update current site
        $current_site->update([
            'current_site' => $site_name,
        ]);

        return redirect()->route('overview');

    }

    public function render()
    {
        $sites_details = $this->detailsOfSites();

        $summary = $this->SummaryOfAllSites();

        $all_sites = Sites::all();
        
        return view('livewire.all-sites')
            ->with(['all_sites' => $all_sites, 'summary' => $summary, 'sites_details' => $sites_details])
            ->extends('layouts.template')
            ->section('content');
    }

    public function detailsOfSites()
    {
        $all_sites = Sites::all()->except(1);

        $sites_details = [];

        foreach ($all_sites as $site) {
            
            $total_hosts = 0;
            $total_boxes = 0;
            $total_services = 0;
            $total_equips = 0;

            //----------------------------------------- Hosts -----------------------------------------//

            $site_hosts = DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->where('nagios_customvariables.varvalue',$site->site_name)
                ->get();

            $hosts_up = 0;
            $hosts_down = 0;
            $hosts_unreach = 0;

            foreach ($site_hosts as $hosts) {

                switch ($hosts->current_state) {
                    case 0:
                        $hosts_up++;
                        break;
                    case 1:
                        $hosts_down++;
                        break;
                    case 2:
                        $hosts_unreach++;
                        break;
                }

                $total_hosts++;

            }
            
            //----------------------------------------- Boxes -----------------------------------------//

            $site_boxes = DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
                ->where('nagios_customvariables.varvalue',$site->site_name)
                ->get();

            $boxes_up = 0;
            $boxes_down = 0;
            $boxes_unreach = 0;

            foreach ($site_boxes as $boxes) {

                switch ($boxes->current_state) {
                    case 0:
                        $boxes_up++;
                        break;
                    case 1:
                        $boxes_down++;
                        break;
                    case 2:
                        $boxes_unreach++;
                        break;
                }

                $total_boxes++;

            }

            //----------------------------------------- Services -------------------------------------------------//

            $site_services = DB::table('nagios_hosts')
                ->where('alias','host')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->where('nagios_customvariables.varvalue',$site->site_name)
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.*','nagios_services.display_name as service_name','nagios_services.*','nagios_servicestatus.*')
                ->get();

            $services_ok = 0;
            $services_warning = 0;
            $services_critical = 0;
            $services_unknown = 0;

            foreach ($site_services as $services) {

                switch ($services->current_state) {
                    case 0:
                        $services_ok++;
                        break;
                    case 1:
                        $services_warning++;
                        break;
                    case 2:
                        $services_critical++;
                        break;
                    case 3:
                        $services_unknown++;
                        break;
                }

                $total_services++;

            }

            //----------------------------------------- Equips -------------------------------------------------//

            $site_equips = DB::table('nagios_hosts')
                ->where('alias','box')
                ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
                ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
                ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
                ->where('nagios_customvariables.varvalue',$site->site_name)
                ->select('nagios_hosts.display_name as host_name','nagios_hosts.*','nagios_services.display_name as service_name','nagios_services.*','nagios_servicestatus.*')
                ->get();

            $equips_ok = 0;
            $equips_warning = 0;
            $equips_critical = 0;
            $equips_unknown = 0;

            foreach ($site_equips as $equips) {

                switch ($equips->current_state) {
                    case 0:
                        $equips_ok++;
                        break;
                    case 1:
                        $equips_warning++;
                        break;
                    case 2:
                        $equips_critical++;
                        break;
                    case 3:
                        $equips_unknown++;
                        break;
                }

                $total_equips++;

            }

            array_push($sites_details,(object)["id" => $site->id ,"site_name" => $site->site_name,"total_hosts" => $total_hosts,"hosts_up" => $hosts_up,"hosts_down" => $hosts_down,"hosts_unreach" => $hosts_unreach,"total_boxes" => $total_boxes,"boxes_up" => $boxes_up,"boxes_down" => $boxes_down,"boxes_unreach" => $boxes_unreach,"total_services" => $total_services,"services_ok" => $services_ok,"services_warning" => $services_warning,"services_critical" => $services_critical,"services_unknown" => $services_unknown,"total_equips" => $total_equips,"equips_ok" => $equips_ok,"equips_warning" => $equips_warning,"equips_critical" => $equips_critical,"equips_unknown" => $equips_unknown]);
        }

        return $sites_details;
    }

    public function SummaryOfAllSites()
    {
        $total_hosts = 0;
        $total_boxes = 0;
        $total_services = 0;
        $total_equips = 0;

        //----------------------------------------- Hosts -----------------------------------------//

        $hosts_up = 0;
        $hosts_down = 0;
        $hosts_unreach = 0;

        $hosts = DB::table('nagios_hosts')
            ->where('alias','host')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->get();

        foreach ($hosts as $host) {
        
            switch ($host->current_state) {
                case 0:
                    $hosts_up++;
                    break;
                case 1:
                    $hosts_down++;
                    break;
                case 2:
                    $hosts_unreach++;
                    break;
            }

            $total_hosts++;
        }

        //------------------------------------------- Boxes --------------------------------------------//

        $boxes_up = 0;
        $boxes_down = 0;
        $boxes_unreach = 0;

        $boxes = DB::table('nagios_hosts')
            ->where('alias','box')
            ->join('nagios_hoststatus','nagios_hosts.host_object_id','=','nagios_hoststatus.host_object_id')
            ->get();
                
        foreach ($boxes as $box) {
        
            switch ($box->current_state) {
                case 0:
                    $boxes_up++;
                    break;
                case 1:
                    $boxes_down++;
                    break;
                case 2:
                    $boxes_unreach++;
                    break;
            }

            $total_boxes++;
        }

        //------------------------------------------- Services --------------------------------------------//

        $services_ok = 0;
        $services_warning = 0;
        $services_critical = 0;
        $services_unknown = 0;

        $services = DB::table('nagios_hosts')
            ->where('alias','host')
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->get();        
        
        foreach ($services as $service) {
        
            switch ($service->current_state) {
                case 0:
                    $services_ok++;
                    break;
                case 1:
                    $services_warning++;
                    break;
                case 2:
                    $services_critical++;
                    break;
                case 3:
                    $services_unknown++;
                    break;
            }

            $total_services++;
        }

        //------------------------------------------- Equipements --------------------------------------------//

        $equips_ok = 0;
        $equips_warning = 0;
        $equips_critical = 0;
        $equips_unknown = 0;

        $equips = DB::table('nagios_hosts')
            ->where('alias','box')
            ->join('nagios_services','nagios_hosts.host_object_id','=','nagios_services.host_object_id')
            ->join('nagios_servicestatus','nagios_services.service_object_id','=','nagios_servicestatus.service_object_id')
            ->get();
        
        foreach ($equips as $equip) {
        
            switch ($equip->current_state) {
                case 0:
                    $equips_ok++;
                    break;
                case 1:
                    $equips_warning++;
                    break;
                case 2:
                    $equips_critical++;
                    break;
                case 3:
                    $equips_unknown++;
                    break;
            }

            $total_equips++;
        }

        return (object)[
            "hosts_up" => $hosts_up, "hosts_down" => $hosts_down, "hosts_unreach" => $hosts_unreach,
            "boxes_up" => $boxes_up, "boxes_down" => $boxes_down, "boxes_unreach" => $boxes_unreach,
            "services_ok" => $services_ok, "services_warning" => $services_warning, "services_critical" => $services_critical, "services_unknown" => $services_unknown,
            "equips_ok" => $equips_ok, "equips_warning" => $equips_warning, "equips_critical" => $equips_critical, "equips_unknown" => $equips_unknown,
            "total_hosts" => $total_hosts, "total_boxes" => $total_boxes, "total_boxes", "total_services" => $total_services, "total_equips" => $total_equips,
        ];

    }

}
