<?php
namespace App\Helpers;

class PublicHelper{
    public static function formatPhoneNumber($phone){
        $phone = str_replace(['+','-',' '] ,'' ,$phone);
        if(strlen($phone) == 10){
            $phone = "91".$phone;
        }
        return $phone;
    }
}
?>
