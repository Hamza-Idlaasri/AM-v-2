<?php

namespace App\Http\Livewire\Config\Edit;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\EquipsDetail;

class Pin extends Component
{
    public $equip_id;

    public function mount(Request $request)
    {
        $this->equip_id = $request->id;
    }

    public function render()
    {
        $equip = DB::table('nagios_services')
            ->where('service_id', $this->equip_id)
            ->join('am.equips_details as ed','nagios_services.display_name','=','ed.pin_name')
            ->first();

        $equip->check_interval = round($equip->check_interval * 60);
        $equip->retry_interval = round($equip->retry_interval * 60);
        
        return view('livewire.config.edit.pin')
            ->with('equip', $equip)
            ->extends('layouts.app')
            ->section('content');
    }
}
