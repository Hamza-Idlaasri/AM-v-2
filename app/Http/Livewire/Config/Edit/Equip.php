<?php

namespace App\Http\Livewire\Config\Edit;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Equip extends Component
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
            ->get();

        return view('livewire.config.edit.equip')
            ->with('equip', $equip)
            ->extends('layouts.app')
            ->section('content');
    }
}
