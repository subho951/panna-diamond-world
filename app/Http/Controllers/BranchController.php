<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use App\Models\UserActivity;
use App\Services\SiteAuthService;
use App\Helpers\Helper;
use Auth;
use Session;

class BranchController extends Controller
{
    protected $siteAuthService;
    protected $data;
    public function __construct()
    {
        $this->siteAuthService = new SiteAuthService();
        $this->data = array(
            'title'             => 'Branch',
            'controller'        => 'BranchController',
            'controller_route'  => 'branch',
            'primary_key'       => 'id',
            'table_name'        => 'branches',
        );
    }


    /* add */
    public function add(Request $request){
        $data['module']           = $this->data;
        if($request->isMethod('post')){
            $postData = $request->all();
            $rules = [
                'name'                  => 'required',
                'email'                 => 'required',
                'phone_code'            => 'required',
                'phone'                 => 'required',
                'address'               => 'required',
                'pincode'               => 'required'
            ];
            if($this->validate($request, $rules)){
                /* user activity */
                    $activityData = [
                        'user_email'        => session('user_data')['email'],
                        'user_name'         => session('user_data')['name'],
                        'user_type'         => 'ADMIN',
                        'ip_address'        => $request->ip(),
                        'activity_type'     => 3,
                        'activity_details'  => $postData['name'] . ' ' . $this->data['title'] . ' Added',
                        'platform_type'     => 'WEB',
                    ];
                    UserActivity::insert($activityData);
                /* user activity */
                $fields = [
                    'name'                  => strip_tags($postData['name']),
                    'email'                 => strip_tags($postData['email']),
                    'phone_code'            => strip_tags($postData['phone_code']),
                    'phone'                 => strip_tags($postData['phone']),
                    'address'               => strip_tags($postData['address']),
                    'pincode'               => strip_tags($postData['pincode']),
                    'status'                => ((array_key_exists("status",$postData))?1:0),
                ];
                Branch::insert($fields);
                return redirect($this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Inserted Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        $data['module']                 = $this->data;
        $title                          = $this->data['title'].' Add';
        $page_name                      = 'branch.add-edit';
        $data['row']                    = [];
        $data                           = $this->siteAuthService ->admin_after_login_layout($title,$page_name,$data);
        return view('maincontents.' . $page_name, $data);
    }
    /* add */


}
