<?php

namespace App\Imports;

use App\Helpers\PublicHelper;
use App\Models\Lead;
use App\Models\User;
use App\Models\Answer;
use App\Models\Campaign;
use App\Models\Followup;
use App\Models\Question;
use App\Models\Source;
use App\Services\PageService;
use Carbon\Carbon;
use Hamcrest\Type\IsNumeric;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use function PHPUnit\Framework\isNan;

class LeadsImport implements ToArray, WithHeadingRow
{
    private $campaign;
    private $agents = [];
    // private $x = null;
    private $currentAgentId = 0;
    private $hospital = null;
    private $center = null;
    private $headings = [];
    private $mainCols = [];
    private $totalCount = 0;
    private $importedCount = 0;
    public function __construct(array $headings, $hospital, $center, $agents = null, $campaign)
    {
        $this->headings = $headings;
        $this->hospital = $hospital;
        $this->campaign = $campaign;
        $this->center = $center;
        $this->mainCols = $hospital->main_cols;
        $this->agents = $agents ?? $center->agents();

        $last_lead = Lead::where('hospital_id', $hospital->id)->get()->last();

        if ($last_lead != null) {
            $this->currentAgentId = $this->getNextAgentId($last_lead->assigned_to);
        } else {
            $agentIds = $this->agents->pluck('id')->toArray();
            sort($agentIds, SORT_NUMERIC);
            $this->currentAgentId = $agentIds[0];
        }
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function array(array $rows)
    {
        //info('showing headings');
        //info($this->headings);
        // $row = $row[0];
        foreach ($rows as $row) {
            if($row[$this->mainCols->name] == null || $row[$this->mainCols->phone] == null){
                continue;
            }
            $this->totalCount++;
            $lquery = Lead::where(
                'phone',
                PublicHelper::formatPhoneNumber($row[$this->mainCols->phone])
            )->where('hospital_id', auth()->user()->hospital_id);
            if (auth()->user()->hospital_id == 2) {
                $lquery->where('campaign', $this->campaign);
            }
            $existing_lead = $lquery->get()->first();
            if ($existing_lead != null){
                $agentId = $existing_lead->id;
                continue;
            } else {
                $agentId = $this->currentAgentId;
            }

            /*** */
            $qarr = [];
            foreach ($this->headings as $colName) {
                if (!in_array($colName, [
                    $this->mainCols->name,
                    $this->mainCols->phone,
                    $this->mainCols->email,
                    $this->mainCols->city,
                    $this->mainCols->campaign
                ]) && $colName != '' && !is_numeric($colName)) {
                    $qarr[$colName] = $row[$colName];
                }
            }
            /*** */
            $source = PageService::getSource('FB', 'Facebook');

            //info("going to create lead");
            $lead = Lead::create([
                'name' => $row[$this->mainCols->name],
                'phone' => PublicHelper::formatPhoneNumber($row[$this->mainCols->phone]),
                'email' => $row[$this->mainCols->email] ?? '',
                'city' => $row[$this->mainCols->city] ?? '',
                'campaign' => $this->campaign,
                // 'campaign' => $row[$this->mainCols->campaign] ?? '',
                'qnas' => $qarr,
                'is_valid' => false,
                'is_genuine' => false,
                'history' => $row['history'] ?? '',
                'status' => 'Created',
                'followup_created' => false,
                'assigned_to' => $agentId,
                'hospital_id' => $this->hospital->id,
                'center_id' => $this->center->id,
                'created_by' => auth()->user()->id,
                'source_id' => $source->id
            ]);
            //info("lead created, assigned_to: ".$agentId);
            $this->createFollowup($lead);

            // $this->checkAndStoreCampaign($lead->campaign);

            $this->currentAgentId = $this->getNextAgentId($this->currentAgentId);

            // foreach ($this->getQuestionHeaders() as $qh) {
            //     $q = Question::where('question_code', $qh)->get()->first();
            //     $ans = Answer::create([
            //         'question_id' => $q->id,
            //         'lead_id' => $lead->id,
            //         'question_code' => $qh,
            //         'answer' => $row[strtolower($qh)]
            //     ]);
            // }
            $this->importedCount ++;
            // return $lead;
        }
    }

    public function createFollowup($lead){
        $pendingFolloup = DB::table('leads as l')
            ->join('followups as f', 'l.id', '=', 'f.lead_id')
            ->where('l.id', $lead->id)
            ->where('f.actual_date', null)
            ->get()->first();
        if (!isset($pendingFolloup)) {
            Followup::create([
                'lead_id' => $lead->id,
                'followup_count' => 1,
                'scheduled_date' => Carbon::today(),
                'user_id' => $lead->assigned_to
            ]);
        }

        $lead->followup_created = true;
        $lead->save();

        return null;
    }

    public function checkAndStoreCampaign($campaign){
        $campaign = ltrim(ucwords(strtolower($campaign)));
        $existing_campaign = Campaign::where('name', $campaign)->get()->first();
        if(!$existing_campaign && $campaign != ''){
            Campaign::create([
                'name' => $campaign
            ]);
        }

        return null;
    }

    private function getQuestionHeaders()
    {
        $h = [];
        foreach ($this->headings as $heading) {
            if (substr(strtoupper($heading), 0, 2) == 'Q_') {
                $h[] = strtoupper($heading);
            }
        }
        return $h;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    private function getNextAgentId($lastAssigned)
    {
        /*
        $x = 0;
        for($i = 0; $i < count($this->agents); $i++) {
            if (($this->agents[$i])->id == $lastAssigned) {
                $x = $i + 1;
                break;
            }
        }
        $x = $x < count($this->agents) ? $x : 0;

        return $x;
        */
        $x = 0;
        $agentIds = $this->agents->pluck('id')->toArray();
        sort($agentIds, SORT_NUMERIC);
        for($i = 0; $i < count($agentIds); $i++) {
            if ($agentIds[$i] == $lastAssigned) {
                $index = ($i + 1) < count($agentIds) ? $i + 1 : 0;
                $x = $index;
                break;
            }
        }

        return $agentIds[$x];
    }
}
