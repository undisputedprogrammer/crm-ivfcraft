<?php
namespace App\Helpers;

use App\Models\Campaign;

class PublicHelper{
    public static function formatPhoneNumber($phone){
        $phone = str_replace(['+','-',' '] ,'' ,$phone);
        if (substr($phone, 0, 1) === '0'){
            $phone = preg_replace('/0/', '', $phone, 1);
        }
        if(strlen($phone) == 10){
            $phone = "91".$phone;
        }
        return $phone;
    }

    public static function checkAndStoreCampaign($campaign){
        $campaign = ucwords(strtolower($campaign));
        $existing_campaign = Campaign::where('name', $campaign)->get()->first();
        if(!$existing_campaign && $campaign != ''){
            Campaign::create([
                'name' => $campaign
            ]);
        }

        return true;
    }
}

?>
