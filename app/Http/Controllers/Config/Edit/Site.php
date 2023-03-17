<?php

namespace App\Http\Controllers\Config\Edit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sites;
use App\Models\EquipsDetail;
use App\Models\EquipsNames;
use Illuminate\Support\Facades\DB;

class Site extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }

    public function editSite(Request $request,$site_id)
    {
        $site = Sites::find($site_id);

        // Update site from all_sites table
        // Sites::find($site_id)->update(['site_name' => $request->site_name]);

        // Edit the site and its equips from equips_names table
        // EquipsDetail::where('site_name', $site->site_name)->update(['site_name' => $request->site_name]);

        // Edit the site and its pins from equips_details table
        // EquipsNames::where('site_name', $site->site_name)->update(['site_name' => $request->site_name]);

        // Edit the files from nagios 
        $this->editTheElements($site->site_name);

        return redirect()->back();
    }

    public function editTheElements($site_name)
    {
        $boxes_of_site = DB::table('nagios_hosts')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varvalue',$site_name)
            ->select('nagios_hosts.display_name as box_name','nagios_hosts.host_object_id as box_object_id')
            ->get();

        foreach ($boxes_of_site as $box) {
            
            $file = "/usr/local/nagios/etc/objects/boxes/{$box->box_name}/{$box->bx_name}.cfg";
            $lines = file($file);
            $site_var_line = $lines[7];

            // Editing in host .cfg file
            $box_file_content = file_get_contents("/usr/local/nagios/etc/objects/{$box->box_name}/{$box->box_name}/{$box->box_name}.cfg");
            $box_file_content = str_replace($lines[7], '', $box_file_content);
            file_put_contents("/usr/local/nagios/etc/objects/{$box->box_name}/{$box->box_name}/{$box->box_name}.cfg", $box_file_content);

        }
    }
}
