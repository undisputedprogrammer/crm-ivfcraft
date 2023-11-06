<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\User;
use App\Models\Audit;
use App\Models\Center;
use App\Models\Doctor;
use App\Models\Message;
use App\Models\Followup;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Services\PageService;
use Illuminate\Support\Facades\Auth;
use Ynotz\Metatags\Helpers\MetatagHelper;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class PageController extends SmartController
{
    private $pageService;

    public function __construct(Request $request, PageService $pageService)
    {
        parent::__construct($request);
        $this->pageService = $pageService;
    }

    public function overview()
    {
        $data = $this->pageService->getOverviewData();

        return $this->buildResponse('pages.overview', $data);
    }

    public function getAgents(Request $request)
    {
        return response()->json(
            $this->pageService->getAgents($request->input('cid'))
        );
    }


    public function performance(Request $request)
    {
        info('calling getoverviewdata function');
        $overview = $this->pageService->getOverviewData($request->month);
        $performance = $this->pageService->agentsPerformance($request->month);
// dd(array_merge($overview, $performance));

        return $this->buildResponse('pages.performance', array_merge($overview, $performance));
    }


    public function leadIndex(Request $request)
    {

        $data = $this->pageService->getLeads($request->user(),$request->selectedLeads,$request->center,$request->search, $request->status, $request->is_valid, $request->is_genuine, $request->creation_date, $request->processed);

        return $this->buildResponse('pages.leads', $data);

    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $audit = Audit::where('user_id',$user->id)->where('logout',null)->latest()->get()->first();

        if($audit != null){

            $audit->logout = Carbon::now();

            $audit->save();
        }

        return redirect('/login');
    }

    public function home()
    {
        return redirect('/overview');
    }

    public function followUps(Request $request)
    {
        $data = $this->pageService->getFollowupData($request->user(),$request->center);

        return $this->buildResponse('pages.followups', $data);
    }

    public function searchIndex(Request $request)
    {
        $agents = User::where('hospital_id',$request->user()->hospital_id)->whereHas('roles',function($q){
            $q->where('name','agent');
        })->with('centers')->get();
        $centers = Center::where('hospital_id', $request->user()->hospital_id)->get();

        return $this->buildResponse('pages.search', compact('agents','centers'));
    }

    public function questionIndex(Request $request)
    {
        $questions = Question::orderBy('created_at', 'desc')->paginate(8);

        return $this->buildResponse('pages.manage-questions', compact('questions'));
    }

    public function showFollowup(Request $request, $id)
    {
        $data = $this->pageService->getSingleFollowupData($request->user(),$id);

        return $this->buildResponse('pages.show-followup', $data);
    }

    public function compose(Request $request, $id){
        info('Viewing compose mail page');
        $lead = Lead::find($id);
        return $this->buildResponse('pages.compose-email',compact('lead'));
    }
}
