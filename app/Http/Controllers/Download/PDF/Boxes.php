<?php

namespace App\Http\Controllers\Download\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use App\Models\UsersSite;

class Boxes extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }
    
    public function pdf(Request $request)
    {
        if ($request->data == 'null') {

            return redirect()->back();

        } else {
          
            $boxes_history = json_decode($request->data);

            $pdf = PDF::loadView('download.boxes', compact('boxes_history'))->setPaper('a4', 'landscape');

            return $pdf->stream('boxes_historique'.date('Y-m-d H:i:s').'.pdf');
        }
    }

}
