<?php

namespace App\Http\Controllers\Config\Edit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sites;
use App\Models\EquipsDetail;
use App\Models\EquipsNames;
use App\Models\UsersSite;
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
        Sites::find($site_id)->update(['site_name' => $request->site_name]);

        // Edit the site and its equips from equips_names table
        EquipsDetail::where('site_name', $site->site_name)->update(['site_name' => $request->site_name]);

        // Edit the site and its pins from equips_details table
        EquipsNames::where('site_name', $site->site_name)->update(['site_name' => $request->site_name]);

        // Edit the files from nagios 
        $this->editTheElements($site->site_name, $request->site_name);

        // Re-name current site for user
        $this->renameUserSite($request->site_name);

        return redirect()->back();
    }

    public function editTheElements($old_site_name, $new_site_name)
    {
        $element_of_site = DB::table('nagios_hosts')
            ->join('nagios_customvariables','nagios_hosts.host_object_id','=','nagios_customvariables.object_id')
            ->where('nagios_customvariables.varvalue',$old_site_name)
            ->select('nagios_hosts.display_name as element_name','nagios_hosts.host_object_id as element_object_id','nagios_hosts.alias as host_type')
            ->get();

        foreach ($element_of_site as $element) {

            if($element->host_type == 'host')
            {
                $directory = "hosts";
            }

            if ($element->host_type == 'box') {
                $directory = "boxes";
            }
        
            $myFile = "/usr/local/nagios/etc/objects/{$directory}/{$element->element_name}/{$element->element_name}.cfg";
            $lines = file($myFile);

            // Editing in host .cfg file
            $file_content = file_get_contents("/usr/local/nagios/etc/objects/{$directory}/{$element->element_name}/{$element->element_name}.cfg");
            $file_content = str_replace($lines[5], "\t_site\t\t\t{$new_site_name}\n", $file_content);
            file_put_contents("/usr/local/nagios/etc/objects/{$directory}/{$element->element_name}/{$element->element_name}.cfg", $file_content);

            DB::table('nagios_customvariables')->where('object_id', $element->element_object_id)->where('varname','SITE')->update(['varvalue' => $new_site_name]);
        }

        shell_exec('sudo service nagios stop');
        shell_exec('sudo service nagios start');
    }

    public function renameUserSite($new_site_name)
    {
        // Get the current user id
        $user_id = auth()->user()->id;

        // Get the current site the user seeing
        $current_site = UsersSite::where('user_id', $user_id)->first();

        // Update current site
        $current_site->update([
            'current_site' => $new_site_name,
        ]);

    }
}
