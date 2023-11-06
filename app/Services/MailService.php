<?php

namespace App\Services;

use App\Mail\SendCustomMail;
use App\Models\Lead;
use App\Models\Doctor;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendCustomMail($request)
    {
        $lead = Lead::find($request->lead_id);
        $subject = $request->subject;
        $body = $request->body;
        $result = $this->findAndReplace($body, $lead);
        if($result['errors'] == true){
            return [
                'success' => false,
                'message' => isset($result['message']) ? $result['message'] : 'Could not process request'];
        }else{
            $data = [
                'subject' => $subject,
                'body' => $result['body']
            ];
            info('sending mail');
            Mail::to($lead->email)->send(new SendCustomMail($data));
        }
        return ['success' => true, 'message' => 'Message sent successfully'];
    }

    public function findAndReplace($body, $lead)
    {
        $string = $body;
        $pattern = '/\{([^}]+)\}/';

        if (preg_match_all($pattern, $string, $matches)) {
            $placeholders = $matches[1];
        } else {
            $placeholders = [];
        }

        if(in_array('appointment',$placeholders) || in_array('doctor',$placeholders)){
            if($lead->appointment == null){
                return ['errors'=>true, 'message'=>'Appointment not scheduled for the lead'];
            }
        }
        foreach ($placeholders as $placeholder)
        {
            if(!in_array($placeholder, ['name','phone','appointment','doctor'])){
                return ['errors'=>true, 'message'=>'Invalid placeholder found!'];
            }
            else{
                if($placeholder == 'name'){
                    $body = str_replace('{'.$placeholder.'}',$lead->name,$body);
                }
                if($placeholder == 'phone'){
                    $body = str_replace('{'.$placeholder.'}',$lead->phone, $body);
                }
                if($placeholder == 'appointment'){
                    $body = str_replace('{'.$placeholder.'}',$lead->appointment->appointment_date, $body);
                }
                if($placeholder == 'doctor'){
                    $doctor = Doctor::find($lead->appointment->doctor_id);
                    $body = str_replace('{'.$placeholder.'}',$doctor->name, $body);
                }
            }

        }

        return ['errors'=>false, 'body'=>$body];
    }
}
