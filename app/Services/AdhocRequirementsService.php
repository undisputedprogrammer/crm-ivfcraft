<?php
namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Carbon;

class AdhocRequirementsService
{
    public function reassignedLeads($data)
    {
        $from = $data['from'] ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $to = $data['to'] ?? Carbon::now()->format('Y-m-d');
        $hid = $data['hid'] ?? 1;

        $leadsQuery = Lead::where('hospital_id', $hid)
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to);
        if(isset($data['cid'])) {
            $leadsQuery->where('center_id', $data['cid']);
        }

        $leads = $leadsQuery->get();

        $reassignedList = [];
        foreach ($leads as $l) {
            $originalAssignedUser = $l->followUps()->first()->user;
            $firstFollowupCreatedat = $l->followUps()->first()->created_at;
            if($originalAssignedUser->id != $l->assigned_to) {
                $reassignedList[] = [
                    'lead' => $l,
                    'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', $l->created_at)->format('d-m-Y'),
                    'first_followup_created_at' => $firstFollowupCreatedat,
                    'centre' => $l->center->name,
                    'original_assigned_id' => $originalAssignedUser->id,
                    'original_assigned' => $originalAssignedUser,
                    'current_assigned_id' => $l->assigned->id,
                    'current_assigned' => $l->assigned
                ];
            }
        }
        return $reassignedList;
    }
}
?>
