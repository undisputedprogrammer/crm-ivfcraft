<?php

namespace App\Services;

use Exception;
use App\Models\Chat;
use App\Models\Lead;
use App\Models\Center;
use GuzzleHttp\Client;
use App\Models\Followup;
use App\Models\Hospital;
use Illuminate\Support\Facades\Storage;

class WhatsAppApiService
{
    public function message($request, $recipient, $lead)
    {
        $message = $request->message;
        $client = new Client();
        $hospital = Hospital::find($lead->hospital_id);
        $center = Center::find($lead->center_id);
        $postfields = array(
            "messaging_product"=> "whatsapp",
            "recipient_type"=> "individual",
            "to"=> $recipient,
            "type"=>"text",
            "text"=>array(
                "preview_url"=> false,
                "body"=>$message
            )
        );
        $json_postfields = json_encode($postfields);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v18.0/'.$center->phone_number_id.'/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_postfields,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'authkey: '.$hospital->authkey,
                'Authorization: Bearer '.$hospital->bearer_token
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response, true);
        if(isset($data['error'])){
            return ['status'=>'fail','message'=>'Cannot sent custom message'];
        }
        if($data['messages'] != null){
            $chat = Chat::create([
                'message'=>$message,
                'type' => 'text',
                'direction'=>'Outbound',
                'lead_id'=>$lead->id,
                'status'=>'submitted',
                'wamid'=>$data['messages'][0]['id'],

            ]);
            $data['status'] = 'success';
            $data['chat'] = $chat;
            return $data;
        }else{
            return ['status'=>'fail','message'=>'Cannot sent message'];
        }
    }

    public function sendMedia($request, $recipient, $lead)
    {
        //store media
        info('storing file');
        $ext = $request->file('media')->extension();
        $filename = 'media_'.$lead->id.'_'.time().'.'.$ext;
        info('filename is '.$filename);
        $path = $request->file('media')->move('storage/whatsapp/media',$filename);
        $media_url = env('APP_URL').$path;
        info('link to file is '.env('APP_URL').$path);

        $center = Center::find($lead->center_id);
        $hospital = Hospital::find($lead->hospital_id);

        $postfields = array(
            "messaging_product"=> "whatsapp",
            "recipient_type"=> "individual",
            "to"=> $recipient,
            "type"=>"image",
            "image"=>array(
                "link"=> env('APP_MODE') == 'dev' ? "https://i.pinimg.com/originals/be/33/76/be3376b0f835a1766cb7a95003ea4a7d.jpg" : $media_url,
                // change link to $media_url when deploying
                "caption" => "Nature is unbelievable"
            )
        );
        $json_postfields = json_encode($postfields);
        info('sending media message');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v18.0/'.$center->phone_number_id.'/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_postfields,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer '.$hospital->bearer_token
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        info($response);
        $data = json_decode($response, true);
        info('message sent to'.$recipient);
        if(isset($data['error'])){
            info('failed to sent message');
            return ['status'=>'fail','message'=>'Cannot sent media'];
        }
        if($data['messages'] != null){
            $chat = Chat::create([
                'message'=>$media_url,
                'type' => 'media',
                'direction'=>'Outbound',
                'lead_id'=>$lead->id,
                'status'=>'submitted',
                'wamid'=>$data['messages'][0]['id'],
            ]);
            $data['status'] = 'success';
            $data['chat'] = $chat;
            return $data;
        }else{
            return ['status'=>'fail','message'=>'Cannot sent message'];
        }
    }

    public function gettemplate($template_name)
    {
        $integrated_number = config('credentials.integrated_number');
        $authkeky = config('credentials.authkey');
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', 'https://control.msg91.com/api/v5/whatsapp/get-template-client/' . $integrated_number . '?template_name=' . $template_name, [
            'headers' => [
                'accept' => 'application/json',
                'authkey' => $authkeky,
            ],
        ]);

        $r = json_decode($response->getBody(), true);
        return $r;
    }

    public function getVariables($template_body)
    {
        $pattern = '/{{(.*?)}}/';

        preg_match_all($pattern, $template_body, $matches);

        $placeholders = $matches[0];

        return $placeholders;
    }

    public function getParameters($content)
    {
        $data = json_decode(json_encode($content), true);

        if ($data && isset($data['template']['components'])) {
            $components = json_decode($data['template']['components'], true);

            if ($components[0]['type'] === 'body' && isset($components[0]['parameters'])) {
                $parameters = $components[0]['parameters'];


                return $parameters;
            }
        }
    }

    public function renderMessage($template_body, $vars, $parameters)
    {
        $placeholderMap = [];

        foreach ($vars as $index => $var) {
            $placeholderMap[$var] = $parameters[$index]['text'];
        }

        $message = str_replace(array_keys($placeholderMap), array_values($placeholderMap), $template_body);

        return $message;
    }


    public static function bulkMessage($lead_id, $template){

        // Fetching lead or follow up details
        if ($lead_id) {
            $lead = Lead::where('id', $lead_id)->with(['followups', 'appointment'])->get()->first();
            $recipient = $lead->phone;
        }

        $hospital = Hospital::find($lead->hospital_id);
        $center = Center::find($lead->center_id);

        $params = json_decode($template->payload);

        if (count($params) == 0) {
            $components = [];
        } else {
            $components = array();
            array_push($components, array(
                'type' => 'body',
                'parameters' => array()
            ));
            foreach ($params as $param) {
                foreach ($param as $component => $data) {
                    $temp = explode('.', $data);
                    if ($temp[0] == 'Lead') {
                        array_shift($temp);
                        $data = $lead;
                        foreach ($temp as $i) {
                            if ($data[$i] == null) {
                                return response()->json(['status' => 'fail', 'message' => 'Invalid argument found']);
                            }
                            $data = $data[$i];
                        }

                        // return response($lead);
                    } elseif ($temp[0] == 'Followup') {
                        $followup = Followup::where('lead_id',$lead_id)->with('lead')->get()->first();
                        array_shift($temp);
                        $data = $followup;
                        foreach ($temp as $i) {
                            if ($data[$i] == null) {
                                return response()->json(['status' => 'fail', 'message' => 'Invalid argument found']);
                            }
                            $data = $data[$i];
                        }
                    }
                    // dd($temp);
                    array_push($components[0]['parameters'], array('type' => 'text', 'text' => $data));
                }
            }
        }

        // return response($components);
        // $recipient = $lead->phone;

        $payload = array(
            "name" => $template->template,
            "language" => array(
                "code" => "en",
                "policy" => "deterministic"
            ),
            "components" => json_encode($components),
        );


        $postfields = array(
            "integrated_number" => $center->phone,
            "lead_id" => $lead->id,
            "content_type" => "template",
            "type" => "template",
            "template" => $payload,
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $recipient
        );

        $json_postfields = json_encode($postfields);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v17.0/'.$center->phone_number_id.'/'.'messages/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_postfields,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'authkey: '.$hospital->authkey,
                'Authorization: Bearer '.$hospital->bearer_token
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response, true);

        if(isset($data['error'])){
            return response()->json(['status'=>'fail','message'=>'Sorry! Could not send message']);
        }
        $serviceObject = new WhatsAppApiService;
        if ($data['messages'] != null) {
            info('Message is submitted');
            info($data);
            $message_params = $components[0]['parameters'];
            $placeholders = $serviceObject->getVariables($template->body);
            $rendered_message = $serviceObject->renderMessage($template->body, $placeholders, $message_params);
            info($rendered_message);
            $chat = Chat::create([
                'message' => $rendered_message,
                'direction' => 'Outbound',
                'lead_id' => $lead->id,
                'status' => 'submitted',
                'wamid' => $data['messages'][0]['id'],
                // 'template_id'=>$template->id
            ]);

            $data['status'] = 'success';
            $data['chat'] = $chat;
        }

        return response(json_encode($data), 200);
    }

    public function markasread($wamid, $lead_id){
        $lead = Lead::find($lead_id);
        $hospital = Hospital::find($lead->hospital_id);
        $center = Center::find($lead->center_id);
        $postfields = array(
            "messaging_product"=> "whatsapp",
            "status"=> "read",
            "message_id"=> $wamid
        );
        $json_postfields = json_encode($postfields);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v18.0/'.$center->phone_number_id.'/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_postfields,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'authkey: '.$hospital->authkey,
                'Authorization: Bearer '.$hospital->bearer_token
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response, true);
        return $data;
    }

    public function downloadAndSave($lead, $id){
        $hospital = Hospital::find($lead->hospital_id);

        // fetching image download link from id
        $fetch_link_client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $hospital->bearer_token,
            ],
        ]);
        try {
            $response = $fetch_link_client->get('https://graph.facebook.com/v18.0/'.$id);

            if ($response->getStatusCode() === 200) {

                $response_array = json_decode($response->getBody(), true);
                $imagelink = $response_array['url'];

            } else {
                return ['success' => false,'message'=>"Failed to download the image."];
            }
        }catch(Exception $e){
            return ['success' => false,'message'=>"Failed to download the image."];
        }


        // downloading and saving the image from the image link fetched
        $client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $hospital->bearer_token,
            ],
        ]);

        try {
            $response = $client->get($imagelink);

            if ($response->getStatusCode() === 200) {

                $imageContent = $response->getBody();

                $filename = 'media_'.$lead->id.'_'.time().'.jpg';
                $storagePath = 'public/whatsapp/media/' . $filename;

                // $path = Storage::put($storagePath, $imageContent);
                $saved = Storage::disk('local')->put($storagePath, $imageContent);

                if($saved){
                    $storagePath = str_replace('public','storage',env('APP_URL').$storagePath);
                    return ['success' => true, 'path' => $storagePath];
                }
                else{
                    return ['success' => false, 'message' => "Could not save file"];
                }

            } else {
                return ['success' => false,'message'=>"Failed to download the image."];
            }
        }catch (Exception $e) {
            return ['success' => false, 'message' => "Error: " . $e->getMessage()];
        }
    }
}
