<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Doctor;
use App\Models\Remark;
use App\Models\Followup;
use App\Models\Appointment;
use App\Models\Procedure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;

class ProcedureService implements ModelViewConnector
{
    use IsModelViewConnector;

    public function __construct()
    {
        $this->modelClass = Doctor::class;
    }

    // public function getStoreValidationRules(): array
    // {
    //     return [
    //         'name' => ['required', 'string'],
    //         'department' => ['sometimes', 'string']
    //     ];
    // }

    // public function getUpdateValidationRules(): array
    // {
    //     return [
    //         'name' => ['required', 'string'],
    //         'department' => ['sometimes', 'nullable', 'string']
    //     ];
    // }

    public function processAndStore($request)
    {
        $procedure_date = Carbon::parse($request->procedure_date);
        $followup_date = Carbon::parse($request->followup_date);
        // if ($followup_date->lessThan($appointment_date)) {
        //     return ['success' => false, 'message' => 'Follow up date should be after Appointment date'];
        // }
        if ($procedure_date->isPast() && !$procedure_date->isToday()) {
            return ['success' => false, 'message' => 'You cannot input previous dates.'];
        }

        $lead = Lead::find($request->lead_id);
        $lead->status = "Procedure Scheduled";
        $lead->followup_created = true;
        $lead->call_status = "Responsive";
        $lead->save();

        $procedure = Procedure::create([
            'lead_id' => $request->lead_id,
            'doctor_id' => $request->doctor,
            'procedure_scheduled_date' => Carbon::createFromFormat('Y-m-d', $request->procedure_date)->format('Y-m-d H:i:s')
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
            'remark' => 'Procedure Scheduled on ' . Carbon::createFromFormat('Y-m-d', $request->procedure_date)->format('d M Y'),
            'user_id' => Auth::user()->id,
        ]);
        $pendingFolloup = DB::table('leads as l')
            ->join('followups as f', 'l.id', '=', 'f.lead_id')
            ->where('l.id', $request->lead_id)
            ->where('f.actual_date', null)
            ->get()->first();
        if(!isset($pendingFolloup)) {
            $next_followup = Followup::create([
                'lead_id' => $request->lead_id,
                'followup_count' => $followup->followup_count + 1,
                'scheduled_date' => $followup->next_followup_date,
                'converted' => true,
                'user_id' => Auth::user()->id
            ]);
        } else {
            $next_followup = $pendingFolloup;
        }

        return ['success' => true, 'message' => 'Procedure created', 'converted' => true, 'followup' => $followup, 'lead' => $lead, 'procedure' => $procedure, 'next_followup' => $next_followup];
    }

    public function processProcedure($lead_id, $followup_id, $followup_date)
    {
        $lead = Lead::where('id', $lead_id)->with('procedure')->get()->first();

        $date = Carbon::parse($lead->procedure->procedure_scheduled_date);


        $followup = Followup::find($followup_id);
        $followup->consulted = true;
        $followup->actual_date = Carbon::now();
        $followup->next_followup_date = Carbon::createFromFormat('Y-m-d', $followup_date)->format('Y-m-d H:i:s');
        $followup->call_status = 'Responsive';
        $followup->user_id = Auth::user()->id;
        $followup->save();

        Remark::create([
            'remarkable_type' => Followup::class,
            'remarkable_id' => $followup->id,
            'remark' => 'Procedure completed on ' . Carbon::now()->format('d M Y'),
            'user_id' => Auth::user()->id,
        ]);

        $lead->status = 'Completed';
        $lead->save();

        $procedure = Procedure::find($lead->procedure->id);
        $procedure->procedure_done_date = Carbon::now();
        $procedure->save();
        $pendingFolloup = DB::table('leads as l')
            ->join('followups as f', 'l.id', '=', 'f.lead_id')
            ->where('l.id', $lead->id)
            ->where('f.actual_date', null)
            ->get()->first();
        if (!isset($pendingFolloup)) {
            $next_followup = Followup::create([
                'lead_id' => $lead_id,
                'followup_count' => $followup->followup_count + 1,
                'scheduled_date' => $followup->next_followup_date,
                'converted' => true,
                'consulted' => true,
                'user_id' => Auth::user()->id
            ]);
        } else {
            $next_followup = $pendingFolloup;
        }

        return ['success' => true, 'lead' => $lead, 'followup' => $followup, 'procedure' => $procedure, 'next_followup' => $next_followup, 'message' => 'Marked as procedure completed'];
    }

    public function updateProcedure($request)
    {

        $validate = $this->updateValidation($request);
        if ($validate == false) {
            return ['success' => false, 'message' => 'Could not reschedule procedure'];
        }

        $procedure_date = Carbon::parse($request->procedure_date);
        $followup_date = Carbon::parse($request->followup_date);

        if ($followup_date->isPast($procedure_date)) {
            if (!$followup_date->isSameAs('Y-m-j', $procedure_date)) {
                return ['success' => false, 'message' => 'Invalid Follow-up date'];
            }
        }

        $procedure = Appointment::find($request->procedure_id);

        if ($procedure_date->isSameDay(Carbon::parse($procedure->procedure_scheduled_date))) {
            return ['success' => false, 'message' => 'Procedure already on the same date'];
        }

        if ($procedure_date->isPast(Carbon::today())) {
            return ['success' => false, 'message' => 'Procedure cannot be scheduled to a past date'];
        }

        $procedure->doctor_id = $request->doctor;
        $procedure->procedure_scheduled_date = $procedure_date;
        $procedure->save();

        if ($request->followup_id) {
            Remark::create([
                'remarkable_type' => Followup::class,
                'remarkable_id' => $request->followup_id,
                'remark' => 'Procedure rescheduled to ' . $procedure->procedure_scheduled_date->format('Y-m-d'),
                'user_id' => Auth::id(),
            ]);

            $followup = Followup::find($request->followup_id);
            $followup->actual_date = Carbon::now();
            $followup->next_followup_date = $followup_date;
            $followup->call_status = 'Responsive';
            $followup->user_id = Auth::user()->id;
            $followup->save();
            $followup->refresh();

            $pendingFolloup = DB::table('leads as l')
            ->join('followups as f', 'l.id', '=', 'f.lead_id')
            ->where('l.id', $request->lead_id)
            ->where('f.actual_date', null)
            ->get()->first();
            if (!isset($pendingFolloup)) {
                $next_followup = Followup::create([
                    'lead_id' => $request->lead_id,
                    'followup_count' => $followup->followup_count + 1,
                    'scheduled_date' => $followup_date,
                    'converted' => true,
                    'user_id' => Auth::user()->id
                ]);
            } else {
                $next_followup = $pendingFolloup;
            }
        }

        return ['success' => true, 'message' => 'Procedure Rescheduled', 'followup' => $followup, 'next_followup' => $next_followup, 'procedure' => $procedure];
    }

    public function updateValidation($request)
    {
        $validator = Validator::make($request->all(), [
            'doctor' => 'required',
            'procedure_date' => 'required',
            'followup_date' => 'required',
            'followup_id' => 'required',
            'lead_id' => 'required',
            'appointment_id' => 'required'
        ]);

        if ($validator->fails()) {
            return false;
        } else {
            return true;
        }
    }

    public function makeAppointmentifNotExist($lead_id, $consulted_date, $doctor){
        $lead = Lead::find($lead_id);

        $appointment = Appointment::create([
            'lead_id' => $lead->id,
            'doctor_id' => $doctor,
            'appointment_date' => Carbon::createFromFormat('Y-m-d', $consulted_date)
        ]);

        $lead->status = "Appointment Fixed";
        $lead->save();
    }
}
