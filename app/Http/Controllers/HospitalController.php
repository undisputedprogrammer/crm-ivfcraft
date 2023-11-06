<?php

namespace App\Http\Controllers;

use App\Models\Center;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    public function centers(Request $request)
    {
        $centers = Center::where('hospital_id', $request->input('hospital'))->get();
        return response()->json(
            [
                'success' => true,
                'centers' => $centers
            ]
        );
    }
}
