<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class SourceController extends SmartController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function store(Request $request){
        $if_source_exist = Source::where('hospital_id', auth()->user()->hospital_id)->where('code',strtoupper($request->code))->orWhere('name',$request->name)->where('hospital_id',auth()->user()->hospital_id)->get()->first();

        if($if_source_exist){
            return response()->json(['success'=>false, 'message'=>'Source with same name or code already exist']);
        }

        Source::create([
            'hospital_id' => auth()->user()->hospital_id,
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'forms' => is_array($request->forms) ? implode(',',$request->forms) : ''
        ]);
        return response()->json(['success'=>true, 'message' => 'New source added !']);
    }

    public function fetch(Request $request){
        $sources = Source::where('hospital_id',auth()->user()->hospital_id)->get();
        return response()->json(['sources'=> $sources]);
    }

    public function update(Request $request){
        $source = Source::find($request->source_id);

        $if_source_exist = Source::where('hospital_id', auth()->user()->hospital_id)->where('code',strtoupper($request->code))->orWhere('name',$request->name)->where('hospital_id',auth()->user()->hospital_id)->get();

        if($if_source_exist->count() > 1 && $if_source_exist->first()->id != $request->source_id){
            return response()->json(['success'=>false, 'message'=>'Source with same name or code already exist']);
        }

        if(in_array($source->code, ['IRF'])){
            return response()->json(['success'=>false, 'message' => 'Updating this source is restricted']);
        }

        if(!$source){
            return response()->json(['success' => true, 'message' => 'Could not fetch source !']);
        }
        info(filter_var($request->is_enabled, FILTER_VALIDATE_BOOL));

        $source->update([
            'code' => $request->code,
            'name' => $request->name,
            'forms' => is_array($request->forms) ? implode(',', $request->forms) : '',
            'is_enabled' => filter_var($request->is_enabled, FILTER_VALIDATE_BOOL) == 1 ? true : false
        ]);

        return response()->json(['success' => true, 'message' => 'Source updated !']);
    }

}
