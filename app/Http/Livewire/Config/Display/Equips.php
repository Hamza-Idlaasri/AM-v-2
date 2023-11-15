<?php

namespace App\Http\Livewire\Config\Display;

use Livewire\Component;
use App\Models\EquipsDetail;
use App\Models\EquipsNames;
use App\Models\UsersSite;
use Illuminate\Support\Collection;

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
                $equips_details = EquipsDetail::where('equip_name', 'like', '%' . $this->search . '%')->get();
            } else {

                // Get Equips Names
                $equips = EquipsNames::all();

                // Get Equips Details
                $equips_details = EquipsDetail::all();
            }
        } else {

            if ($this->search) {
                // Get Equips Names
                $equips = EquipsNames::where('site_name', $this->site_name)->where('equip_name', 'like', '%' . $this->search . '%')->get();

                // Get Equips Details
                $equips_details = EquipsDetail::where('site_name', $this->site_name)->where('equip_name', 'like', '%' . $this->search . '%')->get();
            } else {

                // Get Equips Names
                $equips = EquipsNames::where('site_name', $this->site_name)->get();

                // Get Equips Details
                $equips_details = EquipsDetail::where('site_name', $this->site_name)->get();
            }
        }

        $all_equips = [];

        foreach ($equips as $equip) {

            $details = [];

            foreach ($equips_details as $detail) {

                if ($equip->equip_name == $detail->equip_name && $equip->box_name == $detail->box_name) {

                    array_push($details, (object) ['pin_name' => $detail->pin_name, 'input_nbr' => $detail->input_nbr]);
                }
            }

            array_push($all_equips, (object)['id' => $equip->id, 'equip_name' => $equip->equip_name, 'box_name' => $equip->box_name, 'site_name' => $equip->site_name, 'details' => $details]);
        }

        return view('livewire.config.display.equips')
            ->with(['all_equips' => $all_equips, 'site_name' => $this->site_name])
            ->extends('layouts.app')
            ->section('content');
    }
}
