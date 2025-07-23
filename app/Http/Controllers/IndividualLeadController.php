<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Branch;
use App\Models\UploadLead;
use App\Models\UserActivity;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\Source;
use App\Models\LeadHeader;
use App\Models\CampaignType;
use App\Models\Campaign;
use App\Models\MasterLead;
use App\Models\BranchLead;
use App\Services\SiteAuthService;
use App\Helpers\Helper;

use Auth;
use Session;
use Hash;
use DB;

class IndividualLeadController extends Controller
{
    protected $siteAuthService;
    protected $data;
    public function __construct()
    {
        $this->siteAuthService = new SiteAuthService();
        $this->data = array(
            'title'             => 'Lead',
            'controller'        => 'IndividualLeadController',
            'controller_route'  => 'individual-lead',
            'primary_key'       => 'id',
        );
    }

    public function add(Request $request)
    {
        $data['module']            = $this->data;
        $title                     = 'Add Individual' . ' ' . $this->data['title'];
        $page_name                 = 'individual-lead.add';

        $data['lead_headers']      = LeadHeader::where('status', '=', 1)->orderBy('rank', 'asc')->get();
        // dd($data['lead_headers']);
        $isRequiredArr = LeadHeader::where('is_required', '=', 1)->where('status', '=', 1)->pluck('slug')->toArray();

        if ($request->isMethod('post')) {
            $postData = $request->all();
            // dd($postData);
          
            $rules = [];            
            $postData = array_slice($postData, 1, null, true); //remove the first element before looping i.e., the _token
            foreach ($postData as $key => $value) 
            {
                if($key == "branch_id")
                {
                    $rules[$key] = 'required';
                }

                if($key == "telecaller_id")
                {
                    $rules[$key] = 'required';
                }

                if ($key == "phone") 
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
                    'activity_details'  => $this->data['title'] . ' Added',
                    'platform_type'     => 'WEB',
                ];
                UserActivity::insert($activityData);
                /* user activity */

                //inserting in master_leads and branch_leads
                $sl_no = MasterLead::orderBy('id', 'desc')->value('sl_no') ?? 0;
                $sl_no++;
                $lead_no = str_pad($sl_no, 8, '0', STR_PAD_LEFT);

                // dd($postData);
                $leadRow = [];
                foreach ($postData as $key => $value) {
                    $slug = $key;
                    $header_id = LeadHeader::where('slug', '=', $slug)->where('status', '=', 1)->first()->id  ?? '';

                    if ($header_id) {
                        $leadCell = [];
                        $leadCell = [
                            "header_id" => $header_id,
                            "header_value" => !empty($value) ? strip_tags($value) : NULL,
                            "upload_id"  => 0,
                            "sl_no" => $sl_no,
                            "lead_no" => $lead_no
                        ];

                        if ($slug == 'phone')   //validating with respect to phone
                        {
                            if(!empty($value))
                            {
                                $phone = MasterLead::where('header_id', '=', 4)->where('header_value', '=', strip_tags($value))->where('status', '!=', 3)->exists();
                                if ($phone) //true
                                {
                                    $leadCell = [];
                                    return redirect()->back()->with('error_message', 'This Lead Already Exists With Respect To Phone Number !!!');
                                }
                            }
                        }

                        if ($slug == 'whatsapp-number')   //validating with respect to whatsapp-number
                        {
                            if(!empty($value))
                            {
                                $whatsapp = MasterLead::where('header_id', '=', 14)->where('header_value', '=', strip_tags($value))->where('status', '!=', 3)->exists();
                                if ($whatsapp) //true
                                {
                                    $leadCell = [];
                                    return redirect()->back()->with('error_message', 'This Lead Already Exists With Respect To WhatsApp Number !!!');
                                }
                            }
                        }



                        $leadRow[] = $leadCell;
                    }
                }

                if (!empty($leadRow)) {
                    $cellsPerRow = 0;
                    foreach ($leadRow as $cell)   //uploading 1 lead coming from form
                    {
                        $masterLead = new MasterLead();

                        $masterLead->header_id        = $cell["header_id"];
                        $masterLead->header_value     = $cell["header_value"];
                        $masterLead->upload_id        = $cell["upload_id"];
                        $masterLead->sl_no            = $cell["sl_no"];
                        $masterLead->lead_no          = $cell["lead_no"];

                        $masterLead->save();

                        if ($cellsPerRow == 0) {
                            $branchLead =  new BranchLead();

                            $branchLead->upload_id = $masterLead->upload_id;
                            $branchLead->master_lead_id = $masterLead->id;
                            $branchLead->lead_sl_no = $masterLead->sl_no;
                            $branchLead->branch_id = $postData["branch_id"];
                            $branchLead->campaign_type_id = $postData["campaign_type_id"] ?? 0;
                            $branchLead->campaign_id = $postData["campaign_id"] ?? 0;
                            $branchLead->assigned_telecaller_id = $postData["telecaller_id"];

                            $branchLead->save();
                        }

                        $cellsPerRow++;
                    }

                    return redirect($this->data['controller_route'] . '/add')->with('success_message',  'Lead Added Successfully !!!');
                } else {
                    return redirect()->back()->with('error_message', 'Lead Insertion Failed !!!');
                }
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
        //for dropdowns

        $data                      = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
        return view('maincontents.' . $page_name, $data)->with(["isRequiredArr"=>$isRequiredArr]);
    }



    /* ajax requests */
    public function fetchTelecaller(Request $request)
    {
        if ($request->isMethod('post')) {
            $branch_id = $request->branch_id;
            $telecaller = User::where('branch_id', '=', $branch_id)->where('role_id', '=', 3)->where('status', '=', 1)->get();
            return response()->json($telecaller);
        }
    }
    public function fetchCampaign(Request $request)
    {
        if ($request->isMethod('post')) {
            $campaign_type_id = $request->campaign_type_id;
            $campaign = Campaign::where('campaign_type_id', '=', $campaign_type_id)->where('status', '=', 1)->get();
            return response()->json($campaign);
        }
    }
    public function fetchState(Request $request)
    {
        if ($request->isMethod('post')){
            $country_id  = $request->country_id;
            $state = State::where('country_id', '=', $country_id)->where('status', '=', 1)->get();
            return response()->json($state);
        }
    }
    public function fetchPhoneCode(Request $request)
    {
        if ($request->isMethod('post')){
            $country_id = $request->country_id;
            $phone_code = Country::where('id', '=', $country_id)->where('status', '=', 1)->get();
            return response()->json($phone_code);
        }
    }
    /* ajax requests */
}
