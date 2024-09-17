<?php

namespace App\Http\Controllers;

use App\Models\Adviser;
use App\Models\Solicitude;
use Illuminate\Http\Request;

class AdviserController extends Controller
{
    public function getToSelect()
    {
        return response()->json(
            Adviser::select('_id', 'adv_name')->get(),
            200
        );
    }  
}
