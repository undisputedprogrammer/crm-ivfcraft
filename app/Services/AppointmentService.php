<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Doctor;
use App\Models\Remark;
use App\Models\Followup;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;

class AppointmentService implements ModelViewConnector
{
    use IsModelViewConnector;

    public function __construct()
    {
        $this->modelClass = Doctor::class;
    }

    public function getStoreValidationRules(): array
    {
        return [
            'name' => ['required', 'string'],
            'department' => ['sometimes', 'string']
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'name' => ['required', 'string'],
            'department' => ['sometimes','nullable', 'string']
        ];
    }

    public function processAndStore($request){
        $appointment_date = Carbon::parse($request->appointment_date);
        $followup_date = Carbon::parse($request->followup_date);
        if($followup_date->lessThan($appointment_date)){
            return ['success'=>false, 'message'=>'Follow up date should be after Appointment date'];
        }
        if($appointment_date->isPast() && !$appointment_date->isToday()){
            return ['success'=>false, 'message'=>'You cannot input previous dates.'];
        }

        $lead = Lead::find($request->lead_id);
        $lead->status = "Appointment Fixed";
        $lead->followup_created = true;
        $lead->save();

        $appointment = Appointment::create([
            'lead_id' => $request->lead_id,
            'doctor_id' => $request->doctor,
            'appointment_date' => Carbon::createFromFormat('Y-m-d', $request->appointment_date)->format('Y-m-d H:i:s')
        ]);


            $followup = Followup::where('id', $request->followup_id)->with('remarks')->get()->first();
            $followup->converted = true;
            $followup->actual_date = Carbon::now();
            $followup->next_followup_date = Carbon::createFromFormat('Y-m-d', $request->followup_date);
            $followup->user_id = Auth::user()->id;
            $followup->call_status = 'Responsive';
            $followup->save();
            $followup->refresh();

            Remark::create([
                'remarkable_type' => Followup::class,
                'remarkable_id' => $followup->id,
                'remark' => 'Appointment fixed on ' . $request->appointment_date,
                'user_id' => Auth::user()->id,
            ]);

            $next_followup = Followup::create([
                'lead_id' => $request->lead_id,
                'followup_count' => $followup->followup_count + 1,
                'scheduled_date' => $followup->next_followup_date,
                'converted' => true,
                'user_id' =>Auth::user()->id
            ]);

            return ['success' => true, 'message' => 'Remark added and converted', 'converted' => true, 'followup' => $followup, 'lead' => $lead, 'appointment' => $appointment,'next_followup'=>$next_followup];

    }

    public function processConsult($lead_id, $followup_id, $followup_date)
    {
        $lead = Lead::where('id',$lead_id)->with('appointment')->get()->first();
        // $date = Carbon::createFromFormat('d-m-Y', substr($lead->appointment->appointment_date,0,10));
        $date = Carbon::parse($lead->appointment->appointment_date);

        if($date->isPast()){
            $followup = Followup::find($followup_id);
            $followup->consulted = true;
            $followup->actual_date = Carbon::now();
            $followup->next_followup_date = Carbon::createFromFormat('Y-m-d', $followup_date)->format('Y-m-d H:i:s');
            $followup->call_status = 'Responsive';
            $followup->save();

            $lead->status = 'Consulted';
            $lead->save();

            $appointment = Appointment::find($lead->appointment->id);
            $appointment->consulted_date = Carbon::now();
            $appointment->save();

            $next_followup = Followup::create([
                'lead_id' => $lead_id,
                'followup_count' => $followup->followup_count + 1,
                'scheduled_date' => $followup->next_followup_date,
                'converted' => true,
                'consulted' => true,
                'user_id' => Auth::user()->id
            ]);

            return ['success'=>true, 'lead'=>$lead, 'followup'=>$followup, 'appointment'=>$appointment, 'next_followup' => $next_followup, 'message'=>'Consult is marked'];
        }
        return ['success'=>false, 'lead'=>$lead, 'message'=>'Appointment date has not reached'];
    }

    public function updateAppointment($request){

        $validate = $this->updateValidation($request);
        if($validate == false){
            return ['success'=>false, 'message'=>'Could not reschedule appointmet'];
        }

        $appointment_date = Carbon::parse($request->appointment_date);
        $followup_date = Carbon::parse($request->followup_date);

        if($followup_date->isPast($appointment_date)){
            if(!$followup_date->isSameAs('Y-m-j',$appointment_date)){
                return ['success'=>false, 'message'=>'Invalid Follow-up date'];
            }
        }

        $appointment = Appointment::find($request->appointment_id);

        if($appointment_date->isSameDay(Carbon::parse($appointment->appointment_date))){
            return ['success'=>false, 'message'=>'Appointment already on the same date'];
        }

        if($appointment_date->isPast(Carbon::today())){
            return ['success'=>false, 'message'=>'Appointment cannot be fixed to past date'];
        }

        $appointment->doctor_id = $request->doctor;
        $appointment->appointment_date = $appointment_date;
        $appointment->save();

        if($request->followup_id){
            Remark::create([
                'remarkable_type' => Followup::class,
                'remarkable_id' => $request->followup_id,
                'remark' => 'Appointment Rescheduled to ' . $appointment->appointment_date->format('Y-m-d'),
                'user_id' => Auth::id(),
            ]);

            $followup = Followup::find($request->followup_id);
            $followup->actual_date = Carbon::now();
            $followup->next_followup_date = $followup_date;
            $followup->call_status = 'Responsive';
            $followup->save();
            $followup->refresh();

            $next_followup = Followup::create([
                'lead_id' => $request->lead_id,
                'followup_count' => $followup->followup_count + 1,
                'scheduled_date' => $followup_date,
                'converted' => true,
                'user_id' => Auth::user()->id
            ]);
        }

        return ['success'=>true,'message'=>'Appointment Rescheduled', 'followup' => $followup, 'next_followup' => $next_followup, 'appointment' => $appointment];
    }

    public function updateValidation($request){
        $validator = Validator::make($request->all(), [
            'doctor' => 'required',
            'appointment_date' => 'required',
            'followup_date' => 'required',
            'followup_id' => 'required',
            'lead_id' => 'required',
            'appointment_id' => 'required'
        ]);

        if ($validator->fails()) {
            return false;
        }else{
            return true;
        }
    }

}
