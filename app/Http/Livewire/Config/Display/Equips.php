<?php

namespace App\Http\Livewire\Config\Display;

use Livewire\Component;
use App\Models\EquipsDetail;
use App\Models\EquipsNames;
use App\Models\UsersSite;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Foreach_;

class Equips extends Component
{
    public $search, $site_name;

    protected $queryString = ['search'];

    public function render()
    {
        $this->site_name = UsersSite::where('user_id', auth()->user()->id)->first()->current_site;

        if ($this->site_name == "All") {

            if ($this->search) {
                // Get Equips Names
                $equips = EquipsNames::where('equip_name', 'like', '%' . $this->search . '%')->get();

                // Get Equips Details
                $equips_details = $this->getInputNbr(EquipsDetail::where('equip_name', 'like', '%' . $this->search . '%')->get());
            } else {

                // Get Equips Names
                $equips = EquipsNames::all();

                // Get Equips Details
                $equips_details = $this->getInputNbr(EquipsDetail::all());
            }
        } else {

            if ($this->search) {
                // Get Equips Names
                $equips = EquipsNames::where('site_name', $this->site_name)->where('equip_name', 'like', '%' . $this->search . '%')->get();

                // Get Equips Details
                $equips_details = $this->getInputNbr(EquipsDetail::where('site_name', $this->site_name)->where('equip_name', 'like', '%' . $this->search . '%')->get());
            } else {

                // Get Equips Names
                $equips = EquipsNames::where('site_name', $this->site_name)->get();

                // Get Equips Details
                $equips_details = $this->getInputNbr(EquipsDetail::where('site_name', $this->site_name)->get());
            }
        }

        $all_equips = [];

        foreach ($equips as $equip) {

            $details = [];

            foreach ($equips_details as $detail) {

                if ($equip->equip_name == $detail->equip_name && $equip->box_name == $detail->box_name) {

                    array_push($details, (object) ['pin_name' => $detail->pin_name, 'input_nbr' => $detail->check_command]);
                }
            }

            array_push($all_equips, (object)['id' => $equip->id, 'equip_name' => $equip->equip_name, 'box_name' => $equip->box_name, 'site_name' => $equip->site_name, 'details' => $details]);
        }

        return view('livewire.config.display.equips')
            ->with(['all_equips' => $all_equips, 'site_name' => $this->site_name])
            ->extends('layouts.app')
            ->section('content');
    }

    public function getInputNbr($equips_details) {

        $pins_names = DB::table('nagios_services')
            ->join('nagios_hosts', 'nagios_services.host_object_id', '=','nagios_hosts.host_object_id')
            ->join('nagios_servicestatus', 'nagios_services.service_object_id','=', 'nagios_servicestatus.service_object_id')
            ->select('nagios_hosts.display_name as box_name', 'nagios_services.display_name as pin_name', 'nagios_servicestatus.check_command')
            ->get();

        foreach ($equips_details as $equip) {
            foreach ($pins_names as $pin) {
                if ($pin->box_name == $equip->box_name && $pin->pin_name == $equip->pin_name) {
                    $equip->check_command = substr($pin->check_command,9,-2);
                }
            }
        }

        return $equips_details;
    }
}
