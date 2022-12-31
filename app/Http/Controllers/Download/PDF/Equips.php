<?php

namespace App\Http\Controllers\Download\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use App\Models\UsersSite;

class Equips extends Controller
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
          
            $equipements_history = json_decode($request->data);

            $pdf = PDF::loadView('download.equips', compact('equipements_history'))->setPaper('a4', 'landscape');

            return $pdf->stream('equipements_historique'.date('Y-m-d H:i:s').'.pdf');
        }
    }

}
