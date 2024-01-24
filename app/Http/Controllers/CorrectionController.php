<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CorrectionController{
    private $wordsList = [
        " dr Noushin Ashraf Leads"=>"Dr. Noushin Leads",
        " dr Noushin Maldives"=>"Dr. Noushin Maldives Leads",
        "3000 Package"=>"3000 offer ",
        "3000 Package Leads"=>"3000 offer ",
        "Advance Health Checkup"=>"Health Check Up ",
        "All  Kerala Leads"=>"All Kerala Leads",
        "All Kerala Leads "=>"All Kerala Leads",
        "All Kerala Leads Ivf Camp"=>"All Kerala Leads",
        "Basic Health Package"=>"Health Check Up ",
        "Call"=>"Direct Call",
        "Camp Perinthalmanna"=>"Perinthalmanna Leads",
        "Craft Dr Noushin Ashraf"=>"Dr. Noushin Leads",
        "Craft Dr Noushin Ashraf Leads"=>"Dr. Noushin Leads",
        "Craft Dr Noushin Maldive"=>"Dr. Noushin Maldives Leads",
        "Craft Dr Noushin Maldivie"=>"Dr. Noushin Maldives Leads",
        "Craft Swanthanam South Kerala"=>"Santhwanam South Kerala Leads",
        "Craft Swanthanam South Kerala Lead"=>"Santhwanam South Kerala Leads",
        "Craft Swanthanam South Kerala Leads"=>"Santhwanam South Kerala Leads",
        "Dr Ashraf"=>"Dr Ashraf Leads",
        "Dr Ashraf Lead"=>"Dr Ashraf Leads",
        "Dr Noushin"=>"Dr. Noushin Leads",
        "Dr Noushin Ashraf"=>"Dr. Noushin Leads",
        "Dr Noushin Ashraf Leads"=>"Dr. Noushin Leads",
        "Dr Noushin Lead"=>"Dr. Noushin Leads",
        "Dr Noushin Leads"=>"Dr. Noushin Leads",
        "Dr Noushin Maldives"=>"Dr. Noushin Maldives Leads",
        "Dr.noushin"=>"Dr. Noushin Leads",
        "Gastrology"=>"Gastroenterology",
        "Ghghk"=>"Email",
        "Gmail"=>"Email",
        "Google"=>"Google Call/Message",
        "Google Call"=>"Google Call/Message",
        "Health"=>"Health Check Up ",
        "Health Package"=>"Health Check Up ",
        "Health Package &  Pulmonology"=>"Health Check Up ",
        "Ivf Camp Perinthalmanna"=>"Perinthalmanna Leads",
        "Ivf Failed"=>"IVF Failed Leads",
        "Ivf Failed  Leads"=>"IVF Failed Leads",
        "Ivf Failed Case "=>"IVF Failed Leads",
        "Ivf Treatment Failed"=>"IVF Failed Leads",
        "Maldived"=>"Dr. Noushin Maldives Leads",
        "Maldives Lead"=>"Dr. Noushin Maldives Leads",
        "Maldives Leads"=>"Dr. Noushin Maldives Leads",
        "Male Infertility"=>"Male Infertility  Leads",
        "Male Infertility Leads"=>"Male Infertility  Leads",
        "Male Infertility Perinthalmanna"=>"Perinthalmanna Leads",
        "Male' Leads"=>"Dr. Noushin Maldives Leads",
        "Message"=>"Whats App/Text Message",
        "Msg"=>"Whats App/Text Message",
        "New Package"=>"3000 offer ",
        "New Package Leads"=>"3000 offer ",
        "North  Leads"=>"North Kerala Leads",
        "North Leads"=>"North Kerala Leads",
        "North Swanthanam Leads"=>"Santhwanam North Kerala Leads",
        "Ortho Enquiry"=>"Orthopeadic",
        "Orthopeadics"=>"Orthopeadic",
        "Orthopedics"=>"Orthopeadic",
        "Pcod"=>"Gyneacology",
        "Perinthalmanna Camp"=>"Perinthalmanna Leads",
        "Pulmonology & Health Package"=>"Health Check Up ",
        "Pulmonology And Health Package"=>"Health Check Up ",
        "Santhwanam Laeds"=>"Santhwanam South Kerala Leads",
        "Santhwanam Lead"=>"Santhwanam South Kerala Leads",
        "Santhwanam Leads"=>"Santhwanam South Kerala Leads",
        "South Kerala"=>"South Kerala Leads",
        "South Lead"=>"South Kerala Leads",
        "South Leads"=>"South Kerala Leads",
        "Surrogacy"=>"Infertility",
        "Swanthanam North   Leads"=>"Santhwanam North Kerala Leads",
        "Swanthanam North Leads"=>"Santhwanam North Kerala Leads",
        "Swanthanam South Kerala"=>"Santhwanam South Kerala Leads",
        "Swanthanam South Kerala Leads"=>"Santhwanam South Kerala Leads",
        "Tele"=>"Tele Desk",
        "Telly"=>"Tele Desk",
        "Tete"=>"Tele Desk",
        "Twe"=>"Tele Desk",
        "Whatsapp"=>"Whats App/Text Message",
    ];
    private $deleteList = [
        "7592941983",
        "Fb Leads"
    ];

    public function sanitizeCampaignNames()
    {
        try{
            foreach ($this->wordsList as $old => $new) {
                DB::table('leads')->where('campaign', $old)->update(['campaign' => $new]);
            }
            foreach ($this->deleteList as $item) {
                DB::table('leads')->where('campaign', $item)->delete();
            }
            return 'success';
        } catch (Exception $e) {
            return $e->__toString();
        }
    }
}
