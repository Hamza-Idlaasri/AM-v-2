<?php

namespace App\Http\Livewire\Config\Add\Equip;

use Livewire\Component;
use App\Models\UsersSite;
use App\Models\EquipsDetail;
use Illuminate\Support\Facades\DB;

class Equip extends Component
{
    public $site_name;

    public function render()
    {
        $this->site_name = UsersSite::where('user_id', auth()->user()->id)->first()->current_site;

        // Get all boxes
        $boxes = $this->checkBox($this->getBoxes());

        return view('livewire.config.add.equip.equip')
            ->with('boxes', $boxes)
            ->extends('layouts.app')
            ->section('content');
    }

    public function getBoxes()
    {

        if ($this->site_name == "All") {
            return DB::table('nagios_hosts')
                ->where('alias', 'box')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->where('nagios_customvariables.varname', 'SITE')
                ->select('nagios_hosts.host_object_id', 'nagios_hosts.display_name as box_name', 'nagios_customvariables.varvalue as site_name')
                ->get();
        } else {
            return DB::table('nagios_hosts')
                ->where('alias', 'box')
                ->join('nagios_customvariables', 'nagios_hosts.host_object_id', '=', 'nagios_customvariables.object_id')
                ->where('nagios_customvariables.varvalue', $this->site_name)
                ->select('nagios_hosts.host_object_id', 'nagios_hosts.display_name as box_name', 'nagios_customvariables.varvalue as site_name')
                ->get();
        }
    }

    public function checkBox($boxes)
    {
        foreach ($boxes as $box) {

            $pins_used = EquipsDetail::where('box_name', $box->box_name)->where('site_name', $box->site_name)->get();

            $box->pins_used = sizeof($pins_used);

            if (sizeof($pins_used)) {

                switch ($pins_used[0]->box_type) {
                    case 'bf1010':
                        $box->pins_not_used = 10 - $box->pins_used;
                        $box->total_pins = 10;
                        break;
                    case 'bf2300':
                        $box->pins_not_used = 12 - $box->pins_used;
                        $box->total_pins = 12;
                        break;
                }
            } else {
                
                $box_type = $this->getBoxType($box->host_object_id)->box_type;

                switch ($box_type) {
                    case 'bf1010':
                        $box->pins_not_used = 10;
                        $box->total_pins = 10;
                        break;
                    case 'bf2300':
                        $box->pins_not_used = 12;
                        $box->total_pins = 12;
                        break;
                }
            }
        }

        return $boxes;
    }

    public function getBoxType($id) {
        return  DB::table('nagios_hosts')
            ->where('alias','box')
            ->where('nagios_hosts.host_object_id', $id)
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varname','BOXTYPE')
            ->select('nagios_customvariables.varvalue as box_type')
            ->first();
    }
}
