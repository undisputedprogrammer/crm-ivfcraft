<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Chat;
use App\Models\Followup;
use App\Models\Lead;
use App\Models\Remark;
use Carbon\Carbon;
use Exception;
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

    public function completedToConsulted()
    {
        DB::beginTransaction();
        try{
                $leads = Lead::where('status', 'Completed')->get();
                foreach ($leads as $l) {
                    $l->status = 'Consulted';
                    $l->save();
                    info('lead saved id: '. $l->id);
                    $last_followup = Followup::where('lead_id', $l->id)->orderBy('id', 'desc')->first();
                    $fcount = $last_followup != null ? $last_followup->followup_count + 1 : 1;
                    Followup::create([
                        'lead_id' => $l->id,
                        'followup_count' => $fcount,
                        'scheduled_date' => Carbon::today()->addDays(7),
                        'user_id' => $l->assigned_to
                    ]);
                    info('followup created');
                }
                $lids = $leads->pluck('id')->toArray();
                info('Reassigned status for '.count($lids).'leads:');
                info($lids);
                DB::commit();
                return 'success';
        } catch (Exception $e) {
            DB::rollBack();
            return $e->__toString();
        }
    }

    public function removeDuplicates()
    {
        $phones = [
            // "919995777237",
            // "919995223299",
            // "919526193082",
            // "9895314632",
            // "919746870222",
            // "918075435598",
            // "919400350617",
            // "918089759984",
            // "919656329248",
            // "919846863946",
            // "919526056747",
            // "919895706798",
            // "919037054324",
            // "918714294499",
            // "919947094230",
            // "919539277196",
            // "919495570000",
            // "919947389063",
            // "917559953261",
            // "919745777981",
            // "918301885937",
            // "919746728471",
            // "918156931825",
            // "917736371405",
            // "917204707820",
            // "919400794200",
            // "918137810200",
            // "917025584095",
            // "7012757856",
            // "919388388111",
            // "919645941106",
            // "919061471746",
            // "917306366335",
            // "917012719641",
            // "919562385759",
            // "919746915664",
            // "919747920538",
            // "916238301800",
            // "919846266060",
            // "917306607093",
            // "919846921364",
            // "919846809993",
            // "971554159180",
            // "919037400861",
            // "918590514187",
            // "918714491170",
            // "918086242404",
            // "919400834068",
            // "918304953218",
            // "919846961419",
            // "919847417681",
            // "919745490018",
            // "919744717171",
            // "917592862882",
            // "918891617556",
            // "919845395280",
            // "919745003904",
            // "919526499374",
            // "919946409829",
            // "919744863333",
            // "918590408284",
            // "918586007640",
            // "919961566102",
            // "919544755207",
            // "918075307194",
            // "919607561868",
            // "918943314876",
            // "919946055522",
            // "919539905166",
            // "919847375134",
            // "919864192696",
            // "919072486189",
            // "919895288457",
            // "918891038491",
            // "919495576496",
            // "918078096069",
            // "917025437395",
            // "919847253685",
            // "919544882961",
            // "917736362643",
            // "919446480450",
            // "919846199283",
            // "919744273799",
            // "919400025633",
            // "12502647495",
            // "918547589880",
            // "919847362622",
            // "919846306396",
            // "919946153022",
            // "919747510886",
            // "966532332834",
            // "919744856272",
            // "919995588626",
            // "918089775134",
            // "917994544062",
            // "919747886586",
            // "919447833627",
            // "919633951282",
            // "918606680103",
            // "917034147044",
            // "919539155421",
            // "919656035998",
            // "917356045950",
            // "918639546128",
            // "918281443660",
            // "919400517684",
            // "918606264203",
            // "919497787585",
            // "919745399112",
            // "917994680210",
            // "61412075123",
            // "919567258827",
            // "918921843272",
            // "918089408820",
        ];
        try{
            DB::beginTransaction();
            $count = 0;
            $x = 0;
            foreach ($phones as $phone) {
                $leads = Lead::where('phone', $phone)->orderBy('created_at', 'ASC')->get();
                $doCount = false;
                for($i = 1; $i < count($leads); $i++) {
                    if ($leads[$i]->status != 'Closed') {
                        $leads[$i]->status = 'Closed';
                        $leads[$i]->save();
                    }
                    Remark::create([
                        'remarkable_type' => Lead::class,
                        'remarkable_id' => $leads[$i]->id,
                        'remark' => "DUPLICATE LEAD DETECTED BY SYSTEM: ** DON'T RE-OPEN **",
                        'user_id' => $leads[$i]->assigned_to
                    ]);
                    $x++;
                    $count++;
                }
            }
            DB::commit();
            return response('Dupicates removed safely - count -> leads_count: '. $count .' -> ' . $x, 200);
        } catch(Exception $e) {
            DB::rollBack();
            info('Failed to remove duplicates');
            return response('Failed to remove duplicates: '. $e->__toString(), 200);
        }
    }
}
