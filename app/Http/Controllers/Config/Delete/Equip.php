<?php

namespace App\Http\Controllers\Config\Delete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EquipsDetail;
use App\Models\EquipsNames;

class Equip extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }

    public function deleteEquip($equip_id) {

        $delete_equip = EquipsNames::find($equip_id);

        // 1. Delete the equip from equips_names table
        EquipsNames::find($equip_id)->delete();

        // 2. Remove the files of its pins from reposotery and define lines at nagios.cfg
        $equip_pins = EquipsDetail::where('equip_name', $delete_equip->equip_name)->where('box_name', $delete_equip->box_name)->get();

        foreach ($equip_pins as $pin) {

            // 1. Remove cgf file of the pin in the box folder
            unlink("/usr/local/nagios/etc/objects/boxes/{$pin->box_name}/{$pin->pin_name}.cfg");

            // 2. Remove definition of pin file path in nagios.cfg
            $nagios_file_content = file_get_contents("/usr/local/nagios/etc/nagios.cfg");
            $nagios_file_content = str_replace("cfg_file=/usr/local/nagios/etc/objects/boxes/{$pin->box_name}/{$pin->pin_name}.cfg", '', $nagios_file_content);
            file_put_contents("/usr/local/nagios/etc/nagios.cfg", $nagios_file_content);
            
        }

        // 3. Delete the equip from equips_details table and its pins
        EquipsDetail::where('equip_name', $delete_equip->equip_name)->where('box_name', $delete_equip->box_name)->delete();

        // 4. Restart nagios
        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');

        return back();
    }
}
