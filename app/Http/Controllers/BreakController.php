<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BreakController extends Controller
{
    public function breakIn(Request $request){
        $user = $request->user();

        $audit = Audit::where('user_id', $user->id)->where('logout',null)->get()->first();

        if($audit->break_in == null){
            $audit->break_in = Carbon::now();
            $audit->save();
            $user->in_break = true;
            $user->save();
            return response()->json(['success'=>true, 'message'=>'User in break','audit'=>$audit]);
        }
        else{
            return response()->json(['success'=>false, 'message'=>'Break is not available now']);
        }
    }

    public function breakOut(Request $request){

        $user = $request->user();

        $audit = Audit::where('user_id', $user->id)->where('logout',null)->get()->first();

        $validated = $request->validateWithBag('checkPassword', [
            'current_password' => ['required', 'current_password']
        ]);

        // if(Hash::make($request->password) != $user->password){
        //     return response()->json(['success'=>false, 'message'=>'Incorrect password']);
        // }

        if($audit->break_in != null){

            $audit->break_out = Carbon::now();
            $audit->save();
            $user->in_break = false;
            $user->save();

            return response()->json(['success'=>true, 'message'=>'Break Ended!','isBreak'=>$user->in_break]);
        }
        else{
            return response()->json(['success'=>false, 'message'=>'Invalid Request']);
        }
    }
}
