<?php

namespace App\Http\Controllers\Download\Excel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\EquipsExcel;
use Excel;
use App\Models\UsersSite;

class Equips extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function csv(Request $request)
    {

        if ($request->data == 'null') {

            return redirect()->back();

        } else {

            return Excel::download(new EquipsExcel(json_decode($request->data)), 'equips_historique '.date('Y-m-d H:i:s').'.xlsx');
        
        }
    }

}
