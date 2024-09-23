<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdviserResource;
use App\Models\Adviser;
use App\Models\Solicitude;
use Illuminate\Http\Request;

class AdviserController extends Controller
{
    public function getToSelect()
    {
        $advisers = Adviser::get();
        return response()->json([
           'data' =>AdviserResource::collection($advisers)
        ],  200);   
    }

    public function getAll()
    {
        $advisers = Adviser::get();
        return response()->json([
            'data' => $advisers
        ],  200);
    }
}
