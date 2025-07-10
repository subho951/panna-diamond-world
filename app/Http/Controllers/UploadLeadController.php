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
use App\Models\CampaignType;
use App\Models\Campaign;
use App\Services\SiteAuthService;
use App\Helpers\Helper;
use Auth;
use Session;
use Hash;
use DB;


class UploadLeadController extends Controller
{
    protected $siteAuthService;
    protected $data;
    public function __construct()
    {
        $this->siteAuthService = new SiteAuthService();
        $this->data = array(
            'title'             => 'Upload Lead',
            'controller'        => 'UploadLeadController',
            'controller_route'  => 'upload-lead',
            'primary_key'       => 'id',
        );
    }

    /* upload */
    public function upload(Request $request){
        $data['module']           = $this->data;
        if($request->isMethod('post')){
            $postData = $request->all();
            Helper::pr($postData);
            $rules = [
                'branch_id'             => 'required',
                'telecaller_id'         => 'required',
                'lead_title'            => 'required',
                'lead_date'             => 'required',
                'lead_file'             => 'required',
            ];
            if($this->validate($request, $rules)){
                /* user activity */
                    $activityData = [
                        'user_email'        => session('user_data')['email'],
                        'user_name'         => session('user_data')['name'],
                        'user_type'         => 'ADMIN',
                        'ip_address'        => $request->ip(),
                        'activity_type'     => 3,
                        'activity_details'  => $postData['lead_title'] . ' ' . $this->data['title'] . ' Added',
                        'platform_type'     => 'WEB',
                    ];
                    UserActivity::insert($activityData);
                /* user activity */
                /* lead file */
                    $upload_folder = 'lead';
                    $imageFile      = $request->file('lead_file');
                    if($imageFile != ''){
                        $imageName      = $imageFile->getClientOriginalName();
                        $uploadedFile   = $this->upload_single_file('lead_file', $imageName, $upload_folder, 'csv');
                        if($uploadedFile['status']){
                            $lead_file = $uploadedFile['newFilename'];
                        } else {
                            return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                        }
                    } else {
                        return redirect()->back()->with(['error_message' => 'Please Upload ' . $this->data['title'] . ' File']);
                    }
                /* lead file */
                $fields = [
                    'branch_id'             => strip_tags($postData['branch_id']),
                    'telecaller_id'         => json_encode($postData['telecaller_id']),
                    'lead_title'            => strip_tags($postData['lead_title']),
                    'lead_date'             => date_format(date_create(strip_tags($postData['lead_date'])), "Y-m-d"),
                    'lead_file'             => strip_tags($postData['lead_file']),
                ];
                Helper::pr($fields);
                UploadLead::insert($fields);
                return redirect($this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Inserted Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        $data['module']                 = $this->data;
        $title                          = $this->data['title'];
        $page_name                      = 'upload-lead.upload';
        $data['row']                    = [];
        $data['branches']               = Branch::where('status', '=',1)->get();
        $data['campaign_types']         = CampaignType::where('status', '=',1)->get();
        $data                           = $this->siteAuthService ->admin_after_login_layout($title,$page_name,$data);
        return view('maincontents.' . $page_name, $data);
    }
    /* upload */

    public function fetchTelecaller(Request $request){
        if($request->isMethod('post'))
        {
            $branch_id = $request->branch_id;
            $telecaller = User::where('branch_id', '=', $branch_id)->where('role_id', '=',3)->where('status', '=',1)->get();
            return response()->json($telecaller);
        }
    }
    public function fetchCampaign(Request $request){
       if($request->isMethod('post'))
       {
            $campaign_type_id = $request->campaign_type_id;
            $campaign = Campaign::where('campaign_type_id', '=', $campaign_type_id)->where('status', '=',1)->get();
            return response()->json($campaign);
       }
    }

}
