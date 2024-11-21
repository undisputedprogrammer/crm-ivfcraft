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

        $leadsQuery = Lead::where('created_at', '>=', $from)
            ->where('created_at', '<=', $to);
        if(isset($data['cid'])) {
            $leadsQuery->where('ceter_id', $data['cid']);
        }

        $leads = $leadsQuery->get();

        $reassignedList = [];
        foreach ($leads as $l) {
            $originalAssignedUser = $l->followUps()->first();
            if($originalAssignedUser->id != $l->assigned_to) {
                $leads[] = [
                    'lead' => $l,
                    'original_assigned' => $originalAssignedUser,
                    'current_assigned' => $l->assigned
                ];
            }
        }
        return $reassignedList;
    }
}
?>
