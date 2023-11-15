<?php

namespace App\Http\Livewire\Config\Edit;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\EquipsNames;

class Equip extends Component
{
    public $equip_id;

    public function mount(Request $request)
    {
        $this->equip_id = $request->id;
    }

    public function render()
    {
        $equip = EquipsNames::find($this->equip_id);
        
        return view('livewire.config.edit.equip')
            ->with('equip', $equip)
            ->extends('layouts.app')
            ->section('content');
    }
}
