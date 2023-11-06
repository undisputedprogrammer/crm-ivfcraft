<?php
namespace App\Services;

use App\Models\Journal;
use Carbon\Carbon;
use PhpParser\Node\Expr\FuncCall;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;

class JournalService implements ModelViewConnector{
    use IsModelViewConnector;

    public function __construct()
    {
        $this->modelClass = Journal::class;
    }

    // public function getStoreValidationRules(): array
    // {
    //     return [
    //         'body' => ['required', 'string']
    //     ];
    // }

    public function storeJournal($request)
    {
        try{
            $today = Carbon::today()->toDateString();
            $existingJournal = Journal::where('user_id',auth()->user()->id)->whereDate('date',$today)->get()->first();

            if($existingJournal != null){
                $existingJournal->body = $request->body;
                $existingJournal->save();
                return compact('existingJournal');
            }else{
                $journal = Journal::create([
                    'user_id'=>auth()->user()->id,
                    'body'=>$request->body,
                    'date'=>Carbon::now()
                ]);
                return compact('journal');
            }
        }
        catch(\Exception $e){
            $error = $e->getMessage();
            return compact('error');
        }
    }
}

?>
