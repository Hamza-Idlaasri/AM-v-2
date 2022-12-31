<?php

namespace App\Http\Controllers\Download\Excel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\BoxesExcel;
use Excel;
use App\Models\UsersSite;

class Boxes extends Controller
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

            return Excel::download(new BoxesExcel(json_decode($request->data)), 'boxes_historique '.date('Y-m-d H:i:s').'.xlsx');
        }
    }

}
