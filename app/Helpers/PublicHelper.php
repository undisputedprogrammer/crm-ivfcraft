<?php
namespace App\Helpers;

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
}
?>
