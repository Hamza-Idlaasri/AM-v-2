<?php

namespace App\Http\Controllers\Config\Edit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EquipsDetail;
use App\Models\EquipsNames;

class Equip extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }
    
    public function editEquip(Request $request, $equip_id) {

        // validation
        $this->validate($request,[
            'equipName' => 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/'
        ]);

        $equip_edited = EquipsNames::find($equip_id);

        // 1. Update name in equip_names table
        EquipsNames::find($equip_id)->update(['equip_name' => $request->equipName]);

        EquipsDetail::where('equip_name', $equip_edited->equip_name)->where('box_name', $equip_edited->box_name)->update(['equip_name' => $request->equipName]);
        
        return redirect()->route('config-equips');

    }
}
