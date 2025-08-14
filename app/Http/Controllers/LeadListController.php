<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\Branch;
use App\Models\UploadLead;
use App\Models\UserActivity;
use App\Models\User;
use App\Models\LeadHeader;
use App\Models\CampaignType;
use App\Models\Campaign;
use App\Models\MasterLead;
use App\Models\BranchLead;
use App\Models\LeadActivity;
use App\Models\LeadStatus;
use App\Models\Country;
use App\Models\State;
use App\Models\Source;
use App\Models\Purpose;
use App\Models\Mood;
use App\Models\FeedbackTag;
use App\Services\SiteAuthService;
use App\Helpers\Helper;

use Auth;
use Session;
use Hash;
use DB;

class LeadListController extends Controller
{
    protected $siteAuthService;
    protected $data;
    public function __construct()
    {
        $this->siteAuthService = new SiteAuthService();
        $this->data = array(
            'title'             => 'Lead',
            'controller'        => 'LeadListController',
            'controller_route'  => 'lead-list',
            'primary_key'       => 'id',
        );
    }

    public function list(Request $request)
    {
        $data['module'] = $this->data;

        $perPage = $request->input('perPage', 10); // default is 10
        $page = $request->input('page', 1); // default is 1

        if(session('user_data')['role_id'] == 3)
        {
            $this_telecaller_id = session('user_data')['user_id'] ;
            $branchleadPaginated = BranchLead::where('assigned_telecaller_id', '=', $this_telecaller_id)->where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        else
        {
            $branchleadPaginated = BranchLead::where('status', '!=', 3)->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        }
        // dd($branchleadPaginated);

        $totalLeadArr = [];
        foreach($branchleadPaginated as $branchlead)
        {
            // $branchlead['lead_title'] = UploadLead::where('id', '=', $branchlead->upload_id)->where('status', '!=', 3)->value('title') ?? '';
            // $branchlead['filename'] = UploadLead::where('id', '=', $branchlead->upload_id)->where('status', '!=', 3)->value('filename') ?? '';
            // $branchlead['campaign_type_name'] = CampaignType::where('id', '=', $branchlead->campaign_type_id)->where('status', '!=', 3)->value('name') ?? '';
            // $branchlead['campaign_name'] = Campaign::where('id', '=', $branchlead->campaign_id)->where('status', '!=', 3)->value('name') ?? '';
            $branchlead['branch_name'] = Branch::where('id', '=', $branchlead->branch_id)->where('status', '!=', 3)->value('name') ?? '';
            $branchlead['assigned_telecaller_name'] = ( User::where('id', '=', $branchlead->assigned_telecaller_id)->where('status', '!=', 3)->value('first_name') . ' '. User::where('id', '=', $branchlead->assigned_telecaller_id)->where('status', '!=', 3)->value('last_name') ) ?? '';
            $branchlead['added_by_name'] = ( User::where('id', '=', $branchlead->created_by)->where('status', '!=', 3)->value('first_name') . ' '. User::where('id', '=', $branchlead->created_by)->where('status', '!=', 3)->value('last_name') ) ?? '';
            $branchlead['updated_by_name'] = ( User::where('id', '=', $branchlead->updated_by)->where('status', '!=', 3)->value('first_name') . ' '. User::where('id', '=', $branchlead->updated_by)->where('status', '!=', 3)->value('last_name') ) ?? '';

            //gathering each BranchLead details
            $branchlead['lead_no'] = MasterLead::where('sl_no', '=', $branchlead->lead_sl_no)->where('status', '!=', 3)->first()->lead_no ?? '';
            $masterleadArr = MasterLead::where('sl_no', '=', $branchlead->lead_sl_no)->where('status', '!=', 3)->get() ?? [];
            // dd($masterleadArr);
            $eachLeadArr = [];
            foreach($masterleadArr as $masterlead)
            {
                // dd($masterlead);
                $eachLeadCellArr = [];
                $eachLeadHead = LeadHeader::where('id', '=', $masterlead->header_id)->where('status', '!=', 3)->first() ?? [];
                // dd($eachLeadHead);
                $eachLeadCellArr[$eachLeadHead->slug] = $masterlead->header_value ?? '';
                $eachLeadCellArr['is_visible_in_lead_list'] = $eachLeadHead->is_visible_in_lead_list ?? '';
                $eachLeadArr[] = $eachLeadCellArr ;
            }
            // dd($eachLeadArr);
            $branchlead['eachLeadDetailsArr'] = $eachLeadArr ?? [];

            //Lead Activity
            $lastLeadActivityRow = LeadActivity::where('lead_sl_no', '=', $branchlead->lead_sl_no)->where('status', '!=', 3)->orderBy('id', 'desc')->first();      
            // dd($lastLeadActivityRow);
            $branchlead['lead_created_at'] = $lastLeadActivityRow?->created_at?->format('M d, Y h:i A') ?? '';

            $next_followup_date = '';
            $next_followup_time = '';

            if ($lastLeadActivityRow && $lastLeadActivityRow->next_followup_date) {
                try {
                    $next_followup_date = Carbon::parse($lastLeadActivityRow->next_followup_date)->format('M d, Y');
                } catch (\Exception $e) {
                    $next_followup_date = '';
                }
            }

            if ($lastLeadActivityRow && $lastLeadActivityRow->next_followup_time) {
                $time = $lastLeadActivityRow->next_followup_time;
                
                // validate format using regex: 24-hr format like "18:14"
                if (preg_match('/^(2[0-3]|[01]?[0-9]):[0-5][0-9]$/', $time)) {
                    try {
                        $next_followup_time = Carbon::createFromFormat('H:i', $time)->format('h:i A');
                    } catch (\Exception $e) {
                        $next_followup_time = '';
                    }
                } else {
                    $next_followup_time = '';
                }
            }
         
            if(!empty($next_followup_date) && !empty($next_followup_time))
            {
                $branchlead['scheduled_date_time'] = $next_followup_date . ' ' . $next_followup_time;
            }
            else
            {
                $branchlead['scheduled_date_time'] = '' ;
            }

            $lead_activity_count = LeadActivity::where('lead_sl_no', '=', $branchlead->lead_sl_no)->where('status', '!=', 3)->count();
            if($lead_activity_count > 0)
            {
                $branchlead['lead_activity_count'] = $lead_activity_count . ' Update' ;
            }
            else
            {
                $branchlead['lead_activity_count'] = 'New' ;
            }

            //Lead Status
            if(!empty($lastLeadActivityRow))
            {
                $parentStatus = [];
                $parentStatus['name'] = LeadStatus::where('id', '=', $lastLeadActivityRow->parent_status_id)->where('parent_id', '=', 0)->where('status', '!=', 3)->value('name') ?? '';
                $parentStatus['background_color'] = LeadStatus::where('id', '=', $lastLeadActivityRow->parent_status_id)->where('parent_id', '=', 0)->where('status', '!=', 3)->value('background_color') ?? '';
                $parentStatus['font_color'] = LeadStatus::where('id', '=', $lastLeadActivityRow->parent_status_id)->where('parent_id', '=', 0)->where('status', '!=', 3)->value('font_color') ?? '';
                $branchlead['parentStatus'] = $parentStatus;
                $childStatus = [];
                $childStatus['name'] = LeadStatus::where('id', '=', $lastLeadActivityRow->child_status_id)->where('parent_id', '=', $lastLeadActivityRow->parent_status_id)->where('status', '!=', 3)->value('name') ?? '';
                $childStatus['background_color'] = LeadStatus::where('id', '=', $lastLeadActivityRow->child_status_id)->where('parent_id', '=', $lastLeadActivityRow->parent_status_id)->where('status', '!=', 3)->value('background_color') ?? '';
                $childStatus['font_color'] = LeadStatus::where('id', '=', $lastLeadActivityRow->child_status_id)->where('parent_id', '=', $lastLeadActivityRow->parent_status_id)->where('status', '!=', 3)->value('font_color') ?? '';
                $branchlead['childStatus'] = $childStatus;
            }



            $totalLeadArr[] = $branchlead ?? [];

        }
        // dd($totalLeadArr) ;
        

        $title                          = $this->data['title'].' List';
        $page_name                      = 'lead.list';
        $data                           = $this->siteAuthService ->admin_after_login_layout($title,$page_name,$data);
        return view('maincontents.' . $page_name, $data)->with(['totalLeadArr' => $totalLeadArr, 'branchleadPaginated' => $branchleadPaginated, 'perPage' => $perPage , 'page' => $page]);
    }

    public function edit(Request $request, $id)
    {
        $id                        = Helper::decoded($id); //BranchLead ID

        $data['lead_headers']      = LeadHeader::where('status', '=', 1)->orderBy('rank', 'asc')->get();
        // dd($data['lead_headers']);
        $isRequiredArr = LeadHeader::where('is_required', '=', 1)->where('status', '=', 1)->pluck('slug')->toArray();

        if ($request->isMethod('post')) {
            // dd($request);
            $postData = $request->all();
            // dd($id);
            $postData["campaign_type_id"] = BranchLead::where('id', '=', $id)->where('status', '!=', 3)->value("campaign_type_id");
            $postData["campaign_id"] = BranchLead::where('id', '=', $id)->where('status', '!=', 3)->value("campaign_id");
            $postData["branch_id"] = BranchLead::where('id', '=', $id)->where('status', '!=', 3)->value("branch_id");
            $postData["telecaller_id"] = BranchLead::where('id', '=', $id)->where('status', '!=', 3)->value("assigned_telecaller_id");
            // dd($postData);

            $rules = [];            
            $postData = array_slice($postData, 1, null, true); //remove the first element before looping i.e., the _token
            foreach ($postData as $key => $value) 
            {
                if ($key == "phone") 
                {
                    $rules[$key] = 'nullable|digits:10';
                }

                if ($key == "ref-customer-1-number") 
                {
                    $rules[$key] = 'nullable|digits:10';
                }

                if ($key == "ref-customer-2-number") 
                {
                    $rules[$key] = 'nullable|digits:10';
                }

                if ($key == "whatsapp-number") 
                {
                    $rules[$key] = 'nullable|digits:10';
                }

                if ($key == "email") 
                {
                    $rules[$key] = 'nullable|regex:/^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/';
                }

                if(count($isRequiredArr) > 0)
                {
                    if(in_array($key, $isRequiredArr))
                    {
                        if ($key == "phone") 
                        {
                            $rules[$key] = 'required|digits:10';
                        }
                        elseif ($key == "whatsapp-number") 
                        {
                            $rules[$key] = 'required|digits:10';
                        }
                        elseif ($key == "ref-customer-1-number") 
                        {
                            $rules[$key] = 'required|digits:10';
                        }
                        elseif ($key == "ref-customer-2-number") 
                        {
                            $rules[$key] = 'required|digits:10';
                        }
                        elseif ($key == "email") 
                        {
                            $rules[$key] = 'required|regex:/^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/';
                        }
                        else{
                            $rules[$key] = 'required';
                        }
                    }
                }

            }
            // dd($rules);

            if ($this->validate($request, $rules)) 
            {
                /* user activity */
                $activityData = [
                    'user_email'        => session('user_data')['email'],
                    'user_name'         => session('user_data')['name'],
                    'user_type'         => 'ADMIN',
                    'ip_address'        => $request->ip(),
                    'activity_type'     => 3,
                    'activity_details'  => $this->data['title'] . ' Updated',
                    'platform_type'     => 'WEB',
                ];
                UserActivity::insert($activityData);
                /* user activity */

                //inserting in master_leads and branch_leads
                // $sl_no = MasterLead::orderBy('id', 'desc')->value('sl_no') ?? 0;
                // $sl_no++;
                // $lead_no = str_pad($sl_no, 8, '0', STR_PAD_LEFT);

                // dd($postData);
                $leadRow = [];
                $notDuplicate = true;

                foreach ($postData as $key => $value) {
                    $slug = $key;
                    $header_id = LeadHeader::where('slug', '=', $slug)->where('status', '=', 1)->first()->id  ?? '';

                    if ($header_id) {
                        $leadCell = [];
                        $leadCell = [
                            "header_id" => $header_id,
                            "header_value" => isset($value) && trim($value) !== '' ? strip_tags(trim($value)) : NULL,
                            // "upload_id"  => 0,
                            // "sl_no" => $sl_no,
                            // "lead_no" => $lead_no,
                            // "created_by" => session('user_data')['user_id'],
                            "updated_by" => session('user_data')['user_id'],
                        ];

                        $sl_no = BranchLead::where('id', '=', $id)->where('status', '!=', 3)->value("lead_sl_no");
                       
                        if ($slug == 'phone')   //validating with respect to phone
                        {
                            if(!empty($value))
                            {
                                // Check if it's already exists
                                $phone = MasterLead::where('header_id', '=', 4)->where('header_value', '=', strip_tags($value))->where('sl_no', '!=', $sl_no)->where('status', '!=', 3)->exists();
                                if ($phone) //true
                                {
                                    $sl_no_Duplicate =  MasterLead::where('header_id', '=', 4)->where('header_value', '=', strip_tags($value))->where('sl_no', '!=', $sl_no)->where('status', '!=', 3)->value('sl_no');

                                    if($sl_no_Duplicate)
                                    {
                                        $duplicateData = MasterLead::where('sl_no', '=', $sl_no_Duplicate)->where('sl_no', '!=', $sl_no)->first();
                            
                                        //Restricting that, same lead is not inserted in BranchLead table w.r.t same campaign
                                        if(($postData["campaign_type_id"] != 0) && ($postData["campaign_id"] != 0))
                                        {
                                            if(BranchLead::where('campaign_type_id', '=', $postData["campaign_type_id"])->where('campaign_id', '=', $postData["campaign_id"])->where('master_lead_id', '=', $duplicateData->id)->where('lead_sl_no', '!=', $sl_no)->where('status', '!=', 3)->exists())
                                            {          
                                                $leadCell = [];
                                                return redirect()->back()->with('error_message', 'Phone Number Already Exists In That Campaign !!!');
                                            }
                                            elseif(BranchLead::where('campaign_type_id', '!=', $postData["campaign_type_id"])->where('campaign_id', '!=', $postData["campaign_id"])->where('master_lead_id', '=', $duplicateData->id)->where('lead_sl_no', '!=', $sl_no)->where('status', '!=', 3)->exists())
                                            {
                                                // update allowed
                                                // $leadCell = [];
                                                // $notDuplicate = false;

                                                // $dupLeadRow = [];
                                                // $dupLeadRow['master_lead_id'] = $duplicateData->id ;
                                                // $dupLeadRow['lead_sl_no'] = $duplicateData->sl_no ;
                                                // break;
                                            }
                                        }
                                        elseif(($postData["campaign_type_id"] == 0) && ($postData["campaign_id"] == 0))
                                        {
                                            
                                            if(BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '=', 0)->where('campaign_id', '=', 0)->where('lead_sl_no', '!=', $sl_no)->exists()) // true
                                            {
                                                //without selecting campaign and also exist in db without campaign
                                                $leadCell = [];
                                                return redirect()->back()->with('error_message', 'Phone Number Already Exists !!!');
                                            }

                                            if(BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '!=', 0)->where('campaign_id', '!=', 0)->where('lead_sl_no', '!=', $sl_no)->exists()) // true
                                            {
                                                //without selecting campaign and also exist in db with another campaign
                                                // update allowed
                                                // $leadCell = [];
                                                // $notDuplicate = false;
                                                
                                                // $dupLeadRow = [];
                                                // $dupLeadRow['master_lead_id'] = $duplicateData->id ;
                                                // $dupLeadRow['lead_sl_no'] = $duplicateData->sl_no ;
                                                // break;
                                                
                                            }

                                            if((BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '=', 0)->where('campaign_id', '=', 0)->where('lead_sl_no', '!=', $sl_no)->exists()) && (BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '!=', 0)->where('campaign_id', '!=', 0)->where('lead_sl_no', '!=', $sl_no)->exists())) // true
                                            {   
                                                //both
                                                $leadCell = [];
                                                return redirect()->back()->with('error_message', 'Phone Number Already Exists !!!');
                                            }

                                        }
                                    }

                                }
                            }
                        }

                        if ($slug == 'whatsapp-number')   //validating with respect to whatsapp-number
                        {
                            if(!empty($value))
                            {
                                // Check if it's already exists
                                $whatsapp = MasterLead::where('header_id', '=', 14)->where('header_value', '=', strip_tags($value))->where('sl_no', '!=', $sl_no)->where('status', '!=', 3)->exists();
                                if ($whatsapp) //true
                                {
                                    $sl_no_Duplicate = MasterLead::where('header_id', '=', 14)->where('header_value', '=', strip_tags($value))->where('sl_no', '!=', $sl_no)->where('status', '!=', 3)->value('sl_no');

                                    if($sl_no_Duplicate)
                                    {
                                        $duplicateData = MasterLead::where('sl_no', '=', $sl_no_Duplicate)->where('sl_no', '!=', $sl_no)->first();
                            
                                        //Restricting that, same lead is not inserted in BranchLead table w.r.t same campaign
                                        if(($postData["campaign_type_id"] != 0) && ($postData["campaign_id"] != 0))
                                        {
                                            if(BranchLead::where('campaign_type_id', '=', $postData["campaign_type_id"])->where('campaign_id', '=', $postData["campaign_id"])->where('master_lead_id', '=', $duplicateData->id)->where('lead_sl_no', '!=', $sl_no)->where('status', '!=', 3)->exists())
                                            {
                                                $leadCell = [];
                                                return redirect()->back()->with('error_message', 'Whatsapp Number Already Exists In That Campaign !!!');
                                            }
                                            elseif(BranchLead::where('campaign_type_id', '!=', $postData["campaign_type_id"])->where('campaign_id', '!=', $postData["campaign_id"])->where('master_lead_id', '=', $duplicateData->id)->where('lead_sl_no', '!=', $sl_no)->where('status', '!=', 3)->exists())
                                            {
                                                // update allowed
                                                //  $leadCell = [];
                                                //  $notDuplicate = false;
                                                 
                                                //  $dupLeadRow = [];
                                                //  $dupLeadRow['master_lead_id'] = $duplicateData->id ;
                                                //  $dupLeadRow['lead_sl_no'] = $duplicateData->sl_no ;
                                                //  break;
                                            }
                                        }
                                        elseif(($postData["campaign_type_id"] == 0) && ($postData["campaign_id"] == 0))
                                        {
                                            
                                            if(BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '=', 0)->where('campaign_id', '=', 0)->where('lead_sl_no', '!=', $sl_no)->exists()) // true
                                            {
                                                //without selecting campaign and also exist in db without campaign
                                                $leadCell = [];
                                                return redirect()->back()->with('error_message', 'Whatsapp Number Already Exists !!!');
                                            }

                                            if(BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '!=', 0)->where('campaign_id', '!=', 0)->where('lead_sl_no', '!=', $sl_no)->exists()) // true
                                            {
                                                //without selecting campaign and also exist in db with another campaign
                                                // update allowed
                                                //  $leadCell = [];
                                                //  $notDuplicate = false;
                                                 
                                                //  $dupLeadRow = [];
                                                //  $dupLeadRow['master_lead_id'] = $duplicateData->id ;
                                                //  $dupLeadRow['lead_sl_no'] = $duplicateData->sl_no ;
                                                //  break;
                                                
                                            }

                                            if((BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '=', 0)->where('campaign_id', '=', 0)->where('lead_sl_no', '!=', $sl_no)->exists()) && (BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '!=', 0)->where('campaign_id', '!=', 0)->where('lead_sl_no', '!=', $sl_no)->exists())) // true
                                            {   
                                                //both
                                                $leadCell = [];
                                                return redirect()->back()->with('error_message', 'Whatsapp Number Already Exists !!!');
                                            }

                                        }
                                    }
                                }
                            }
                        }



                        $leadRow[] = $leadCell;
                    }
                }

                if (!empty($leadRow) && $notDuplicate) 
                {
                    $sl_no = BranchLead::where('id', '=', $id)->where('status', '!=', 3)->value("lead_sl_no");
                    foreach ($leadRow as $cell)   //updating 1 lead coming from form
                    {
                        $masterLead = MasterLead::where('sl_no', '=', $sl_no)->where('header_id', '=', $cell["header_id"])->where('status', '!=', 3)->first();

                        $masterLead->header_value     = $cell["header_value"];
                        // $masterLead->header_id        = $cell["header_id"];
                        // $masterLead->upload_id        = $cell["upload_id"];
                        // $masterLead->sl_no            = $cell["sl_no"];
                        // $masterLead->lead_no          = $cell["lead_no"];
                        // $masterLead->created_by = session('user_data')['user_id'];
                        $masterLead->updated_by = session('user_data')['user_id'];

                        $masterLead->update();
                    }

                    BranchLead::where('id', '=', $id)->where('status', '!=', 3)->update([
                        "updated_by" => session('user_data')['user_id']
                    ]);
                    return redirect($this->data['controller_route'])->with('success_message',  'Lead Updated Successfully !!!');
                }
                else 
                {
                    return redirect()->back()->with('error_message', 'Lead Update Failed !!!');
                }

                // elseif(!empty($dupLeadRow))
                // {

                //     $branchLead =  new BranchLead();

                //     $branchLead->upload_id = 0;
                //     $branchLead->master_lead_id = $dupLeadRow['master_lead_id'] ; 
                //     $branchLead->lead_sl_no = $dupLeadRow['lead_sl_no'] ;
                //     $branchLead->branch_id = $postData["branch_id"];
                //     $branchLead->campaign_type_id = $postData["campaign_type_id"] ?? 0;
                //     $branchLead->campaign_id = $postData["campaign_id"] ?? 0;
                //     $branchLead->assigned_telecaller_id = $postData["telecaller_id"];
                //     $branchLead->created_by = session('user_data')['user_id'];
                //     $branchLead->updated_by = session('user_data')['user_id'];

                //     $branchLead->save();
                    
                //     return redirect($this->data['controller_route'])->with('success_message',  'Lead Added Successfully !!!');

                // }
                
            } else {
                return redirect()->back()->with('error_message', 'Star Marked Fields Are Required With Valid Data !!!');
            }
        }

        $data['branches']          = Branch::where('status', '=', 1)->get();
        $data['campaign_types']    = CampaignType::where('status', '=', 1)->get();

        //for dropdowns
        $data['country']           = Country::where('status', '=', 1)->get();
        $data['state']             = State::where('status', '=', 1)->get();
        $data['source']            = Source::where('status', '=', 1)->get();
        
        //fetching lead details
        $branchlead  = BranchLead::where('id', '=', $id)->where('status', '!=', 3)->first() ?? [];
        $masterleadArr = MasterLead::where('sl_no', '=', $branchlead->lead_sl_no)->where('status', '!=', 3)->get() ?? [];
        // dd($masterleadArr);
        $eachLeadArr = [];
        foreach($masterleadArr as $masterlead)
        {
            // dd($masterlead);
            $eachLeadCellArr = [];
            $eachLeadHead = LeadHeader::where('id', '=', $masterlead->header_id)->where('status', '!=', 3)->first() ?? [];
            // dd($eachLeadHead);
            $eachLeadCellArr[$eachLeadHead->slug] = $masterlead->header_value ?? '';
            // $eachLeadCellArr['is_visible_in_lead_list'] = $eachLeadHead->is_visible_in_lead_list ?? '';
            $eachLeadArr[] = $eachLeadCellArr ;
        }
        // dd($eachLeadArr);
        $data['row'] = $eachLeadArr ?? [];



        $data['module']            = $this->data;
        $title                     = $this->data['title'].' Update';
        $page_name                 = 'lead.edit';

        $data                      = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
        return view('maincontents.' . $page_name, $data)->with(["isRequiredArr"=>$isRequiredArr]);
    }

    public function delete(Request $request, $id)
    {
        $id = Helper::decoded($id); //BranchLead ID

        $fields = [
            'status'             => 3,
            'deleted_at'         => date('Y-m-d H:i:s'),
            'updated_by'         => session('user_data')['user_id'],
            // 'updated_at'         => date('Y-m-d H:i:s'),
        ];
        $lead_sl_no = BranchLead::find($id)->lead_sl_no ;

        // from MasterLead
        MasterLead::where('sl_no', '=', $lead_sl_no)->update($fields);

        // from BranchLead
        BranchLead::where('lead_sl_no', '=', $lead_sl_no)->update($fields);

        /* user activity */
            $activityData = [
                'user_email'        => session('user_data')['email'],
                'user_name'         => session('user_data')['name'],
                'user_type'         => 'ADMIN',
                'ip_address'        => $request->ip(),
                'activity_type'     => 3,
                'activity_details'  => $this->data['title'] . ' Deleted',
                'platform_type'     => 'WEB',
            ];
            UserActivity::insert($activityData);
        /* user activity */
        return redirect($this->data['controller_route'])->with('success_message', $this->data['title'].' Deleted Successfully !!!');
    }

    public function change_status(Request $request, $id)
    {
        $id = Helper::decoded($id);   //BranchLead ID
        $model = BranchLead::find($id);
        $lead_sl_no = BranchLead::find($id)->lead_sl_no ;
        if ($model->status == 1)
        {
            $fields = [
                'status'             => 0,
                'updated_by'         => session('user_data')['user_id'],
                // 'updated_at'         => date('Y-m-d H:i:s'),
            ];

            // from MasterLead
            MasterLead::where('sl_no', '=', $lead_sl_no)->update($fields);

            // from BranchLead
            BranchLead::where('lead_sl_no', '=', $lead_sl_no)->update($fields);

            $msg            = 'Deactivated';
            /* user activity */
                $activityData = [
                    'user_email'        => session('user_data')['email'],
                    'user_name'         => session('user_data')['name'],
                    'user_type'         => 'ADMIN',
                    'ip_address'        => $request->ip(),
                    'activity_type'     => 3,
                    'activity_details'  => $this->data['title'] . ' Deactivated',
                    'platform_type'     => 'WEB',
                ];
                UserActivity::insert($activityData);
            /* user activity */
        } else {
            
            $fields = [
                'status'             => 1,
                'updated_by'         => session('user_data')['user_id'],
                // 'updated_at'         => date('Y-m-d H:i:s'),
            ];

            // from MasterLead
            MasterLead::where('sl_no', '=', $lead_sl_no)->update($fields);

            // from BranchLead
            BranchLead::where('lead_sl_no', '=', $lead_sl_no)->update($fields);

            $msg            = 'Activated';
            /* user activity */
                $activityData = [
                    'user_email'        => session('user_data')['email'],
                    'user_name'         => session('user_data')['name'],
                    'user_type'         => 'ADMIN',
                    'ip_address'        => $request->ip(),
                    'activity_type'     => 3,
                    'activity_details'  => $this->data['title'] . ' Activated',
                    'platform_type'     => 'WEB',
                ];
                UserActivity::insert($activityData);
            /* user activity */
        }     
               
        return redirect($this->data['controller_route'])->with('success_message', $this->data['title'].' '.$msg.' Successfully !!!');
    }

    /* ajax request */
    public function getLeadCallData(Request $request)
    {
        if ($request->isMethod('post'))
        {
            $id = Helper::decoded($request->branchLead_id); //BranchLead ID
            $branchLeadArr = BranchLead::find($id);

            // lead added updated
            $addedUpdated = [];
            $addedUpdated['added_by_name'] = ( User::where('id', '=', $branchLeadArr->created_by)->where('status', '!=', 3)->value('first_name') . ' '. User::where('id', '=', $branchLeadArr->created_by)->where('status', '!=', 3)->value('last_name') ) ?? '';
            $addedUpdated['updated_by_name'] = ( User::where('id', '=', $branchLeadArr->updated_by)->where('status', '!=', 3)->value('first_name') . ' '. User::where('id', '=', $branchLeadArr->updated_by)->where('status', '!=', 3)->value('last_name') ) ?? '';
            $addedUpdated['created_at'] = $branchLeadArr->created_at?->format('M d, Y h:i A') ?? '';
            $addedUpdated['updated_at'] = $branchLeadArr->updated_at?->format('M d, Y h:i A') ?? '';

            // activity count and sl no.
            $leadActivityCount = [];
            $leadActivityCount['lead_activity_count'] = LeadActivity::where('lead_sl_no', '=', $branchLeadArr->lead_sl_no)->where('status', '!=', 3)->count();
            $leadActivityCount['lead_no'] = MasterLead::where('sl_no', '=', $branchLeadArr->lead_sl_no)->where('status', '!=', 3)->first()->lead_no ?? '';

            // lead details
            $masterleadArr = MasterLead::where('sl_no', '=', $branchLeadArr->lead_sl_no)->where('status', '!=', 3)->get() ?? [];
            $eachLeadArr = [];
            foreach($masterleadArr as $masterlead)
            {
                // dd($masterlead);
                $eachLeadCellArr = [];
                $eachLeadHead = LeadHeader::where('id', '=', $masterlead->header_id)->where('status', '!=', 3)->first() ?? [];
                // dd($eachLeadHead);
                $eachLeadCellArr[$eachLeadHead->slug] = $masterlead->header_value ?? '';
                $eachLeadCellArr['is_visible_in_lead_list'] = $eachLeadHead->is_visible_in_lead_list ?? '';
                $eachLeadArr[] = $eachLeadCellArr ;
            }

            //fetch current campaign
            $campaignArr = [];
            $campaignArr['campaign_type_name'] = CampaignType::where('id', '=', $branchLeadArr->campaign_type_id)->where('status', '!=', 3)->value('name') ?? '';
            $campaignArr['campaign_name'] = Campaign::where('id', '=', $branchLeadArr->campaign_id)->where('status', '!=', 3)->value('name') ?? '';

            // fetch lead status
            $parentStatusArr = LeadStatus::where('parent_id', '=', 0)->where('status', '!=', 3)->get();
            $ChildParentStatusArr = [];
            foreach($parentStatusArr as $parentStatus)
            {
                $childStatusArr = LeadStatus::where('parent_id', '=', $parentStatus->id)->where('status', '!=', 3)->get();
                $statusArr = [];
                foreach($childStatusArr as $childStatus)
                {
                    $statusArr['parent_status_id'] = $parentStatus->id;
                    $statusArr['child_status_id'] = $childStatus->id;
                    $statusArr['name'] = $childStatus->name . ' [' . $parentStatus->name . ']' ;
                    $ChildParentStatusArr[] = $statusArr;
                }
            }

            // fetch call purpose
            $purposeArr =  Purpose::where('status', '!=', 3)->get();

            // fetch mood
            $moodArr = Mood::where('status', '!=', 3)->get();

            //fetch feedback tags
            $feedbackTagArr = FeedbackTag::where('status', '!=', 3)->get();

            // fetch lead history
            $leadActivityArr = LeadActivity::where('lead_sl_no', '=', $branchLeadArr->lead_sl_no)->where('status', '!=', 3)->orderBy('id', 'desc')->get();

            $leadHistoryArr = [];
            foreach($leadActivityArr as $leadActivity)
            {
                $leadHistory = [];
                $leadHistory['branch_name'] = Branch::where('id', '=', $leadActivity->branch_id)->where('status', '!=', 3)->value('name') ?? '';
                $leadHistory['campaign_type_name'] = CampaignType::where('id', '=', $leadActivity->campaign_type_id)->where('status', '!=', 3)->value('name') ?? '';
                $leadHistory['campaign_name'] = Campaign::where('id', '=', $leadActivity->campaign_id)->where('status', '!=', 3)->value('name') ?? '';
                
                //fetching parent status
                $parentStatus = [];
                $parentStatus['name'] = LeadStatus::where('id', '=', $leadActivity->parent_status_id)->where('parent_id', '=', 0)->where('status', '!=', 3)->value('name') ?? '';
                $parentStatus['background_color'] = LeadStatus::where('id', '=', $leadActivity->parent_status_id)->where('parent_id', '=', 0)->where('status', '!=', 3)->value('background_color') ?? '';
                $parentStatus['font_color'] = LeadStatus::where('id', '=', $leadActivity->parent_status_id)->where('parent_id', '=', 0)->where('status', '!=', 3)->value('font_color') ?? '';
                $leadHistory['parentStatus'] = $parentStatus;
                
                //fetching child status
                $childStatus = [];
                $childStatus['name'] = LeadStatus::where('id', '=', $leadActivity->child_status_id)->where('parent_id', '=', $leadActivity->parent_status_id)->where('status', '!=', 3)->value('name') ?? '';
                $childStatus['background_color'] = LeadStatus::where('id', '=', $leadActivity->child_status_id)->where('parent_id', '=', $leadActivity->parent_status_id)->where('status', '!=', 3)->value('background_color') ?? '';
                $childStatus['font_color'] = LeadStatus::where('id', '=', $leadActivity->child_status_id)->where('parent_id', '=', $leadActivity->parent_status_id)->where('status', '!=', 3)->value('font_color') ?? '';
                $leadHistory['childStatus'] = $childStatus;

                $leadHistory['comment'] = $leadActivity->comment ?? '';

                //fetching mood
                $mood = [];
                $mood['name'] = Mood::where('id', '=', $leadActivity->mood)->where('status', '!=', 3)->value('name') ?? '';
                $mood['emoji'] = Mood::where('id', '=', $leadActivity->mood)->where('status', '!=', 3)->value('emoji') ?? '';
                $mood['color'] = Mood::where('id', '=', $leadActivity->mood)->where('status', '!=', 3)->value('color') ?? '';
                $leadHistory['mood'] = $mood;

                $leadHistory['purpose_name'] = Purpose::where('id', '=', $leadActivity->purpose_id)->where('status', '!=', 3)->value('name') ?? '';
                
                //fetch feedback tags
                if(!empty($leadActivity->feedback_tag_ids))
                {
                    $feedbackTagNameArr = [] ;
                    foreach(json_decode($leadActivity->feedback_tag_ids) as $feedbackTagId)
                    {
                        $feedbackTagNameArr[] = FeedbackTag::where('id', '=', $feedbackTagId)->where('status', '!=', 3)->value('name') ?? '';
                    }
                    $leadHistory['feedbackTagNameArr'] = $feedbackTagNameArr;
                }
                else
                {
                    $leadHistory['feedbackTagNameArr'] = [] ;
                }

                $leadHistory['note'] = $leadActivity->note ?? '';

                $leadHistory['next_followup_date'] = $leadActivity->next_followup_date ? Carbon::parse($leadActivity->next_followup_date)->format('M d, Y') : '';
                $leadHistory['next_followup_time'] = $leadActivity->next_followup_time ? Carbon::createFromFormat('H:i', $leadActivity->next_followup_time)->format('h:i A') : '';
                $leadHistory['last_call'] = $leadActivity->created_at ? $leadActivity->created_at->format('M d, Y h:i A') : '';

                $leadHistory['assigned_telecaller_name'] = ( User::where('id', '=', $leadActivity->assigned_telecaller_id)->where('status', '!=', 3)->value('first_name') . ' '. User::where('id', '=', $leadActivity->assigned_telecaller_id)->where('status', '!=', 3)->value('last_name') ) ?? '';


                $leadHistoryArr[] = $leadHistory;
            }


            


            $page_name  = 'lead.modal';
            $html = view('maincontents.' . $page_name)->with(["addedUpdated" => $addedUpdated , "leadActivityCount" => $leadActivityCount , "eachLeadArr" => $eachLeadArr , "ChildParentStatusArr" => $ChildParentStatusArr , "purposeArr" => $purposeArr , "moodArr" => $moodArr , "feedbackTagArr" => $feedbackTagArr , "leadHistoryArr" => $leadHistoryArr , "campaignArr" => $campaignArr ,])->render(); // modal.blade.php

            return response()->json([
                'html' => $html
            ]);

            // return response()->json($leadHistoryArr);
        }
    }
    

   

    public function updateLeadStatus(Request $request)
    {
        if ($request->isMethod('post'))
        {
            $rules = [
                'branchLead_id' => 'required',
                'callPurpose' => 'required',
                'leadStatus' => 'required',
                'mood' => 'required',
                'parent_status_id' => 'required',
                'child_status_id' => 'required',
            ];

            if($request->leadStatus)
            {
               if(!Str::contains(strtolower($request->leadStatus), '[dump]'))
               {
                    $rules["nextFollowUpDate"] = 'required';
                    $rules["nextFollowUpTime"] = 'required';
               }
            }
            
            if($this->validate($request, $rules))
            {
                /* user activity */
                $activityData = [
                    'user_email'        => session('user_data')['email'],
                    'user_name'         => session('user_data')['name'],
                    'user_type'         => 'ADMIN',
                    'ip_address'        => $request->ip(),
                    'activity_type'     => 3,
                    'activity_details'  => $this->data['title'] . ' Status Updated',
                    'platform_type'     => 'WEB',
                ];
                UserActivity::insert($activityData);
                /* user activity */
                
                $id = Helper::decoded($request->branchLead_id); //BranchLead ID
                $branchLeadArr = BranchLead::find($id);

                $fields = [
                    'upload_id' => strip_tags($branchLeadArr->upload_id),
                    'master_lead_id' => strip_tags($branchLeadArr->master_lead_id),
                    'lead_sl_no' => strip_tags($branchLeadArr->lead_sl_no),
                    'branch_id' => strip_tags($branchLeadArr->branch_id),
                    'campaign_type_id' => strip_tags($branchLeadArr->campaign_type_id),
                    'campaign_id' => strip_tags($branchLeadArr->campaign_id),
                    'assigned_telecaller_id' => strip_tags($branchLeadArr->assigned_telecaller_id),
                    'parent_status_id' => strip_tags($request->parent_status_id),
                    'child_status_id' => strip_tags($request->child_status_id),
                    'comment' => isset($request->specialComment) ? strip_tags($request->specialComment) : NULL,
                    'mood' => strip_tags($request->mood),
                    'purpose_id' => strip_tags($request->callPurpose),
                    'feedback_tag_ids' => isset($request->feedbackTag) ? strip_tags(json_encode($request->feedbackTag)) : NULL,
                    'note' => isset($request->note) ? strip_tags($request->note) : NULL,
                    'next_followup_date' => isset($request->nextFollowUpDate) ? strip_tags($request->nextFollowUpDate) : NULL,
                    'next_followup_time' => isset($request->nextFollowUpTime) ? strip_tags($request->nextFollowUpTime) : NULL,
                    'created_by' => session('user_data')['user_id'],
                    'updated_by' => session('user_data')['user_id'],
                ];
                LeadActivity::insert($fields);
                return response()->json(['success_message' => 'Lead Status Updated Successfully !!!']);
            }
            else
            {
                return response()->json(['error_message' => 'Star Marks Fields Are Required !!!']);
            }

        }
        else
        {
            return response()->json(['error_message' => 'Invalid Request !!!']);
        }
    }

    public function fetchLeadHistory(Request $request)
    {
        if ($request->isMethod('post'))
        {
            $id = Helper::decoded($request->branchLead_id); //BranchLead ID
            $branchLeadArr = BranchLead::find($id);

            $leadActivityArr = LeadActivity::where('lead_sl_no', '=', $branchLeadArr->lead_sl_no)->where('status', '!=', 3)->orderBy('id', 'desc')->get();

            $leadHistoryArr = [];
            foreach($leadActivityArr as $leadActivity)
            {
                $leadHistory = [];
                $leadHistory['branch_name'] = Branch::where('id', '=', $leadActivity->branch_id)->where('status', '!=', 3)->value('name') ?? '';
                $leadHistory['campaign_type_name'] = CampaignType::where('id', '=', $leadActivity->campaign_type_id)->where('status', '!=', 3)->value('name') ?? '';
                $leadHistory['campaign_name'] = Campaign::where('id', '=', $leadActivity->campaign_id)->where('status', '!=', 3)->value('name') ?? '';
                
                //fetching parent status
                $parentStatus = [];
                $parentStatus['name'] = LeadStatus::where('id', '=', $leadActivity->parent_status_id)->where('parent_id', '=', 0)->where('status', '!=', 3)->value('name') ?? '';
                $parentStatus['background_color'] = LeadStatus::where('id', '=', $leadActivity->parent_status_id)->where('parent_id', '=', 0)->where('status', '!=', 3)->value('background_color') ?? '';
                $parentStatus['font_color'] = LeadStatus::where('id', '=', $leadActivity->parent_status_id)->where('parent_id', '=', 0)->where('status', '!=', 3)->value('font_color') ?? '';
                $leadHistory['parentStatus'] = $parentStatus;
                
                //fetching child status
                $childStatus = [];
                $childStatus['name'] = LeadStatus::where('id', '=', $leadActivity->child_status_id)->where('parent_id', '=', $leadActivity->parent_status_id)->where('status', '!=', 3)->value('name') ?? '';
                $childStatus['background_color'] = LeadStatus::where('id', '=', $leadActivity->child_status_id)->where('parent_id', '=', $leadActivity->parent_status_id)->where('status', '!=', 3)->value('background_color') ?? '';
                $childStatus['font_color'] = LeadStatus::where('id', '=', $leadActivity->child_status_id)->where('parent_id', '=', $leadActivity->parent_status_id)->where('status', '!=', 3)->value('font_color') ?? '';
                $leadHistory['childStatus'] = $childStatus;

                $leadHistory['comment'] = $leadActivity->comment ?? '';

                //fetching mood
                $mood = [];
                $mood['name'] = Mood::where('id', '=', $leadActivity->mood)->where('status', '!=', 3)->value('name') ?? '';
                $mood['emoji'] = Mood::where('id', '=', $leadActivity->mood)->where('status', '!=', 3)->value('emoji') ?? '';
                $mood['color'] = Mood::where('id', '=', $leadActivity->mood)->where('status', '!=', 3)->value('color') ?? '';
                $leadHistory['mood'] = $mood;

                $leadHistory['purpose_name'] = Purpose::where('id', '=', $leadActivity->purpose_id)->where('status', '!=', 3)->value('name') ?? '';
                
                //fetch feedback tags
                if(!empty($leadActivity->feedback_tag_ids))
                {
                    $feedbackTagNameArr = [] ;
                    foreach(json_decode($leadActivity->feedback_tag_ids) as $feedbackTagId)
                    {
                        $feedbackTagNameArr[] = FeedbackTag::where('id', '=', $feedbackTagId)->where('status', '!=', 3)->value('name') ?? '';
                    }
                    $leadHistory['feedbackTagNameArr'] = $feedbackTagNameArr;
                }
                else
                {
                    $leadHistory['feedbackTagNameArr'] = [] ;
                }

                $leadHistory['note'] = $leadActivity->note ?? '';

                $leadHistory['next_followup_date'] = $leadActivity->next_followup_date ? Carbon::parse($leadActivity->next_followup_date)->format('M d, Y') : '';
                $leadHistory['next_followup_time'] = $leadActivity->next_followup_time ? Carbon::createFromFormat('H:i', $leadActivity->next_followup_time)->format('h:i A') : '';
                $leadHistory['last_call'] = $leadActivity->created_at ? $leadActivity->created_at->format('M d, Y h:i A') : '';

                $leadHistory['assigned_telecaller_name'] = ( User::where('id', '=', $leadActivity->assigned_telecaller_id)->where('status', '!=', 3)->value('first_name') . ' '. User::where('id', '=', $leadActivity->assigned_telecaller_id)->where('status', '!=', 3)->value('last_name') ) ?? '';


                $leadHistoryArr[] = $leadHistory;
            }


            return response()->json($leadHistoryArr);
        }
    }
     
    public function fetchLeadActivityCount(Request $request)
    {
        if ($request->isMethod('post'))
        {
            $id = Helper::decoded($request->branchLead_id); //BranchLead ID
            $branchLeadArr = BranchLead::find($id);
            $arr = [];
            $arr['lead_activity_count'] = LeadActivity::where('lead_sl_no', '=', $branchLeadArr->lead_sl_no)->where('status', '!=', 3)->count();
            $arr['lead_no'] = MasterLead::where('sl_no', '=', $branchLeadArr->lead_sl_no)->where('status', '!=', 3)->first()->lead_no ?? '';

            return response()->json($arr);
        }
    }
   
    /* ajax request */
}
