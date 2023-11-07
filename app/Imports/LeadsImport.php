<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\User;
use App\Models\Answer;
use App\Models\Followup;
use App\Models\Question;
use Carbon\Carbon;
use Hamcrest\Type\IsNumeric;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use function PHPUnit\Framework\isNan;

class LeadsImport implements ToArray, WithHeadingRow
{
    private $agents = [];
    private $x = 0;
    private $hospital = null;
    private $center = null;
    private $headings = [];
    private $mainCols = [];
    private $totalCount = 0;
    private $importedCount = 0;
    public function __construct(array $headings, $hospital, $center, $agents = null)
    {
        $this->headings = $headings;
        $this->hospital = $hospital;
        $this->center = $center;
        $this->mainCols = $hospital->main_cols;
        $this->agents = $agents ?? $center->agents();
        if (count($this->agents) > 1) {
            $this->x = random_int(0, count($this->agents) - 1);
        } else {
            $this->x = 0;
        }
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function array(array $rows)
    {
        info('showing headings');
        info($this->headings);
        // $row = $row[0];
        foreach ($rows as $row) {
            if($row[$this->mainCols->name] == null || $row[$this->mainCols->phone] == null){
                continue;
            }
            $this->totalCount++;

            $existing_count = Lead::where('phone', $row[$this->mainCols->phone])->get()->count();
            if($existing_count > 0){
                continue;
            }

            /*** */
            $qarr = [];
            foreach ($this->headings as $colName) {
                if (!in_array($colName, [
                    $this->mainCols->name,
                    $this->mainCols->phone,
                    $this->mainCols->email,
                    $this->mainCols->city
                ]) && $colName != '' && !is_numeric($colName)) {
                    $qarr[$colName] = $row[$colName];
                }
            }
            /*** */

            $lead = Lead::create([
                'name' => $row[$this->mainCols->name],
                'phone' => $row[$this->mainCols->phone],
                'email' => $row[$this->mainCols->email] ?? '',
                'city' => $row[$this->mainCols->city] ?? '',
                'qnas' => $qarr,
                'is_valid' => false,
                'is_genuine' => false,
                'history' => $row['history'] ?? '',
                'customer_segment' => 'cold',
                'status' => 'Created',
                'followup_created' => false,
                'assigned_to' => $this->agents[$this->x]->id,
                'hospital_id' => $this->hospital->id,
                'center_id' => $this->center->id,
                'created_by' => auth()->user()->id
            ]);

            $this->createFollowup($lead);

            $this->x++;
            if ($this->x == count($this->agents)) {
                $this->x = 0;
            }

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
        Followup::create([
            'lead_id' => $lead->id,
            'followup_count' => 1,
            'scheduled_date' => Carbon::today(),
            'user_id' => $lead->assigned_to
        ]);

        $lead->followup_created = true;
        $lead->save();

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
}
