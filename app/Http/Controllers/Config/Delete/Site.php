<?php

namespace App\Http\Controllers\Config\Delete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Site extends Controller
{
    public function deleteSite($site_id)
    {
        // Delete site from all_sites table
        Site::find($site_id)->delete();

        return redirect()->back();
    }
}
