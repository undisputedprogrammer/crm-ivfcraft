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
        Source::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
        ]);
        return response()->json(['success'=>true, 'message' => 'New source added !']);
    }

    public function fetch(Request $request){
        $sources = Source::all();
        return response()->json(['sources'=> $sources]);
    }

    public function setSource(){
        $sources = [
            ['code' => 'FB', 'name' => 'Facebook'],
            ['code' => 'WA', 'name' => 'WhatsApp']
        ];

        // $non_import_leads = [];

        // $non_import_source = Source::create(['code'=>'', 'name'=>'']);

        foreach($sources as $source){
            Source::create([
                'code' => $source['code'],
                'name' => $source['name']
            ]);
        }
        $facebook_source = Source::where('code','FB')->get()->first();

        $leads= Lead::where('source_id', null)->get();

        foreach($leads as $lead){
            // if(!in_array($lead->phone, $non_import_leads)){
                $lead->source_id = $facebook_source->id;
                $lead->save();
            // }
            // else{
            //     $lead->source_id = $non_import_source->id;
            //     $lead->save();
            // }
        }

        return redirect('/sources');
    }
}
