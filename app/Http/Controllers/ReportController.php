<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\SiteAuthService;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\Branch;
use App\Models\LeadActivity;
use App\Models\GeneralSetting;
use App\Models\UserActivity;
use App\Models\BranchLead;

class ReportController extends Controller
{
    protected $siteAuthService;
    protected $data;
    public function __construct()
    {
        $this->siteAuthService = new SiteAuthService();
        $this->data = array(
            'title'             => 'Report',
            'controller'        => 'ReportController',
            'controller_route'  => 'report',
            'primary_key'       => 'id',
        );
    }

    public function activityReport(Request $request)
    {
        $data['module']                 = $this->data;


        $query = LeadActivity::query(); 
        //filters
        if(!empty($request->input('call-from-date')))
        {
            $callFromDate = $request->input('call-from-date');
            $data['callFromDate'] = $callFromDate ;

            $query->whereDate('created_at', '>=', $callFromDate);
        }

        if(!empty($request->input('call-to-date')))
        {
            $callToDate = $request->input('call-to-date');
            $data['callToDate'] = $callToDate ;

            $query->whereDate('created_at', '<=', $callToDate);
        }

        $query->where('status', '!=', 3);

        
        

        $allBranch = Branch::where('status', '!=', 3)->get();

        $branchWiseTelecallerActivity = [];
        foreach($allBranch as $eachBranch)
        {
            if(session('user_data')['role_id'] == 3)
            {
                $this_telecaller_id = session('user_data')['user_id'] ;
                $branchWiseTelecaller = User::where('id', '=', $this_telecaller_id)->where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
            }
            else
            {                     
                $branchWiseTelecaller = User::where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
            }

            $telecallerActivity = [];
            foreach($branchWiseTelecaller as $eachTelecaller)
            {
                // how many times the telecaller called (koto bar call koreche)
                $telecallerWiseLeads = (clone $query)->where('assigned_telecaller_id', '=', $eachTelecaller->id)->get();


                $parentStatus_new_count = 0;
                $parentStatus_dumb_count = 0;
                $parentStatus_followUp_count = 0;
                $parentStatus_success_count = 0;
                $total_call_count = 0;
                foreach($telecallerWiseLeads as $eachLead)
                {
                    $total_call_count++;

                    if($eachLead->parent_status_id == 0 || $eachLead->parent_status_id == 12)
                    {
                        $parentStatus_new_count++;
                    }
                    elseif($eachLead->parent_status_id == 1)
                    {
                        $parentStatus_dumb_count++;
                    }
                    elseif($eachLead->parent_status_id == 5)
                    {
                        $parentStatus_followUp_count++;
                    }
                    elseif($eachLead->parent_status_id == 10)
                    {
                        $parentStatus_success_count++;
                    }
                }
                
                // last call of of telecaller
                $lastCallOfTelecaller = LeadActivity::where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->orderBy('id', 'desc')->first();
                if(!empty($lastCallOfTelecaller))
                {
                    $formatedLastCallOfTelecaller = $lastCallOfTelecaller->created_at->format('M d, Y h:i A') ;
                }
                else
                {
                    $formatedLastCallOfTelecaller = '' ;
                }

                $telecallerActivity[] = [
                    'telecaller_name' => $eachTelecaller->first_name . ' ' . $eachTelecaller->last_name ,
                    'last_call_of_telecaller' => $formatedLastCallOfTelecaller ,
                    'total_call_count' => $total_call_count ,
                    'parentStatus_new_count' => $parentStatus_new_count ,
                    'parentStatus_dumb_count' => $parentStatus_dumb_count ,
                    'parentStatus_followUp_count' => $parentStatus_followUp_count ,
                    'parentStatus_success_count' => $parentStatus_success_count ,
                ];

            }

            $branchWiseTelecallerActivity[] = [
                'branch_name' => $eachBranch->name ,
                'telecallerActivity' => $telecallerActivity ,
            ];
        }
        
        


        // dd($branchWiseTelecallerActivity);

        $title                          = 'Activity ' . $this->data['title'];
        $page_name                      = 'report.activity-report';
        $data                           = $this->siteAuthService ->admin_after_login_layout($title,$page_name,$data);
        return view('maincontents.' . $page_name, $data)->with(['branchWiseTelecallerActivity' => $branchWiseTelecallerActivity ]);
    }

    public function assignReport(Request $request)
    {
        $data['module'] = $this->data;
        
        if(!empty($request->input('assigned-from-date')) && !empty($request->input('assigned-to-date')))
        {
            $assignedToDate = $request->input('assigned-to-date');
            $data['assignedToDate'] = $assignedToDate ;

            $assignedFromDate = $request->input('assigned-from-date');
            $data['assignedFromDate'] = $assignedFromDate ;
            
            $allBranch = Branch::where('status', '!=', 3)->get();

            $branchWiseTelecallerActivity = [];
            foreach($allBranch as $eachBranch)
            {
                if(session('user_data')['role_id'] == 3)
                {
                    $this_telecaller_id = session('user_data')['user_id'] ;
                    $branchWiseTelecaller = User::where('id', '=', $this_telecaller_id)->where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                }
                else
                {                     
                    $branchWiseTelecaller = User::where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                }

                $telecallerActivity = [];
                foreach($branchWiseTelecaller as $eachTelecaller)
                {
                    $telecallerWiseLeads = BranchLead::whereDate('created_at', '>=', $assignedFromDate)->whereDate('created_at', '<=', $assignedToDate)->where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->get();

                    $parentStatus_new_count = 0;
                    $parentStatus_dumb_count = 0;
                    $parentStatus_followUp_count = 0;
                    $parentStatus_success_count = 0;
                    $total_call_count = 0;
                    foreach($telecallerWiseLeads as $eachLead)
                    {
                        $total_call_count++;

                        if($eachLead->parent_status_id == 0 || $eachLead->parent_status_id == 12)
                        {
                            $parentStatus_new_count++;
                        }
                        elseif($eachLead->parent_status_id == 1)
                        {
                            $parentStatus_dumb_count++;
                        }
                        elseif($eachLead->parent_status_id == 5)
                        {
                            $parentStatus_followUp_count++;
                        }
                        elseif($eachLead->parent_status_id == 10)
                        {
                            $parentStatus_success_count++;
                        }
                    }
                    
                    // last call of of telecaller
                    $lastCallOfTelecaller = LeadActivity::where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->orderBy('id', 'desc')->first();
                    if(!empty($lastCallOfTelecaller))
                    {
                        $formatedLastCallOfTelecaller = $lastCallOfTelecaller->created_at->format('M d, Y h:i A') ;
                    }
                    else
                    {
                        $formatedLastCallOfTelecaller = '' ;
                    }

                    $telecallerActivity[] = [
                        'telecaller_name' => $eachTelecaller->first_name . ' ' . $eachTelecaller->last_name ,
                        'last_call_of_telecaller' => $formatedLastCallOfTelecaller ,
                        'total_call_count' => $total_call_count ,
                        'parentStatus_new_count' => $parentStatus_new_count ,
                        'parentStatus_dumb_count' => $parentStatus_dumb_count ,
                        'parentStatus_followUp_count' => $parentStatus_followUp_count ,
                        'parentStatus_success_count' => $parentStatus_success_count ,
                    ];

                }

                $branchWiseTelecallerActivity[] = [
                    'branch_name' => $eachBranch->name ,
                    'telecallerActivity' => $telecallerActivity ,
                ];
            }
        
        }
        elseif(!empty($request->input('assigned-to-date')))
        {
            $assignedToDate = $request->input('assigned-to-date');
            $data['assignedToDate'] = $assignedToDate ;
            
            $allBranch = Branch::where('status', '!=', 3)->get();

            $branchWiseTelecallerActivity = [];
            foreach($allBranch as $eachBranch)
            {
                if(session('user_data')['role_id'] == 3)
                {
                    $this_telecaller_id = session('user_data')['user_id'] ;
                    $branchWiseTelecaller = User::where('id', '=', $this_telecaller_id)->where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                }
                else
                {                     
                    $branchWiseTelecaller = User::where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                }

                $telecallerActivity = [];
                foreach($branchWiseTelecaller as $eachTelecaller)
                {
                    $telecallerWiseLeads = BranchLead::whereDate('created_at', '<=', $assignedToDate)->where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->get();

                    $parentStatus_new_count = 0;
                    $parentStatus_dumb_count = 0;
                    $parentStatus_followUp_count = 0;
                    $parentStatus_success_count = 0;
                    $total_call_count = 0;
                    foreach($telecallerWiseLeads as $eachLead)
                    {
                        $total_call_count++;

                        if($eachLead->parent_status_id == 0 || $eachLead->parent_status_id == 12)
                        {
                            $parentStatus_new_count++;
                        }
                        elseif($eachLead->parent_status_id == 1)
                        {
                            $parentStatus_dumb_count++;
                        }
                        elseif($eachLead->parent_status_id == 5)
                        {
                            $parentStatus_followUp_count++;
                        }
                        elseif($eachLead->parent_status_id == 10)
                        {
                            $parentStatus_success_count++;
                        }
                    }
                    
                    // last call of of telecaller
                    $lastCallOfTelecaller = LeadActivity::where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->orderBy('id', 'desc')->first();
                    if(!empty($lastCallOfTelecaller))
                    {
                        $formatedLastCallOfTelecaller = $lastCallOfTelecaller->created_at->format('M d, Y h:i A') ;
                    }
                    else
                    {
                        $formatedLastCallOfTelecaller = '' ;
                    }

                    $telecallerActivity[] = [
                        'telecaller_name' => $eachTelecaller->first_name . ' ' . $eachTelecaller->last_name ,
                        'last_call_of_telecaller' => $formatedLastCallOfTelecaller ,
                        'total_call_count' => $total_call_count ,
                        'parentStatus_new_count' => $parentStatus_new_count ,
                        'parentStatus_dumb_count' => $parentStatus_dumb_count ,
                        'parentStatus_followUp_count' => $parentStatus_followUp_count ,
                        'parentStatus_success_count' => $parentStatus_success_count ,
                    ];

                }

                $branchWiseTelecallerActivity[] = [
                    'branch_name' => $eachBranch->name ,
                    'telecallerActivity' => $telecallerActivity ,
                ];
            }
        
        }
        elseif(!empty($request->input('assigned-from-date')))
        {
            $assignedFromDate = $request->input('assigned-from-date');
            $data['assignedFromDate'] = $assignedFromDate ;
            // dd($assignedFromDate);
                            
            $allBranch = Branch::where('status', '!=', 3)->get();

            $branchWiseTelecallerActivity = [];
            foreach($allBranch as $eachBranch)
            {
                if(session('user_data')['role_id'] == 3)
                {
                    $this_telecaller_id = session('user_data')['user_id'] ;
                    $branchWiseTelecaller = User::where('id', '=', $this_telecaller_id)->where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                }
                else
                {                     
                    $branchWiseTelecaller = User::where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                }

                $telecallerActivity = [];
                foreach($branchWiseTelecaller as $eachTelecaller)
                {
                    $telecallerWiseLeads = BranchLead::whereDate('created_at', '>=', $assignedFromDate)->where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->get();

                    $parentStatus_new_count = 0;
                    $parentStatus_dumb_count = 0;
                    $parentStatus_followUp_count = 0;
                    $parentStatus_success_count = 0;
                    $total_call_count = 0;
                    foreach($telecallerWiseLeads as $eachLead)
                    {
                        $total_call_count++;

                        if($eachLead->parent_status_id == 0 || $eachLead->parent_status_id == 12)
                        {
                            $parentStatus_new_count++;
                        }
                        elseif($eachLead->parent_status_id == 1)
                        {
                            $parentStatus_dumb_count++;
                        }
                        elseif($eachLead->parent_status_id == 5)
                        {
                            $parentStatus_followUp_count++;
                        }
                        elseif($eachLead->parent_status_id == 10)
                        {
                            $parentStatus_success_count++;
                        }
                    }
                    
                    // last call of of telecaller
                    $lastCallOfTelecaller = LeadActivity::where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->orderBy('id', 'desc')->first();
                    if(!empty($lastCallOfTelecaller))
                    {
                        $formatedLastCallOfTelecaller = $lastCallOfTelecaller->created_at->format('M d, Y h:i A') ;
                    }
                    else
                    {
                        $formatedLastCallOfTelecaller = '' ;
                    }

                    $telecallerActivity[] = [
                        'telecaller_name' => $eachTelecaller->first_name . ' ' . $eachTelecaller->last_name ,
                        'last_call_of_telecaller' => $formatedLastCallOfTelecaller ,
                        'total_call_count' => $total_call_count ,
                        'parentStatus_new_count' => $parentStatus_new_count ,
                        'parentStatus_dumb_count' => $parentStatus_dumb_count ,
                        'parentStatus_followUp_count' => $parentStatus_followUp_count ,
                        'parentStatus_success_count' => $parentStatus_success_count ,
                    ];

                }

                $branchWiseTelecallerActivity[] = [
                    'branch_name' => $eachBranch->name ,
                    'telecallerActivity' => $telecallerActivity ,
                ];
            }
                    
        }
        else // no filter
        {

            $allBranch = Branch::where('status', '!=', 3)->get();

            $branchWiseTelecallerActivity = [];
            foreach($allBranch as $eachBranch)
            {
                if(session('user_data')['role_id'] == 3)
                {
                    $this_telecaller_id = session('user_data')['user_id'] ;
                    $branchWiseTelecaller = User::where('id', '=', $this_telecaller_id)->where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                }
                else
                {                     
                    $branchWiseTelecaller = User::where('branch_id', '=', $eachBranch->id)->where('role_id', '=', 3)->where('status', '!=', 3)->get();
                }

                $telecallerActivity = [];
                foreach($branchWiseTelecaller as $eachTelecaller)
                {
                    $telecallerWiseLeads = BranchLead::where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->get();

                    $parentStatus_new_count = 0;
                    $parentStatus_dumb_count = 0;
                    $parentStatus_followUp_count = 0;
                    $parentStatus_success_count = 0;
                    $total_call_count = 0;
                    foreach($telecallerWiseLeads as $eachLead)
                    {
                        $total_call_count++;

                        if($eachLead->parent_status_id == 0 || $eachLead->parent_status_id == 12)
                        {
                            $parentStatus_new_count++;
                        }
                        elseif($eachLead->parent_status_id == 1)
                        {
                            $parentStatus_dumb_count++;
                        }
                        elseif($eachLead->parent_status_id == 5)
                        {
                            $parentStatus_followUp_count++;
                        }
                        elseif($eachLead->parent_status_id == 10)
                        {
                            $parentStatus_success_count++;
                        }
                    }
                    
                    // last call of of telecaller
                    $lastCallOfTelecaller = LeadActivity::where('assigned_telecaller_id', '=', $eachTelecaller->id)->where('status', '!=', 3)->orderBy('id', 'desc')->first();
                    if(!empty($lastCallOfTelecaller))
                    {
                        $formatedLastCallOfTelecaller = $lastCallOfTelecaller->created_at->format('M d, Y h:i A') ;
                    }
                    else
                    {
                        $formatedLastCallOfTelecaller = '' ;
                    }

                    $telecallerActivity[] = [
                        'telecaller_name' => $eachTelecaller->first_name . ' ' . $eachTelecaller->last_name ,
                        'last_call_of_telecaller' => $formatedLastCallOfTelecaller ,
                        'total_call_count' => $total_call_count ,
                        'parentStatus_new_count' => $parentStatus_new_count ,
                        'parentStatus_dumb_count' => $parentStatus_dumb_count ,
                        'parentStatus_followUp_count' => $parentStatus_followUp_count ,
                        'parentStatus_success_count' => $parentStatus_success_count ,
                    ];

                }

                $branchWiseTelecallerActivity[] = [
                    'branch_name' => $eachBranch->name ,
                    'telecallerActivity' => $telecallerActivity ,
                ];
            }
        
        }

        $title                                  = 'Assign ' . $this->data['title'];
        $page_name                              = 'report.assign-report';
        $data = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
        return view('maincontents.' . $page_name, $data)->with(['branchWiseTelecallerActivity' => $branchWiseTelecallerActivity ]);
    }
}
