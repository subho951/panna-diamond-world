<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\SiteAuthService;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use App\Models\LeadHeader;
use App\Models\CampaignType;
use App\Models\Campaign;
use App\Models\MasterLead;
use App\Models\User;
use App\Models\Branch;
use App\Models\LeadActivity;
use App\Models\GeneralSetting;
use App\Models\UserActivity;
use App\Models\BranchLead;
use App\Models\LeadStatus;
use App\Models\Country;
use App\Models\State;
use App\Models\Source;
use App\Models\Purpose;
use App\Models\Mood;
use App\Models\LeadTransfer;
use App\Models\FeedbackTag;

class ReportController extends Controller
{
    protected $siteAuthService;
    protected $data;
    public function __construct()
    {
        $this->siteAuthService = new SiteAuthService();
        $this->data = array(
            'title' => 'Report',
            'controller' => 'ReportController',
            'controller_route' => 'report',
            'primary_key' => 'id',
        );
    }

    public function activityReport(Request $request)
    {
        $data['module'] = $this->data;


        $query = LeadActivity::query();
        //filters
        if (!empty($request->input('call-from-date'))) {
            $callFromDate = $request->input('call-from-date');
            $data['callFromDate'] = $callFromDate;

            $query->whereDate('created_at', '>=', $callFromDate);
        }

        if (!empty($request->input('call-to-date'))) {
            $callToDate = $request->input('call-to-date');
            $data['callToDate'] = $callToDate;

            $query->whereDate('created_at', '<=', $callToDate);
        }

        $query->where('status', '!=', 3)->orderBy('id', 'desc');




        $allBranch = Branch::where('status', '!=', 3)->get();

        $branchWiseTelecallerActivity = [];
        foreach ($allBranch as $eachBranch) {
            if (session('user_data')['role_id'] == 3) {
                $this_telecaller_id = session('user_data')['user_id'];
                $branchWiseTelecaller = User::where('id', '=', $this_telecaller_id)->where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
            } else {
                $branchWiseTelecaller = User::where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
            }

            $telecallerActivity = [];
            foreach ($branchWiseTelecaller as $eachTelecaller) {

                $tellecallerWisePendingCount = BranchLead::where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('parent_status_id', '=', 0)->where('child_status_id', '=', 0)->where('status', '!=', 3)->count();

                
                if (!empty($request->input('unique')))
                {
                    $data['unique_check'] = 'checked';

                    // how many unique leads the telecaller called (koto jon k call koreche setar last activity)
                    $telecallerWiseLeads = (clone $query)
                        ->where('assigned_telecaller_id', $eachTelecaller->id)
                        ->whereIn('id', function ($sub) use ($eachTelecaller) {
                            $sub->selectRaw('MAX(id)')
                                ->from('lead_activities')
                                ->where('assigned_telecaller_id', $eachTelecaller->id)
                                ->groupBy('lead_sl_no');
                        })
                        ->get();
                } else {
                    // how many times the telecaller called (koto bar call koreche)
                    $telecallerWiseLeads = (clone $query)->where('assigned_telecaller_id', '=', $eachTelecaller->id)->get();
                }



                $parentStatus_new_count = 0;
                $parentStatus_new_count_idArr = [];

                $parentStatus_dumb_count = 0;
                $parentStatus_dumb_count_idArr = [];

                $parentStatus_followUp_count = 0;
                $parentStatus_followUp_count_idArr = [];

                $parentStatus_success_count = 0;
                $parentStatus_success_count_idArr = [];

                $total_call_count = 0;
                $total_call_count_idArr = [];

                foreach ($telecallerWiseLeads as $eachLead) {
                    $total_call_count++;
                    $total_call_count_idArr[] = $eachLead->id;

                    if ($eachLead->parent_status_id == 0 || $eachLead->parent_status_id == 12) {
                        $parentStatus_new_count++;
                        $parentStatus_new_count_idArr[] = $eachLead->id;
                    } elseif ($eachLead->parent_status_id == 1) {
                        $parentStatus_dumb_count++;
                        $parentStatus_dumb_count_idArr[] = $eachLead->id;
                    } elseif ($eachLead->parent_status_id == 5) {
                        $parentStatus_followUp_count++;
                        $parentStatus_followUp_count_idArr[] = $eachLead->id;
                    } elseif ($eachLead->parent_status_id == 10) {
                        $parentStatus_success_count++;
                        $parentStatus_success_count_idArr[] = $eachLead->id;
                    }
                }

                // last call of of telecaller
                $lastCallOfTelecaller = LeadActivity::where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->orderBy('id', 'desc')->first();
                if (!empty($lastCallOfTelecaller)) {
                    $formatedLastCallOfTelecaller = $lastCallOfTelecaller->created_at->format('M d, Y h:i A');
                } else {
                    $formatedLastCallOfTelecaller = '';
                }

                $telecallerActivity[] = [
                    'telecaller_name' => $eachTelecaller->first_name . ' ' . $eachTelecaller->last_name,
                    'last_call_of_telecaller' => $formatedLastCallOfTelecaller,
                    'total_call_count' => $total_call_count,
                    'total_call_count_idArr' => $total_call_count_idArr,
                    'parentStatus_new_count' => $parentStatus_new_count,
                    'parentStatus_new_count_idArr' => $parentStatus_new_count_idArr,
                    'parentStatus_dumb_count' => $parentStatus_dumb_count,
                    'parentStatus_dumb_count_idArr' => $parentStatus_dumb_count_idArr,
                    'parentStatus_followUp_count' => $parentStatus_followUp_count,
                    'parentStatus_followUp_count_idArr' => $parentStatus_followUp_count_idArr,
                    'parentStatus_success_count' => $parentStatus_success_count,
                    'parentStatus_success_count_idArr' => $parentStatus_success_count_idArr,
                    'tellecallerWisePendingCount' => $tellecallerWisePendingCount,
                ];

            }

            $branchWiseTelecallerActivity[] = [
                'branch_name' => $eachBranch->name,
                'telecallerActivity' => $telecallerActivity,
            ];
        }




        // dd($branchWiseTelecallerActivity);

        $title = 'Activity ' . $this->data['title'];
        $page_name = 'report.activity-report';
        $data = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
        return view('maincontents.' . $page_name, $data)->with(['branchWiseTelecallerActivity' => $branchWiseTelecallerActivity]);
    }


    public function activityReportNew(Request $request)
    {
        $data['module'] = $this->data;


        $query = LeadActivity::query();
        //filters
        if (!empty($request->input('call-from-date'))) {
            $callFromDate = $request->input('call-from-date');
            $data['callFromDate'] = $callFromDate;

            $query->whereDate('created_at', '>=', $callFromDate);
        }

        if (!empty($request->input('call-to-date'))) {
            $callToDate = $request->input('call-to-date');
            $data['callToDate'] = $callToDate;

            $query->whereDate('created_at', '<=', $callToDate);
        }

        $query->where('status', '!=', 3)->orderBy('id', 'desc');




        $allBranch = Branch::where('status', '!=', 3)->get();

        $branchWiseTelecallerActivity = [];
        foreach ($allBranch as $eachBranch) {
            if (session('user_data')['role_id'] == 3) {
                $this_telecaller_id = session('user_data')['user_id'];
                $branchWiseTelecaller = User::where('id', '=', $this_telecaller_id)->where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
            } else {
                $branchWiseTelecaller = User::where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
            }

            $telecallerActivity = [];
            foreach ($branchWiseTelecaller as $eachTelecaller) {

                $tellecallerWisePendingCount = BranchLead::where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('parent_status_id', '=', 0)->where('child_status_id', '=', 0)->where('status', '!=', 3)->count();

                
                if (!empty($request->input('unique')))
                {
                    $data['unique_check'] = 'checked';

                    // how many unique leads the telecaller called (koto jon k call koreche setar last activity)
                    $telecallerWiseLeads = (clone $query)
                        ->where('assigned_telecaller_id', $eachTelecaller->id)
                        ->whereIn('id', function ($sub) use ($eachTelecaller) {
                            $sub->selectRaw('MAX(id)')
                                ->from('lead_activities')
                                ->where('assigned_telecaller_id', $eachTelecaller->id)
                                ->groupBy('lead_sl_no');
                        })
                        ->get();
                } else {
                    // how many times the telecaller called (koto bar call koreche)
                    $telecallerWiseLeads = (clone $query)->where('assigned_telecaller_id', '=', $eachTelecaller->id)->get();
                }




                $parentStatus = LeadStatus::where('parent_id', '=', 0)->where('id', '!=', 12)->where('status', '=', 1)->orderBy('rank', 'desc')->get();
                // dd($parentStatus);
                $parentStatusSlugArr = [];
                foreach($parentStatus as $eachParentStatus)
                {
                    $parentStatusSlugArr[] = [
                       'id' => $eachParentStatus->id ,
                       'name' => $eachParentStatus->name ,
                       'background_color' => $eachParentStatus->background_color ,
                       'font_color' => $eachParentStatus->font_color ,

                       'background_color_modified' => Helper::adjustHexColor($eachParentStatus->background_color, +99),
                       'font_color_modified' => Helper::adjustHexColor($eachParentStatus->background_color, -99),

                       'slug' => strtolower(Helper::clean(strip_tags($eachParentStatus->name))) ,
                       'slug_count' => 0 ,
                       'slug_count_idArr' => [] ,
                       'rank' => $eachParentStatus->rank ,
                    ];
    
                }
                // dd($parentStatusSlugArr);


                $total_call_count = 0;
                $total_call_count_idArr = [];

                foreach ($telecallerWiseLeads as $eachLead) 
                {
                    $total_call_count++;
                    $total_call_count_idArr[] = Helper::encoded($eachLead->id);

                    foreach($parentStatusSlugArr as $key => $eachparentStatusSlugArr)
                    {
                        if ($eachLead->parent_status_id == $eachparentStatusSlugArr['id']) 
                        {
                            $parentStatusSlugArr[$key]['slug_count']++;        
                            $parentStatusSlugArr[$key]['slug_count_idArr'][] = Helper::encoded($eachLead->id);    
                        } 
                    }
                }
                // dd($parentStatusSlugArr);


                // last call of of telecaller
                $lastCallOfTelecaller = LeadActivity::where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->orderBy('id', 'desc')->first();
                if (!empty($lastCallOfTelecaller)) {
                    $formatedLastCallOfTelecaller = $lastCallOfTelecaller->created_at->format('M d, Y h:i A');
                } else {
                    $formatedLastCallOfTelecaller = '';
                }

                $telecallerActivity[] = [
                    'telecaller_name' => $eachTelecaller->first_name . ' ' . $eachTelecaller->last_name,
                    'last_call_of_telecaller' => $formatedLastCallOfTelecaller,
                    'total_call_count' => $total_call_count,
                    'total_call_count_idArr' => $total_call_count_idArr,
                    'parentStatusSlugArr' => $parentStatusSlugArr,
                    'tellecallerWisePendingCount' => $tellecallerWisePendingCount,
                ];

            }




            $parentStatus = LeadStatus::where('parent_id', '=', 0)->where('id', '!=', 12)->where('status', '=', 1)->orderBy('rank', 'desc')->get();
            $totalParentStatusSlugArr = [];
            foreach($parentStatus as $eachParentStatus)
            {
                $totalParentStatusSlugArr[] = [
                    'id' => $eachParentStatus->id ,
                    'name' => $eachParentStatus->name ,
                    'background_color' => $eachParentStatus->background_color ,
                    'font_color' => $eachParentStatus->font_color ,

                    'background_color_modified' => Helper::adjustHexColor($eachParentStatus->background_color, -80),
                    'font_color_modified' => Helper::adjustHexColor($eachParentStatus->font_color, +99),

                    'slug' => strtolower(Helper::clean(strip_tags($eachParentStatus->name))) ,
                    'SUM_slug_count' => 0 ,
                    'MERGE_slug_count_idArr' => [] ,
                    'rank' => $eachParentStatus->rank ,
                ];
            }
            // dd($totalParentStatusSlugArr);

            
            foreach($telecallerActivity as $key => $eachtelecallerActivity)
            {
                foreach($eachtelecallerActivity["parentStatusSlugArr"] as $keySuper => $eachTelecallerParentStatusSlugItem)
                {
                    // dd($eachTelecallerParentStatusSlugItem);
                    foreach($totalParentStatusSlugArr as $keySub => $eachtotalParentStatusSlugArr)
                    {
                        if($eachTelecallerParentStatusSlugItem["id"] == $eachtotalParentStatusSlugArr["id"])
                        {
                            if($keySuper == $keySub)
                            {
                                $totalParentStatusSlugArr[$keySub]['SUM_slug_count'] += $eachTelecallerParentStatusSlugItem['slug_count'];

                                $totalParentStatusSlugArr[$keySub]['MERGE_slug_count_idArr'] = array_merge(
                                    $totalParentStatusSlugArr[$keySub]['MERGE_slug_count_idArr'],
                                    $eachTelecallerParentStatusSlugItem['slug_count_idArr']
                                );
                            }
                        }
                    }
                }
            }
            // dd($totalParentStatusSlugArr);



            $branchWiseTelecallerActivity[] = [
                'branch_name' => $eachBranch->name,
                'telecallerActivity' => $telecallerActivity,
                'totalParentStatusSlugArr' => $totalParentStatusSlugArr,
            ];
        }




        // dd($branchWiseTelecallerActivity);

        $title = 'Activity ' . $this->data['title'];
        $page_name = 'report.activity-report-new';
        $data = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
        return view('maincontents.' . $page_name, $data)->with(['branchWiseTelecallerActivity' => $branchWiseTelecallerActivity]);
    }




    public function activityReportModalNew(Request $request)
    {
        if ($request->isMethod('post')) {
            // dd($request->leadActivityIdArr);
            $leadHistoryArr = [];
            if (!empty($request->leadActivityIdArr)) {
                foreach ($request->leadActivityIdArr as $leadActivityId) {
                    // dd($leadActivityId);
                    $leadActivity = LeadActivity::find(Helper::decoded($leadActivityId));
                    // dd($leadActivity);

                    $leadHistory = [];

                    $leadHistory['lead_sl_no'] = str_pad($leadActivity->lead_sl_no, 8, '0', STR_PAD_LEFT);

                    $leadHistory['contact-person-name'] = MasterLead::withTrashed()->where('sl_no', '=', $leadActivity->lead_sl_no)->where('header_id', '=', 2)->value('header_value') ?? '';
                    $leadHistory['phone'] = MasterLead::withTrashed()->where('sl_no', '=', $leadActivity->lead_sl_no)->where('header_id', '=', 4)->value('header_value') ?? '';

                    $leadHistory['branch_name'] = Branch::withTrashed()->where('id', '=', $leadActivity->branch_id)->value('name') ?? '';
                    $leadHistory['campaign_type_name'] = CampaignType::withTrashed()->where('id', '=', $leadActivity->campaign_type_id)->value('name') ?? '';
                    $leadHistory['campaign_name'] = Campaign::withTrashed()->where('id', '=', $leadActivity->campaign_id)->value('name') ?? '';

                    //fetching parent status
                    $parentStatus = [];
                    $parentStatus['name'] = LeadStatus::withTrashed()->where('id', '=', $leadActivity->parent_status_id)->where('parent_id', '=', 0)->value('name') ?? '';
                    $parentStatus['background_color'] = LeadStatus::withTrashed()->where('id', '=', $leadActivity->parent_status_id)->where('parent_id', '=', 0)->value('background_color') ?? '';
                    $parentStatus['font_color'] = LeadStatus::withTrashed()->where('id', '=', $leadActivity->parent_status_id)->where('parent_id', '=', 0)->value('font_color') ?? '';
                    $leadHistory['parentStatus'] = $parentStatus;

                    //fetching child status
                    $childStatus = [];
                    $childStatus['name'] = LeadStatus::withTrashed()->where('id', '=', $leadActivity->child_status_id)->where('parent_id', '=', $leadActivity->parent_status_id)->value('name') ?? '';
                    $childStatus['background_color'] = LeadStatus::withTrashed()->where('id', '=', $leadActivity->child_status_id)->where('parent_id', '=', $leadActivity->parent_status_id)->value('background_color') ?? '';
                    $childStatus['font_color'] = LeadStatus::withTrashed()->where('id', '=', $leadActivity->child_status_id)->where('parent_id', '=', $leadActivity->parent_status_id)->value('font_color') ?? '';
                    $leadHistory['childStatus'] = $childStatus;

                    $leadHistory['comment'] = $leadActivity->comment ?? '';

                    //fetching mood
                    $mood = [];
                    $mood['name'] = Mood::withTrashed()->where('id', '=', $leadActivity->mood)->value('name') ?? '';
                    $mood['emoji'] = Mood::withTrashed()->where('id', '=', $leadActivity->mood)->value('emoji') ?? '';
                    $mood['color'] = Mood::withTrashed()->where('id', '=', $leadActivity->mood)->value('color') ?? '';
                    $leadHistory['mood'] = $mood;

                    $leadHistory['purpose_name'] = Purpose::withTrashed()->where('id', '=', $leadActivity->purpose_id)->value('name') ?? '';

                    //fetch feedback tags
                    if (!empty($leadActivity->feedback_tag_ids)) {
                        $feedbackTagNameArr = [];
                        foreach (json_decode($leadActivity->feedback_tag_ids) as $feedbackTagId) {
                            $feedbackTagNameArr[] = FeedbackTag::withTrashed()->where('id', '=', $feedbackTagId)->value('name') ?? '';
                        }
                        $leadHistory['feedbackTagNameArr'] = $feedbackTagNameArr;
                    } else {
                        $leadHistory['feedbackTagNameArr'] = [];
                    }

                    $leadHistory['note'] = $leadActivity->note ?? '';

                    $leadHistory['next_followup_date'] = $leadActivity->next_followup_date ? Carbon::parse($leadActivity->next_followup_date)->format('M d, Y') : '';
                    $leadHistory['next_followup_time'] = $leadActivity->next_followup_time ? Carbon::createFromFormat('H:i:s', $leadActivity->next_followup_time)->format('h:i A') : '';
                    $leadHistory['last_call'] = $leadActivity->created_at ? $leadActivity->created_at->format('M d, Y h:i A') : '';

                    $leadHistory['assigned_telecaller_name'] = (User::withTrashed()->where('id', '=', $leadActivity->assigned_telecaller_id)->value('first_name') . ' ' . User::withTrashed()->where('id', '=', $leadActivity->assigned_telecaller_id)->value('last_name')) ?? '';


                    $leadHistoryArr[] = $leadHistory;


                }

            }


            $data = [];
            if (!empty($leadHistoryArr)) 
            {
                $data["branch_name"] = strip_tags($request->branchName);

                // dd($request->total);
                if ($request->total == "false") {
                    $data["assigned_telecaller_name"] = strip_tags($request->assignedTelecallerName);
                } elseif ($request->total == "true") {
                    $data["assigned_telecaller_name"] = '';
                }

            }

            $page_name = 'report.activity-report-modal';
            $html = view('maincontents.' . $page_name, $data)->with(["leadHistoryArr" => $leadHistoryArr,])->render();

            return response()->json([
                'html' => $html
            ]);

            // return response()->json(['message' => 'Modal loaded successfully.']);
        }

    }


    public function activityReportModal(Request $request)
    {
        if ($request->isMethod('post')) {
            // dd($request->leadActivityIdArr);
            $leadHistoryArr = [];
            if (!empty($request->leadActivityIdArr)) {
                foreach ($request->leadActivityIdArr as $leadActivityId) {
                    // dd($leadActivityId);
                    $leadActivity = LeadActivity::find($leadActivityId);
                    // dd($leadActivity);

                    $leadHistory = [];

                    $leadHistory['lead_sl_no'] = str_pad($leadActivity->lead_sl_no, 8, '0', STR_PAD_LEFT);

                    $leadHistory['contact-person-name'] = MasterLead::withTrashed()->where('sl_no', '=', $leadActivity->lead_sl_no)->where('header_id', '=', 2)->value('header_value') ?? '';
                    $leadHistory['phone'] = MasterLead::withTrashed()->where('sl_no', '=', $leadActivity->lead_sl_no)->where('header_id', '=', 4)->value('header_value') ?? '';

                    $leadHistory['branch_name'] = Branch::withTrashed()->where('id', '=', $leadActivity->branch_id)->value('name') ?? '';
                    $leadHistory['campaign_type_name'] = CampaignType::withTrashed()->where('id', '=', $leadActivity->campaign_type_id)->value('name') ?? '';
                    $leadHistory['campaign_name'] = Campaign::withTrashed()->where('id', '=', $leadActivity->campaign_id)->value('name') ?? '';

                    //fetching parent status
                    $parentStatus = [];
                    $parentStatus['name'] = LeadStatus::withTrashed()->where('id', '=', $leadActivity->parent_status_id)->where('parent_id', '=', 0)->value('name') ?? '';
                    $parentStatus['background_color'] = LeadStatus::withTrashed()->where('id', '=', $leadActivity->parent_status_id)->where('parent_id', '=', 0)->value('background_color') ?? '';
                    $parentStatus['font_color'] = LeadStatus::withTrashed()->where('id', '=', $leadActivity->parent_status_id)->where('parent_id', '=', 0)->value('font_color') ?? '';
                    $leadHistory['parentStatus'] = $parentStatus;

                    //fetching child status
                    $childStatus = [];
                    $childStatus['name'] = LeadStatus::withTrashed()->where('id', '=', $leadActivity->child_status_id)->where('parent_id', '=', $leadActivity->parent_status_id)->value('name') ?? '';
                    $childStatus['background_color'] = LeadStatus::withTrashed()->where('id', '=', $leadActivity->child_status_id)->where('parent_id', '=', $leadActivity->parent_status_id)->value('background_color') ?? '';
                    $childStatus['font_color'] = LeadStatus::withTrashed()->where('id', '=', $leadActivity->child_status_id)->where('parent_id', '=', $leadActivity->parent_status_id)->value('font_color') ?? '';
                    $leadHistory['childStatus'] = $childStatus;

                    $leadHistory['comment'] = $leadActivity->comment ?? '';

                    //fetching mood
                    $mood = [];
                    $mood['name'] = Mood::withTrashed()->where('id', '=', $leadActivity->mood)->value('name') ?? '';
                    $mood['emoji'] = Mood::withTrashed()->where('id', '=', $leadActivity->mood)->value('emoji') ?? '';
                    $mood['color'] = Mood::withTrashed()->where('id', '=', $leadActivity->mood)->value('color') ?? '';
                    $leadHistory['mood'] = $mood;

                    $leadHistory['purpose_name'] = Purpose::withTrashed()->where('id', '=', $leadActivity->purpose_id)->value('name') ?? '';

                    //fetch feedback tags
                    if (!empty($leadActivity->feedback_tag_ids)) {
                        $feedbackTagNameArr = [];
                        foreach (json_decode($leadActivity->feedback_tag_ids) as $feedbackTagId) {
                            $feedbackTagNameArr[] = FeedbackTag::withTrashed()->where('id', '=', $feedbackTagId)->value('name') ?? '';
                        }
                        $leadHistory['feedbackTagNameArr'] = $feedbackTagNameArr;
                    } else {
                        $leadHistory['feedbackTagNameArr'] = [];
                    }

                    $leadHistory['note'] = $leadActivity->note ?? '';

                    $leadHistory['next_followup_date'] = $leadActivity->next_followup_date ? Carbon::parse($leadActivity->next_followup_date)->format('M d, Y') : '';
                    $leadHistory['next_followup_time'] = $leadActivity->next_followup_time ? Carbon::createFromFormat('H:i:s', $leadActivity->next_followup_time)->format('h:i A') : '';
                    $leadHistory['last_call'] = $leadActivity->created_at ? $leadActivity->created_at->format('M d, Y h:i A') : '';

                    $leadHistory['assigned_telecaller_name'] = (User::withTrashed()->where('id', '=', $leadActivity->assigned_telecaller_id)->value('first_name') . ' ' . User::withTrashed()->where('id', '=', $leadActivity->assigned_telecaller_id)->value('last_name')) ?? '';


                    $leadHistoryArr[] = $leadHistory;


                }

            }


            $data = [];
            if (!empty($leadHistoryArr)) 
            {
                $data["branch_name"] = strip_tags($request->branchName);

                // dd($request->total);
                if ($request->total == "false") {
                    $data["assigned_telecaller_name"] = strip_tags($request->assignedTelecallerName);
                } elseif ($request->total == "true") {
                    $data["assigned_telecaller_name"] = '';
                }

            }

            $page_name = 'report.activity-report-modal';
            $html = view('maincontents.' . $page_name, $data)->with(["leadHistoryArr" => $leadHistoryArr,])->render();

            return response()->json([
                'html' => $html
            ]);

            // return response()->json(['message' => 'Modal loaded successfully.']);
        }

    }



    public function assignReport(Request $request)
    {
        $data['module'] = $this->data;

        if (!empty($request->input('assigned-from-date')) && !empty($request->input('assigned-to-date'))) {
            $assignedToDate = $request->input('assigned-to-date');
            $data['assignedToDate'] = $assignedToDate;

            $assignedFromDate = $request->input('assigned-from-date');
            $data['assignedFromDate'] = $assignedFromDate;

            $allBranch = Branch::where('status', '!=', 3)->get();

            $branchWiseTelecallerActivity = [];
            foreach ($allBranch as $eachBranch) {
                if (session('user_data')['role_id'] == 3) {
                    $this_telecaller_id = session('user_data')['user_id'];
                    $branchWiseTelecaller = User::where('id', '=', $this_telecaller_id)->where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                } else {
                    $branchWiseTelecaller = User::where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                }

                $telecallerActivity = [];
                foreach ($branchWiseTelecaller as $eachTelecaller) {
                    $telecallerWiseLeads = BranchLead::whereDate('created_at', '>=', $assignedFromDate)->whereDate('created_at', '<=', $assignedToDate)->where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->get();

                    $parentStatus_new_count = 0;
                    $parentStatus_dumb_count = 0;
                    $parentStatus_followUp_count = 0;
                    $parentStatus_success_count = 0;
                    $total_call_count = 0;
                    foreach ($telecallerWiseLeads as $eachLead) {
                        $total_call_count++;

                        if ($eachLead->parent_status_id == 0 || $eachLead->parent_status_id == 12) {
                            $parentStatus_new_count++;
                        } elseif ($eachLead->parent_status_id == 1) {
                            $parentStatus_dumb_count++;
                        } elseif ($eachLead->parent_status_id == 5) {
                            $parentStatus_followUp_count++;
                        } elseif ($eachLead->parent_status_id == 10) {
                            $parentStatus_success_count++;
                        }
                    }

                    // last call of of telecaller
                    $lastCallOfTelecaller = LeadActivity::where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->orderBy('id', 'desc')->first();
                    if (!empty($lastCallOfTelecaller)) {
                        $formatedLastCallOfTelecaller = $lastCallOfTelecaller->created_at->format('M d, Y h:i A');
                    } else {
                        $formatedLastCallOfTelecaller = '';
                    }

                    $telecallerActivity[] = [
                        'telecaller_name' => $eachTelecaller->first_name . ' ' . $eachTelecaller->last_name,
                        'last_call_of_telecaller' => $formatedLastCallOfTelecaller,
                        'total_call_count' => $total_call_count,
                        'parentStatus_new_count' => $parentStatus_new_count,
                        'parentStatus_dumb_count' => $parentStatus_dumb_count,
                        'parentStatus_followUp_count' => $parentStatus_followUp_count,
                        'parentStatus_success_count' => $parentStatus_success_count,
                    ];

                }

                $branchWiseTelecallerActivity[] = [
                    'branch_name' => $eachBranch->name,
                    'telecallerActivity' => $telecallerActivity,
                ];
            }

        } elseif (!empty($request->input('assigned-to-date'))) {
            $assignedToDate = $request->input('assigned-to-date');
            $data['assignedToDate'] = $assignedToDate;

            $allBranch = Branch::where('status', '!=', 3)->get();

            $branchWiseTelecallerActivity = [];
            foreach ($allBranch as $eachBranch) {
                if (session('user_data')['role_id'] == 3) {
                    $this_telecaller_id = session('user_data')['user_id'];
                    $branchWiseTelecaller = User::where('id', '=', $this_telecaller_id)->where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                } else {
                    $branchWiseTelecaller = User::where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                }

                $telecallerActivity = [];
                foreach ($branchWiseTelecaller as $eachTelecaller) {
                    $telecallerWiseLeads = BranchLead::whereDate('created_at', '<=', $assignedToDate)->where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->get();

                    $parentStatus_new_count = 0;
                    $parentStatus_dumb_count = 0;
                    $parentStatus_followUp_count = 0;
                    $parentStatus_success_count = 0;
                    $total_call_count = 0;
                    foreach ($telecallerWiseLeads as $eachLead) {
                        $total_call_count++;

                        if ($eachLead->parent_status_id == 0 || $eachLead->parent_status_id == 12) {
                            $parentStatus_new_count++;
                        } elseif ($eachLead->parent_status_id == 1) {
                            $parentStatus_dumb_count++;
                        } elseif ($eachLead->parent_status_id == 5) {
                            $parentStatus_followUp_count++;
                        } elseif ($eachLead->parent_status_id == 10) {
                            $parentStatus_success_count++;
                        }
                    }

                    // last call of of telecaller
                    $lastCallOfTelecaller = LeadActivity::where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->orderBy('id', 'desc')->first();
                    if (!empty($lastCallOfTelecaller)) {
                        $formatedLastCallOfTelecaller = $lastCallOfTelecaller->created_at->format('M d, Y h:i A');
                    } else {
                        $formatedLastCallOfTelecaller = '';
                    }

                    $telecallerActivity[] = [
                        'telecaller_name' => $eachTelecaller->first_name . ' ' . $eachTelecaller->last_name,
                        'last_call_of_telecaller' => $formatedLastCallOfTelecaller,
                        'total_call_count' => $total_call_count,
                        'parentStatus_new_count' => $parentStatus_new_count,
                        'parentStatus_dumb_count' => $parentStatus_dumb_count,
                        'parentStatus_followUp_count' => $parentStatus_followUp_count,
                        'parentStatus_success_count' => $parentStatus_success_count,
                    ];

                }

                $branchWiseTelecallerActivity[] = [
                    'branch_name' => $eachBranch->name,
                    'telecallerActivity' => $telecallerActivity,
                ];
            }

        } elseif (!empty($request->input('assigned-from-date'))) {
            $assignedFromDate = $request->input('assigned-from-date');
            $data['assignedFromDate'] = $assignedFromDate;
            // dd($assignedFromDate);

            $allBranch = Branch::where('status', '!=', 3)->get();

            $branchWiseTelecallerActivity = [];
            foreach ($allBranch as $eachBranch) {
                if (session('user_data')['role_id'] == 3) {
                    $this_telecaller_id = session('user_data')['user_id'];
                    $branchWiseTelecaller = User::where('id', '=', $this_telecaller_id)->where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                } else {
                    $branchWiseTelecaller = User::where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                }

                $telecallerActivity = [];
                foreach ($branchWiseTelecaller as $eachTelecaller) {
                    $telecallerWiseLeads = BranchLead::whereDate('created_at', '>=', $assignedFromDate)->where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->get();

                    $parentStatus_new_count = 0;
                    $parentStatus_dumb_count = 0;
                    $parentStatus_followUp_count = 0;
                    $parentStatus_success_count = 0;
                    $total_call_count = 0;
                    foreach ($telecallerWiseLeads as $eachLead) {
                        $total_call_count++;

                        if ($eachLead->parent_status_id == 0 || $eachLead->parent_status_id == 12) {
                            $parentStatus_new_count++;
                        } elseif ($eachLead->parent_status_id == 1) {
                            $parentStatus_dumb_count++;
                        } elseif ($eachLead->parent_status_id == 5) {
                            $parentStatus_followUp_count++;
                        } elseif ($eachLead->parent_status_id == 10) {
                            $parentStatus_success_count++;
                        }
                    }

                    // last call of of telecaller
                    $lastCallOfTelecaller = LeadActivity::where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->orderBy('id', 'desc')->first();
                    if (!empty($lastCallOfTelecaller)) {
                        $formatedLastCallOfTelecaller = $lastCallOfTelecaller->created_at->format('M d, Y h:i A');
                    } else {
                        $formatedLastCallOfTelecaller = '';
                    }

                    $telecallerActivity[] = [
                        'telecaller_name' => $eachTelecaller->first_name . ' ' . $eachTelecaller->last_name,
                        'last_call_of_telecaller' => $formatedLastCallOfTelecaller,
                        'total_call_count' => $total_call_count,
                        'parentStatus_new_count' => $parentStatus_new_count,
                        'parentStatus_dumb_count' => $parentStatus_dumb_count,
                        'parentStatus_followUp_count' => $parentStatus_followUp_count,
                        'parentStatus_success_count' => $parentStatus_success_count,
                    ];

                }

                $branchWiseTelecallerActivity[] = [
                    'branch_name' => $eachBranch->name,
                    'telecallerActivity' => $telecallerActivity,
                ];
            }

        } else // no filter
        {

            $allBranch = Branch::where('status', '!=', 3)->get();

            $branchWiseTelecallerActivity = [];
            foreach ($allBranch as $eachBranch) {
                if (session('user_data')['role_id'] == 3) {
                    $this_telecaller_id = session('user_data')['user_id'];
                    $branchWiseTelecaller = User::where('id', '=', $this_telecaller_id)->where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                } else {
                    $branchWiseTelecaller = User::where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                }

                $telecallerActivity = [];
                foreach ($branchWiseTelecaller as $eachTelecaller) {
                    $telecallerWiseLeads = BranchLead::where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->get();

                    $parentStatus_new_count = 0;
                    $parentStatus_dumb_count = 0;
                    $parentStatus_followUp_count = 0;
                    $parentStatus_success_count = 0;
                    $total_call_count = 0;
                    foreach ($telecallerWiseLeads as $eachLead) {
                        $total_call_count++;

                        if ($eachLead->parent_status_id == 0 || $eachLead->parent_status_id == 12) {
                            $parentStatus_new_count++;
                        } elseif ($eachLead->parent_status_id == 1) {
                            $parentStatus_dumb_count++;
                        } elseif ($eachLead->parent_status_id == 5) {
                            $parentStatus_followUp_count++;
                        } elseif ($eachLead->parent_status_id == 10) {
                            $parentStatus_success_count++;
                        }
                    }

                    // last call of of telecaller
                    $lastCallOfTelecaller = LeadActivity::where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->orderBy('id', 'desc')->first();
                    if (!empty($lastCallOfTelecaller)) {
                        $formatedLastCallOfTelecaller = $lastCallOfTelecaller->created_at->format('M d, Y h:i A');
                    } else {
                        $formatedLastCallOfTelecaller = '';
                    }

                    $telecallerActivity[] = [
                        'telecaller_name' => $eachTelecaller->first_name . ' ' . $eachTelecaller->last_name,
                        'last_call_of_telecaller' => $formatedLastCallOfTelecaller,
                        'total_call_count' => $total_call_count,
                        'parentStatus_new_count' => $parentStatus_new_count,
                        'parentStatus_dumb_count' => $parentStatus_dumb_count,
                        'parentStatus_followUp_count' => $parentStatus_followUp_count,
                        'parentStatus_success_count' => $parentStatus_success_count,
                    ];

                }

                $branchWiseTelecallerActivity[] = [
                    'branch_name' => $eachBranch->name,
                    'telecallerActivity' => $telecallerActivity,
                ];
            }

        }

        $title = 'Assign ' . $this->data['title'];
        $page_name = 'report.assign-report';
        $data = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
        return view('maincontents.' . $page_name, $data)->with(['branchWiseTelecallerActivity' => $branchWiseTelecallerActivity]);
    }






}
