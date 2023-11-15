<?php

namespace App\Http\Livewire\Config\Edit;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Service extends Component
{
    public $service_id;

    public function mount(Request $request)
    {
        $this->service_id = $request->id;
    }

    public function render()
    {
        $service = DB::table('nagios_services')
            ->where('service_id', $this->service_id)
            ->first();

        $service->check_interval = round($service->check_interval * 60);
        $service->retry_interval = round($service->retry_interval * 60);


        return view('livewire.config.edit.service')
            ->with('service', $service)
            ->extends('layouts.app')
            ->section('content');
    }
}
