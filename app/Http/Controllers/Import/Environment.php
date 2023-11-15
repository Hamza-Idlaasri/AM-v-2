<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Imports\EnvironementImport;
use Illuminate\Http\Request;
use Excel;

class Environment extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }

    public $array;

    public function import(Request $request)
    {
        $this->validate($request, [
            'excel' => 'file|required'
        ]);

        $this->array = Excel::toArray(new EnvironementImport, $request->excel);

        $this->array = $this->organizeArray($this->checkCells($this->array[0]));

        session(['data' => $this->array]);

        return redirect()->route('display-environment');
    }

    public function checkCells($array)
    {

        for ($i = 1; $i < sizeof($array); $i++) {

            for ($j = 0; $j < 7; $j++) {
                if ($array[$i][$j]) {
                    continue;
                } else {
                    $array[$i][$j] = $array[$i - 1][$j];
                }
            }
        }

        for ($i = 1; $i < sizeof($array); $i++) {

            $array[$i] = (object) ['box_name' => $array[$i][0], 'box_type' => $array[$i][1], 'ip_address' => $array[$i][2], 'equip_name' => $array[$i][3], 'pin_desc' => $array[$i][4], 'hall' => $array[$i][5], 'input_nbr' => $array[$i][6]];
        }

        unset($array[0]);

        $array = array_values($array);

        return $array;
    }

    public function organizeArray($array)
    {

        // Get Boxes and there keys
        $boxes = [];
        $keys = [];

        for ($i = 0; $i < sizeof($array); $i++) {

            $keys[] = $i;

            if ($i == sizeof($array) - 1) {
                $boxes[] = (object) ['box_name' => $array[$i]->box_name, 'ip_address' => $array[$i]->ip_address, 'box_type' => $array[$i]->box_type, "keys" => $keys];
                $keys = [];
            } else {
                if ($array[$i]->box_name == $array[$i + 1]->box_name) {
                    continue;
                } else {
                    $boxes[] = (object) ['box_name' => $array[$i]->box_name, 'ip_address' => $array[$i]->ip_address, 'box_type' => $array[$i]->box_type, "keys" => $keys];
                    $keys = [];
                }
            }
        }

        // Set the envirenment
        $envir = [];

        foreach ($boxes as $box) {

            $equips = [];
            $pins_desc = [];
            $input_nbrs = [];
            $halls = [];

            foreach ($box->keys as $key) {
                $equips[] = $array[$key]->equip_name;
                $pins_desc[] = $array[$key]->pin_desc;
                $input_nbrs[] = $array[$key]->input_nbr;
                $halls[] = $array[$key]->hall;
            }

            $equips_names = [];

            for ($i = 0; $i < sizeof($equips); $i++) {

                if ($i == sizeof($equips) - 1) {

                    if ($equips[$i] == $equips[$i - 1]) {

                        $pins[] = (object)['pin_desc' => $pins_desc[$i], 'input_nbr' => $input_nbrs[$i], 'hall' => $halls[$i]];
                        $equips_names[] = (object)['equip_name' => $equips[$i], "pins" => $pins];
                        $pins = [];

                    } else {
                        $pins[] = (object)['pin_desc' => $pins_desc[$i], 'input_nbr' => $input_nbrs[$i], 'hall' => $halls[$i]];
                        $equips_names[] = (object)['equip_name' => $equips[$i], "pins" => $pins];
                        $pins = [];
                    }

                } else {

                    if ($equips[$i] == $equips[$i + 1]) {
                        $pins[] = (object)['pin_desc' => $pins_desc[$i], 'input_nbr' => $input_nbrs[$i], 'hall' => $halls[$i]];
                        continue;
                    } else {
                        $pins[] = (object)['pin_desc' => $pins_desc[$i], 'input_nbr' => $input_nbrs[$i], 'hall' => $halls[$i]];
                        $equips_names[] = (object)['equip_name' => $equips[$i], 'pins' => $pins];
                        $pins = [];
                    }
                }
            }
   
            $envir[] = (object)['box_name' => $box->box_name, 'ip_address' => $box->ip_address, 'box_type' => $box->box_type, 'equips' => $equips_names, 'spans' => sizeof($box->keys)];

        }

        return $envir;
    }
}
