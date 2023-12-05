<?php

namespace App\Http\Controllers;

use App\Helpers\PublicHelper;
use App\Models\Hospital;
use App\Models\Lead;
use App\Models\Source;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function setCallStatusForAllLeads(){
        $leads = Lead::whereHas('followups', function ($q){
            return $q->where('actual_date','!=',null);
        })->get();

        foreach($leads as $lead){
            $responsive_followups = $lead->followups()->where('call_status','Responsive')->get()->count();
            if($responsive_followups != 0){
                $lead->call_status = "Responsive";
            }else{
                $lead->call_status = "Not responsive";
            }
            $lead->save();
        }

        return redirect('/');
    }

    public function setDistinctSourcesForHospital(){
        $sources = Source::all();
        $craft = Hospital::find(1);
        $ar = Hospital::find(2); //replace with orginal ID
        $ar_leads = Lead::where('hospital_id', $ar->id)->get();
        foreach($sources as $source){
            $source->hospital_id = $craft->id;
            $source->save(); // existing source is assigned to craft

            $new_source = Source::create([
                'hospital_id' => $ar->id,
                'code' => $source->code,
                'name' => $source->name
            ]); //creating a same source for AR

            foreach($ar_leads as $lead){
                if($lead->source_id == $source->id){
                    $lead->source_id = $new_source->id;
                    $lead->save();
                }
            } // All leads of AR with the source_id of the existing source is assigned the ID of new source
        }

        return redirect('/');
    }

    public static function updateAllNumbers(){
        $leads = Lead::all();
        foreach($leads as $lead){
            $lead->phone = PublicHelper::formatPhoneNumber($lead->phone);
            $lead->save();
            $phone = $lead->phone;
            $dlq = Lead::where('phone', $lead->phone)->where('id', '!=', $lead->id)->orWhere('phone', $phone)->where('id','!=',$lead->id);
            $dupleads = $dlq->get()->count();
            if( $dupleads > 0){
                $dup_ids = $dlq->get()->pluck('id')->toArray();
                info($dupleads.' duplicate of '.$lead->phone.' in ids '.implode(',',$dup_ids));
            }
        }
    }
}
