<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sites;

class AddSite extends Controller
{
    public function __construct()
    {
        $this->middleware(['super_admin']);
    }

    public function addSite(Request $request)
    {
        $this->validate($request,[
            'site_name' => 'required|min:2|max:20|unique:am.all_sites,site_name|regex:/^[a-zA-Z0-9-_+ ]/',
        ]);

        $add_site = Sites::create([
            'site_name' => $request->site_name,
        ]);

        return back();
    }
}
