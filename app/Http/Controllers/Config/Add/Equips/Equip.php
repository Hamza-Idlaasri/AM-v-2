<?php

namespace App\Http\Controllers\Config\Add\Equips;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EquipsNames;
use Illuminate\Support\Facades\DB;

class Equip extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }

    public function createEquip(Request $request) {

        // validation
        $this->validate($request,[

            'equip_name' => 'required|min:2|max:200|regex:/^[a-zA-Z0-9-_+ ]/', // TODO: Add unique name
            'box_id' => 'required',

        ],[
            'equip_name.required' => 'the equipement name field is empty',
            'box_id.required' => 'choose a box for the equipement',
        ]);

        // Get the details of the box
        $box = DB::table('nagios_hosts')
            ->where('alias', 'box')
            ->where('host_object_id', $request->box_id)
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varname','SITE')
            ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id as box_id','nagios_customvariables.varvalue as site_name')
            ->first();
        
        // Add This equipement to equips table
        $add_equip = EquipsNames::create([
            'box_name' => $box->box_name,
            'equip_name' => $request->equip_name,
            'site_name' => $box->site_name
        ]);

        return redirect()->route('monitoring.equips');

    }
}
