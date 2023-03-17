<?php

namespace App\Http\Controllers\Config\Edit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sites;
use App\Models\EquipsDetail;
use App\Models\EquipsNames;

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
        $this->editTheElements($site->site_name);

        return redirect()->back();
    }

    public function editTheElements($site_name)
    {
         
    }
}
