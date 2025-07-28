<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\Branch;
use App\Models\BranchLead;
use App\Models\CampaignType;
use App\Models\Campaign;
use App\Models\DeleteAccountRequest;
use App\Models\EmailLog;
use App\Models\FeedbackTag;
use App\Models\GeneralSetting;
use App\Models\LeadStatus;
use App\Models\LeadActivity;
use App\Models\MasterLead;
use App\Models\Mood;
use App\Models\Page;
use App\Models\Purpose;
use App\Models\Role;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserActivity;

use App\Services\SiteAuthService;
use App\Helpers\Helper;

use Auth;
use Session;
use Hash;
use DB;
use App\Libraries\CreatorJwt;
use App\Libraries\JWT;

date_default_timezone_set("Asia/Calcutta");

class ApiController extends Controller
{
    protected $siteAuthService;
    protected $data;
    public function __construct()
    {
        $this->siteAuthService = new SiteAuthService();
    }
    
    /* before login screen */
        /* general settings */
            public function getAppSetting(Request $request){
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $generalSetting = GeneralSetting::where('is_active', '=', 1)->orderBy('id', 'ASC')->get();
                    if($generalSetting){
                        foreach($generalSetting as $setting){
                            $apiResponse[] = [
                                'id'             => $setting->id,
                                'key'            => $setting->key,
                                'value'          => $setting->value
                            ];
                        }
                    }
                    http_response_code(200);
                    $apiStatus          = TRUE;
                    $apiMessage         = 'Data Available !!!';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                } else {
                    http_response_code(200);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* general settings */
        /* static page */
            public function getStaticPages(Request $request){
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source', 'page_slug'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $page_slug = $requestData['page_slug'];
                    $pageContent  = Page::select('page_name', 'page_content')->where('status', '=', 1)->where('page_slug', '=', $page_slug)->first();
                    if($pageContent){
                        $apiResponse[] = [
                            'page_name'                 => $pageContent->page_name,
                            'page_content'              => $pageContent->page_content
                        ];
                        http_response_code(200);
                        $apiStatus          = TRUE;
                        $apiMessage         = 'Data Available !!!';
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    } else {
                        http_response_code(200);
                        $apiStatus          = FALSE;
                        $apiMessage         = 'Page not found !!!';
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    }
                } else {
                    http_response_code(400);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* static page */
    /* before login screen */
    /* authentication */
        /* signin with email */
            public function signin(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source', 'email', 'password', 'device_token', 'fcm_token'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $email                      = $requestData['email'];
                    $password                   = $requestData['password'];
                    $device_type                = $headerData['source'][0];
                    $device_token               = $requestData['device_token'];
                    $fcm_token                  = $requestData['fcm_token'];
                    $checkUser                  = User::where('email', '=', $email)->where('status', '=', 1)->first();
                    if($checkUser){
                        if(Hash::check($password, $checkUser->password)){
                            $objOfJwt           = new CreatorJwt();
                            $app_access_token   = $objOfJwt->GenerateToken($checkUser->id, $checkUser->email, $checkUser->phone);
                            $user_id            = $checkUser->id;
                            $fields             = [
                                'branch_id'             => $checkUser->branch_id,
                                'user_id'               => $user_id,
                                'device_type'           => $device_type,
                                'device_token'          => $device_token,
                                'fcm_token'             => $fcm_token,
                                'app_access_token'      => $app_access_token,
                            ];
                            $checkUserTokenExist            = UserDevice::where('user_id', '=', $user_id)->where('status', '=', 1)->where('device_type', '=', $device_type)->where('device_token', '=', $device_token)->first();
                            if(!$checkUserTokenExist){
                                UserDevice::insert($fields);
                            } else {
                                UserDevice::where('id','=',$checkUserTokenExist->id)->update($fields);
                            }
                            
                            $getBranch = Branch::select('name')->where('id', '=', $checkUser->branch_id)->first();
                            $apiResponse            = [
                                'user_id'               => $user_id,
                                'name'                  => $checkUser->first_name. ' ' .$checkUser->last_name,
                                'email'                 => $checkUser->email,
                                'phone'                 => $checkUser->phone,
                                'branch_name'           => (($getBranch)?$getBranch->name:''),
                                'branch_id'             => $checkUser->branch_id,
                                'device_type'           => $device_type,
                                'device_token'          => $device_token,
                                'fcm_token'             => $fcm_token,
                                'app_access_token'      => $app_access_token,
                            ];
                            /* user activity */
                                $activityData = [
                                    'user_email'        => $checkUser->email,
                                    'user_name'         => $checkUser->first_name. ' ' .$checkUser->last_name,
                                    'user_type'         => (($checkUser->role_id == 3)?'TELECALLER':'TEAM LEADER'),
                                    'ip_address'        => $request->ip(),
                                    'activity_type'     => 1,
                                    'activity_details'  => 'SignIn Successfully !!!',
                                    'platform_type'     => 'ANDROID',
                                ];
                                UserActivity::insert($activityData);
                            /* user activity */
                            
                            http_response_code(200);
                            $apiStatus          = TRUE;
                            $apiMessage         = 'SignIn Successfully !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        } else {
                            /* user activity */
                                $activityData = [
                                    'user_email'        => $requestData['email'],
                                    'user_name'         => $checkUser->first_name. ' ' .$checkUser->last_name,
                                    'user_type'         => (($checkUser->role_id == 3)?'TELECALLER':'TEAM LEADER'),
                                    'ip_address'        => $request->ip(),
                                    'activity_type'     => 0,
                                    'activity_details'  => 'Invalid Email Or Password !!!',
                                    'platform_type'     => 'ANDROID',
                                ];
                                UserActivity::insert($activityData);
                            /* user activity */
                            
                            http_response_code(200);
                            $apiStatus          = FALSE;
                            $apiMessage         = 'Invalid Email Or Password !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        }                   
                    } else {
                        /* user activity */
                            $activityData = [
                                'user_email'        => $requestData['email'],
                                'user_name'         => '',
                                'user_type'         => 'TELECALLER',
                                'ip_address'        => $request->ip(),
                                'activity_type'     => 0,
                                'activity_details'  => 'We Don\'t Recognize You !!!',
                                'platform_type'     => 'ANDROID',
                            ];
                            UserActivity::insert($activityData);
                        /* user activity */

                        http_response_code(200);
                        $apiStatus          = FALSE;
                        $apiMessage         = 'We Don\'t Recognize You !!!';
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    }
                } else {
                    http_response_code(400);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* signin with email */
        /* signin with mobile */
            public function signinWithMobile(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['phone'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $phone                      = $requestData['phone'];
                    $checkUser                  = User::where('phone', '=', $phone)->where('status', '=', 1)->first();
                    if($checkUser){
                        $remember_token  = rand(100000,999999);
                        User::where('id', '=', $checkUser->id)->update(['otp' => $remember_token]);
                        $mailData                   = [
                            'id'        => $checkUser->id,
                            'name'      => $checkUser->first_name.' '.$checkUser->last_name,
                            'content'   => $checkUser->first_name.' '.$checkUser->last_name,
                            'email'     => $checkUser->email,
                            'phone'     => $checkUser->phone,
                            'otp'       => $remember_token,
                            'logo'      => url('/public/') . '/' . Helper::getSettingValue('site_logo'),
                            'site_name' => Helper::getSettingValue('site_name'),
                        ];
                        
                        $subject                    = Helper::getSettingValue('site_name').' :: SignIn Validate OTP';
                        $message                    = view('mails.otp',$mailData);
                        $this->siteAuthService->sendMail($checkUser->email, $subject, $message);

                        /* email log save */
                            $postData2 = [
                                'name'                  => $checkUser->first_name.' '.$checkUser->last_name,
                                'email'                 => $checkUser->email,
                                'subject'               => $subject,
                                'message'               => $message
                            ];
                            EmailLog::insert($postData2);
                        /* email log save */
                        /* send sms */
                            // $name       = $checkUser->name;
                            // $message    = "Dear ".$name.", ".$remember_token." is your verification OTP for ProTime Manager at KEYLINE. Do not share this OTP with anyone for security reasons.";
                            // $mobileNo   = (($checkUser)?$checkUser->phone:'');
                            // $this->sendSMS($mobileNo,$message);
                        /* send sms */
                        $apiResponse                        = $mailData;
                        
                        http_response_code(200);
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                        $apiStatus          = TRUE;
                        $apiMessage         = 'OTP Sent To Email & Phone Validation !!!';
                    } else {
                        /* user activity */
                            $activityData = [
                                'user_email'        => $requestData['phone'],
                                'user_name'         => '',
                                'user_type'         => 'TELECALLER',
                                'ip_address'        => $request->ip(),
                                'activity_type'     => 0,
                                'activity_details'  => 'We Don\'t Recognize You !!!',
                                'platform_type'     => 'ANDROID',
                            ];
                            UserActivity::insert($activityData);
                        /* user activity */
                        
                        http_response_code(200);
                        $apiStatus          = FALSE;
                        $apiMessage         = 'We Don\'t Recognize You !!!';
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    }
                } else {
                    http_response_code(400);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* signin with mobile */
        /* signin validate mobile */
            public function signinValidateMobile(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['phone', 'otp', 'device_token'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $phone                      = $requestData['phone'];
                    $otp                        = $requestData['otp'];
                    $device_type                = $headerData['source'][0];
                    $device_token               = $requestData['device_token'];
                    $fcm_token                  = $requestData['fcm_token'];
                    $checkUser                  = User::where('phone', '=', $phone)->where('status', '=', 1)->first();
                    if($checkUser){
                        if($checkUser->otp == $otp){
                            $objOfJwt               = new CreatorJwt();
                            $app_access_token       = $objOfJwt->GenerateToken($checkUser->id, $checkUser->email, $checkUser->phone);
                            $user_id                = $checkUser->id;
                            User::where('id', '=', $user_id)->update(['otp' => 0]);
                            $fields     = [
                                'user_id'               => $user_id,
                                'branch_id'             => $checkUser->branch_id,
                                'device_type'           => $device_type,
                                'device_token'          => $device_token,
                                'fcm_token'             => $fcm_token,
                                'app_access_token'      => $app_access_token,
                            ];
                            $checkUserTokenExist            = UserDevice::where('user_id', '=', $user_id)->where('status', '=', 1)->where('device_type', '=', $device_type)->where('device_token', '=', $device_token)->first();
                            if(!$checkUserTokenExist){
                                UserDevice::insert($fields);
                            } else {
                                UserDevice::where('id','=',$checkUserTokenExist->id)->update($fields);
                            }
                            
                            $getBranch = Branch::select('name')->where('id', '=', $checkUser->branch_id)->first();
                            $apiResponse            = [
                                'user_id'               => $user_id,
                                'name'                  => $checkUser->first_name. ' ' .$checkUser->last_name,
                                'email'                 => $checkUser->email,
                                'phone'                 => $checkUser->phone,
                                'branch_name'           => (($getBranch)?$getBranch->name:''),
                                'branch_id'             => $checkUser->branch_id,
                                'device_type'           => $device_type,
                                'device_token'          => $device_token,
                                'fcm_token'             => $fcm_token,
                                'app_access_token'      => $app_access_token,
                            ];
                            /* user activity */
                                $activityData = [
                                    'user_email'        => $checkUser->email,
                                    'user_name'         => $checkUser->first_name. ' ' .$checkUser->last_name,
                                    'user_type'         => (($checkUser->role_id == 3)?'TELECALLER':'TEAM LEADER'),
                                    'ip_address'        => $request->ip(),
                                    'activity_type'     => 1,
                                    'activity_details'  => 'SignIn Successfully !!!',
                                    'platform_type'     => 'ANDROID',
                                ];
                                UserActivity::insert($activityData);
                            /* user activity */

                            http_response_code(200);
                            $apiStatus          = TRUE;
                            $apiMessage         = 'SignIn Successfully !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        } else {
                            /* user activity */
                                $activityData = [
                                    'user_email'        => $checkUser->email,
                                    'user_name'         => $checkUser->first_name. ' ' .$checkUser->last_name,
                                    'user_type'         => (($checkUser->role_id == 3)?'TELECALLER':'TEAM LEADER'),
                                    'ip_address'        => $request->ip(),
                                    'activity_type'     => 0,
                                    'activity_details'  => 'OTP Mismatched !!!',
                                    'platform_type'     => 'ANDROID',
                                ];
                                UserActivity::insert($activityData);
                            /* user activity */
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'OTP Mismatched !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        }
                    } else {
                        /* user activity */
                            $activityData = [
                                'user_email'        => $requestData['phone'],
                                'user_name'         => '',
                                'user_type'         => 'TELECALLER',
                                'ip_address'        => $request->ip(),
                                'activity_type'     => 0,
                                'activity_details'  => 'We Don\'t Recognize You !!!',
                                'platform_type'     => 'ANDROID',
                            ];
                            UserActivity::insert($activityData);
                        /* user activity */

                        http_response_code(200);
                        $apiStatus          = FALSE;
                        $apiMessage         = 'We Don\'t Recognize You !!!';
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    }
                } else {
                    http_response_code(400);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* signin validate mobile */
        /* resend otp */
            public function resendOtp(Request $request){
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source', 'id'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $id         = $requestData['id'];
                    $checkUser    = User::where('id', '=', $id)->first();
                    if($checkUser){
                        $remember_token = rand(100000,999999);
                        $postData = [
                            'otp'        => $remember_token
                        ];
                        User::where('id', '=', $id)->update($postData);
                        
                        $mailData                   = [
                            'id'        => $checkUser->id,
                            'name'      => $checkUser->first_name.' '.$checkUser->last_name,
                            'content'   => $checkUser->first_name.' '.$checkUser->last_name,
                            'email'     => $checkUser->email,
                            'phone'     => $checkUser->phone,
                            'otp'       => $remember_token,
                            'logo'      => url('/public/') . '/' . Helper::getSettingValue('site_logo'),
                            'site_name' => Helper::getSettingValue('site_name'),
                        ];
                        
                        $subject                    = Helper::getSettingValue('site_name').' :: SignIn Validate OTP';
                        $message                    = view('mails.otp',$mailData);
                        $this->siteAuthService->sendMail($checkUser->email, $subject, $message);

                        /* email log save */
                            $postData2 = [
                                'name'                  => $checkUser->first_name.' '.$checkUser->last_name,
                                'email'                 => $checkUser->email,
                                'subject'               => $subject,
                                'message'               => $message
                            ];
                            EmailLog::insert($postData2);
                        /* email log save */

                        $apiResponse                        = $mailData;
                        $apiStatus                          = TRUE;
                        http_response_code(200);
                        $apiMessage                         = 'OTP Resend !!!';
                        $apiExtraField                      = 'response_code';
                        $apiExtraData                       = http_response_code();
                    } else {
                        $apiStatus          = FALSE;
                        http_response_code(200);
                        $apiMessage         = 'User Not Found !!!';
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    }
                } else {
                    http_response_code(400);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* resend otp */
    /* authentication */
    /* forgot password */
        /* forgot password */
            public function forgotPassword(Request $request){
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source', 'email'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $checkUser = User::where('email', '=', $requestData['email'])->first();
                    if($checkUser){
                        $remember_token  = rand(100000,999999);
                        User::where('id', '=', $checkUser->id)->update(['otp' => $remember_token]);
                        $mailData                   = [
                            'id'        => $checkUser->id,
                            'name'      => $checkUser->first_name.' '.$checkUser->last_name,
                            'content'   => $checkUser->first_name.' '.$checkUser->last_name,
                            'email'     => $checkUser->email,
                            'phone'     => $checkUser->phone,
                            'otp'       => $remember_token,
                            'logo'      => url('/public/') . '/' . Helper::getSettingValue('site_logo'),
                            'site_name' => Helper::getSettingValue('site_name'),
                        ];
                        
                        $subject                    = Helper::getSettingValue('site_name').' :: Forgot Password Validate OTP';
                        $message                    = view('mails.otp',$mailData);
                        $this->siteAuthService->sendMail($checkUser->email, $subject, $message);

                        /* email log save */
                            $postData2 = [
                                'name'                  => $checkUser->first_name.' '.$checkUser->last_name,
                                'email'                 => $checkUser->email,
                                'subject'               => $subject,
                                'message'               => $message
                            ];
                            EmailLog::insert($postData2);
                        /* email log save */

                        $apiResponse                        = $mailData;
                        $apiStatus                          = TRUE;
                        http_response_code(200);
                        $apiMessage                         = 'OTP Sent To Email Validation !!!';
                        $apiExtraField                      = 'response_code';
                        $apiExtraData                       = http_response_code();
                    } else {
                        $apiStatus          = FALSE;
                        http_response_code(200);
                        $apiMessage         = 'Email Not Registered With Us !!!';
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    }
                } else {
                    http_response_code(200);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* forgot password */
        /* validate otp */
            public function validateOtp(Request $request){
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source', 'id', 'otp'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $getUser = User::where('id', '=', $requestData['id'])->first();
                    if($getUser){
                        $remember_token  = $getUser->otp;
                        if($remember_token == $requestData['otp']){
                            User::where('id', '=', $requestData['id'])->update(['otp' => 0]);
                            
                            $apiResponse        = [
                                'id'    => $getUser->id,
                                'email' => $getUser->email
                            ];
                            $apiStatus                          = TRUE;
                            http_response_code(200);
                            $apiMessage                         = 'OTP Validated Successfully !!!';
                            $apiExtraField                      = 'response_code';
                            $apiExtraData                       = http_response_code();
                        } else {
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'OTP Mismatched !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        }
                    } else {
                        $apiStatus          = FALSE;
                        http_response_code(200);
                        $apiMessage         = 'User Not Found !!!';
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    }
                } else {
                    http_response_code(200);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* validate otp */
        /* reset password */
            public function resetPassword(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source', 'id', 'password', 'confirm_password'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $checkUser = User::where('id', '=', $requestData['id'])->first();
                    if($checkUser){
                        if($requestData['password'] == $requestData['confirm_password']){
                            User::where('id', '=', $requestData['id'])->update(['password' => Hash::make($requestData['password'])]);
                            $mailData                   = [
                                'id'        => $checkUser->id,
                                'name'      => $checkUser->first_name.' '.$checkUser->last_name,
                                'email'     => $checkUser->email,
                                'phone'     => $checkUser->phone,
                                'logo'      => url('/public/') . '/' . Helper::getSettingValue('site_logo'),
                                'site_name' => Helper::getSettingValue('site_name'),
                            ];
                            
                            $subject                    = Helper::getSettingValue('site_name').' :: Reset Password';
                            $message                    = view('mails.reset-password',$mailData);
                            $this->siteAuthService->sendMail($checkUser->email, $subject, $message);

                            /* email log save */
                                $postData2 = [
                                    'name'                  => $checkUser->first_name.' '.$checkUser->last_name,
                                    'email'                 => $checkUser->email,
                                    'subject'               => $subject,
                                    'message'               => $message
                                ];
                                EmailLog::insert($postData2);
                            /* email log save */
                            
                            $apiStatus                          = TRUE;
                            http_response_code(200);
                            $apiMessage                         = 'Password Reset Successfully !!!';
                            $apiExtraField                      = 'response_code';
                            $apiExtraData                       = http_response_code();
                        } else {
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'Password & Confirm Password Not Matched !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData                       = http_response_code();
                        }
                    } else {
                        $apiStatus          = FALSE;
                        http_response_code(200);
                        $apiMessage         = 'User Not Found !!!';
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    }
                } else {
                    http_response_code(200);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* reset password */
    /* forgot password */
    /* after login screen */
        /* signout */
            public function signout(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = [];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $app_access_token           = $headerData['authorization'][0];
                    $checkUserTokenExist        = UserDevice::where('app_access_token', '=', $app_access_token)->where('status', '=', 1)->first();
                    if($checkUserTokenExist){
                        /* user activity */
                            $getTokenValue              = $this->tokenAuth($app_access_token);
                            $uId                        = $getTokenValue['data'][1];
                            $getUser                    = User::where('id', '=', $uId)->first();
                            $activityData = [
                                'user_email'        => (($getUser)?$getUser->email:''),
                                'user_name'         => (($getUser)?$getUser->first_name.' '.$getUser->first_namelast_name:''),
                                'user_type'         => (($getUser)?(($getUser->role_id == 3)?'TELECALLER':'TEAM LEADER'):''),
                                'ip_address'        => $request->ip(),
                                'activity_type'     => 2,
                                'activity_details'  => 'Signout Successfully !!!',
                                'platform_type'     => 'ANDROID',
                            ];
                            UserActivity::insert($activityData);
                        /* user activity */
                        UserDevice::where('app_access_token', '=', $app_access_token)->delete();
                        
                        http_response_code(200);
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                        $apiStatus          = TRUE;
                        $apiMessage         = 'Signout Successfully !!!';
                    } else {
                        http_response_code(200);
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                        $apiStatus          = FALSE;
                        $apiMessage         = 'Something Went Wrong !!!';
                    }               
                } else {
                    http_response_code(400);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* signout */
        /* get profile */
            public function getProfile(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $app_access_token           = $headerData['authorization'][0];
                    $getTokenValue              = $this->tokenAuth($app_access_token);
                    if($getTokenValue['status']){
                        $uId        = $getTokenValue['data'][1];
                        $expiry     = date('d/m/Y H:i:s', $getTokenValue['data'][4]);
                        $getUser    = User::where('id', '=', $uId)->first();
                        if($getUser){
                            $getBranch = Branch::select('name')->where('id', '=', $getUser->branch_id)->first();
                            $checkUserTokenExist        = UserDevice::where('app_access_token', '=', $app_access_token)->where('status', '=', 1)->first();
                            $profileData            = [
                                'user_id'               => $uId,
                                'name'                  => $getUser->first_name. ' ' .$getUser->last_name,
                                'first_name'            => $getUser->first_name,
                                'last_name'             => $getUser->last_name,
                                'email'                 => $getUser->email,
                                'phone'                 => $getUser->phone,
                                'branch_name'           => (($getBranch)?$getBranch->name:''),
                                'branch_id'             => $getUser->branch_id,
                                'created_at'            => date_format(date_create($getUser->created_at), "M d, Y h:i A"),
                                'profile_image'         => (($getUser->profile_image != '')?url('/public/').'/'.$getUser->profile_image:env('NO_IMAGE_AVATAR')),
                                'last_login'            => (($checkUserTokenExist)?date_format(date_create($checkUserTokenExist->created_at), "M d, Y h:i a"):date('Y-m-d H:i:s')),
                            ];
                            
                            $apiStatus          = TRUE;
                            $apiMessage         = 'Data Available !!!';
                            $apiResponse        = $profileData;
                        } else {
                            $apiStatus          = FALSE;
                            $apiMessage         = 'User Not Found !!!';
                        }
                    } else {
                        $apiStatus                      = FALSE;
                        $apiMessage                     = $getTokenValue['data'];
                    }                                               
                } else {
                    $apiStatus          = FALSE;
                    $apiMessage         = 'Unauthenticate Request !!!';
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* get profile */
        /* upload profile image */
            public function uploadProfileImage(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['profile_image'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $app_access_token           = $headerData['authorization'][0];
                    $checkUserTokenExist        = UserDevice::where('app_access_token', '=', $app_access_token)->where('status', '=', 1)->first();
                    if($checkUserTokenExist){
                        $getTokenValue              = $this->tokenAuth($app_access_token);
                        $uId                        = $getTokenValue['data'][1];
                        $getUser    = User::where('id', '=', $uId)->first();
                        if($getUser){
                            $profile_image  = $requestData['profile_image'];
                            if(!empty($profile_image)){
                                $profile_image      = $profile_image;
                                $upload_type        = $profile_image[0]['type'];
                                if($upload_type == 'image/jpeg' || $upload_type == 'image/jpg' || $upload_type == 'image/png' || $upload_type == 'image/gif'){
                                    $upload_base64      = $profile_image[0]['base64'];
                                    $img                = $upload_base64;
                                    $proof_type         = $profile_image[0]['type'];
                                    if($proof_type == 'image/png'){
                                        $extn = 'png';
                                    } elseif($proof_type == 'image/jpg'){
                                        $extn = 'jpg';
                                    } elseif($proof_type == 'image/jpeg'){
                                        $extn = 'jpeg';
                                    } elseif($proof_type == 'image/gif'){
                                        $extn = 'gif';
                                    } else {
                                        $extn = 'png';
                                    }
                                    $data               = base64_decode($img);
                                    $fileName           = uniqid() . '.' . $extn;
                                    $file               = 'public/uploads/user/' . $fileName;
                                    $success            = file_put_contents($file, $data);
                                    $profile_image      = 'uploads/user/' . $fileName;
                                } else {
                                    $apiStatus          = FALSE;
                                    http_response_code(404);
                                    $apiMessage         = 'Please Upload Image !!!';
                                    $apiExtraField      = 'response_code';
                                    $apiExtraData       = http_response_code();
                                }
                            } else {
                                $profile_image = $getUser->profile_image;
                            }
                            $postData = [
                                            'profile_image'         => $profile_image
                                        ];
                            User::where('id', '=', $uId)->update($postData);
                            $apiStatus                  = TRUE;
                            $apiMessage                 = 'Profile Image Uploaded Successfully !!!';
                            http_response_code(200);
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        } else {
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'User Not Found !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        }
                    } else {
                        $apiStatus          = FALSE;
                        $apiMessage         = 'Something Went Wrong !!!';
                        http_response_code(200);
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    }                                               
                } else {
                    http_response_code(200);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* upload profile image */
        /* change password */
            public function changePassword(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $old_password               = $requestData['old_password'];
                    $new_password               = $requestData['new_password'];
                    $confirm_password           = $requestData['confirm_password'];
                    
                    $app_access_token           = $headerData['authorization'][0];
                    $checkUserTokenExist        = UserDevice::where('app_access_token', '=', $app_access_token)->where('status', '=', 1)->first();
                    if($checkUserTokenExist){
                        $getTokenValue              = $this->tokenAuth($app_access_token);
                        $uId                        = $getTokenValue['data'][1];
                        $checkUser                  = User::where('id', '=', $uId)->first();
                        
                        if($checkUser){
                            if(Hash::check($old_password, $checkUser->password)){
                                if($new_password == $confirm_password){
                                    if($new_password != $old_password){
                                        $fields = [
                                            'password'                  => Hash::make($new_password)
                                        ];
                                        User::where('id', '=', $uId)->update($fields);
                                        $mailData                   = [
                                            'id'        => $checkUser->id,
                                            'name'      => $checkUser->first_name.' '.$checkUser->last_name,
                                            'email'     => $checkUser->email,
                                            'phone'     => $checkUser->phone,
                                            'logo'      => url('/public/') . '/' . Helper::getSettingValue('site_logo'),
                                            'site_name' => Helper::getSettingValue('site_name'),
                                        ];
                                        
                                        $subject                    = Helper::getSettingValue('site_name').' :: Reset Password';
                                        $message                    = view('mails.reset-password',$mailData);
                                        $this->siteAuthService->sendMail($checkUser->email, $subject, $message);

                                        /* email log save */
                                            $postData2 = [
                                                'name'                  => $checkUser->first_name.' '.$checkUser->last_name,
                                                'email'                 => $checkUser->email,
                                                'subject'               => $subject,
                                                'message'               => $message
                                            ];
                                            EmailLog::insert($postData2);
                                        /* email log save */

                                        $apiStatus          = TRUE;
                                        http_response_code(200);
                                        $apiMessage         = 'Password Updated Successfully !!!';
                                        $apiExtraField      = 'response_code';
                                        $apiExtraData       = http_response_code();
                                    } else {
                                        $apiStatus          = FALSE;
                                        http_response_code(200);
                                        $apiMessage         = 'Current & New Password Should Not Be Same !!!';
                                        $apiExtraField      = 'response_code';
                                        $apiExtraData       = http_response_code();
                                    }
                                } else {
                                    $apiStatus          = FALSE;
                                    http_response_code(200);
                                    $apiMessage         = 'New & Confirm Password Doesn\'t Matched !!!';
                                    $apiExtraField      = 'response_code';
                                    $apiExtraData       = http_response_code();
                                }
                            } else {
                                $apiStatus          = FALSE;
                                http_response_code(200);
                                $apiMessage         = 'Current Password Doesn\'t Matched !!!';
                                $apiExtraField      = 'response_code';
                                $apiExtraData       = http_response_code();
                            }
                        } else {
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'User Not Found !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        }
                    } else {
                        http_response_code(200);
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                        $apiStatus          = FALSE;
                        $apiMessage         = 'Something Went Wrong !!!';
                    }                                               
                } else {
                    http_response_code(200);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* change password */
        /* update profile */
            public function updateProfile(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source', 'first_name', 'last_name'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $app_access_token           = $headerData['authorization'][0];
                    $checkUserTokenExist        = UserDevice::where('app_access_token', '=', $app_access_token)->where('status', '=', 1)->first();
                    if($checkUserTokenExist){
                        $getTokenValue              = $this->tokenAuth($app_access_token);
                        $uId                        = $getTokenValue['data'][1];
                        $getUser                    = User::where('id', '=', $uId)->first();
                        if($getUser){
                            $postData = [
                                        'first_name'                => $requestData['first_name'],
                                        'last_name'                 => $requestData['last_name'],
                                    ];
                            User::where('id', '=', $uId)->update($postData);
                            
                            $apiStatus          = TRUE;
                            http_response_code(200);
                            $apiMessage         = 'Profile Updated Successfully !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        } else {
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'User Not Found !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        }
                    } else {
                        http_response_code(200);
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                        $apiStatus          = FALSE;
                        $apiMessage         = 'Something Went Wrong !!!';
                    }                                               
                } else {
                    http_response_code(200);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);           
            }
        /* update profile */
        /* delete account */
            public function deleteAccount(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $app_access_token           = $headerData['authorization'][0];
                    $checkUserTokenExist        = UserDevice::where('app_access_token', '=', $app_access_token)->where('status', '=', 1)->first();
                    if($checkUserTokenExist){
                        $getTokenValue              = $this->tokenAuth($app_access_token);
                        $uId                        = $getTokenValue['data'][1];
                        $getUser                    = User::where('id', '=', $uId)->first();
                        if($getUser){
                            $getRole     = Role::select('role_name')->where('id', '=', $getUser->role_id)->first();
                            $fields = [
                                'user_type'                 => (($getRole)?$getRole->role_name:''),
                                'entity_name'               => $getUser->first_name.' '.$getUser->last_name,
                                'email'                     => $getUser->email,
                                'is_email_verify'           => 1,
                                'country_code'              => $getUser->country_code,
                                'phone'                     => $getUser->phone,
                                'is_phone_verify'           => 1,
                            ];
                            DeleteAccountRequest::insert($fields);

                            $apiStatus          = TRUE;
                            http_response_code(200);
                            $apiMessage         = 'Account Delete Requests Submitted Successfully !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        } else {
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'User Not Found !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        }
                    } else {
                        http_response_code(200);
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                        $apiStatus          = FALSE;
                        $apiMessage         = 'Something Went Wrong !!!';
                    }               
                } else {
                    http_response_code(200);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData); 
            }
        /* delete account */
    /* after login screen */
    /* after login lead */
        /* get lead status */
            public function getLeadStatusList(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $app_access_token           = $headerData['authorization'][0];
                    $getTokenValue              = $this->tokenAuth($app_access_token);
                    if($getTokenValue['status']){
                        $uId        = $getTokenValue['data'][1];
                        $expiry     = date('d/m/Y H:i:s', $getTokenValue['data'][4]);
                        $getUser    = User::where('id', '=', $uId)->first();
                        if($getUser){
                            $getParentStats = LeadStatus::select('id', 'name', 'background_color', 'font_color')->where('status', '=', 1)->where('parent_id', '=', 0)->orderBy('rank', 'ASC')->get();
                            if($getParentStats){
                                foreach($getParentStats as $getParentStat){
                                    $child_status = [];
                                    $getChildStats = LeadStatus::select('id', 'name', 'background_color', 'font_color')->where('status', '=', 1)->where('parent_id', '=', $getParentStat->id)->orderBy('rank', 'ASC')->get();
                                    if($getChildStats){
                                        foreach($getChildStats as $getChildStat){
                                            $child_status[]            = [
                                                'child_status_id'                  => $getChildStat->id,
                                                'child_status_name'                => $getChildStat->name,
                                                'child_status_background_color'    => $getChildStat->background_color,
                                                'child_status_font_color'          => $getChildStat->font_color,
                                            ];
                                        }
                                    }

                                    $apiResponse[]            = [
                                        'parent_status_id'                  => $getParentStat->id,
                                        'parent_status_name'                => $getParentStat->name,
                                        'parent_status_background_color'    => $getParentStat->background_color,
                                        'parent_status_font_color'          => $getParentStat->font_color,
                                        'child_status'                      => $child_status,
                                    ];
                                }
                            }

                            $apiStatus          = TRUE;
                            http_response_code(200);
                            $apiMessage         = 'Data Available !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        } else {
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'User Not Found !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        }
                    } else {
                        http_response_code(200);
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                        $apiStatus          = FALSE;
                        $apiMessage         = 'Something Went Wrong !!!';
                    }                                               
                } else {
                    http_response_code(200);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* get lead status */
        /* get followup options */
            public function getFollowupOption(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $app_access_token           = $headerData['authorization'][0];
                    $getTokenValue              = $this->tokenAuth($app_access_token);
                    if($getTokenValue['status']){
                        $uId        = $getTokenValue['data'][1];
                        $expiry     = date('d/m/Y H:i:s', $getTokenValue['data'][4]);
                        $getUser    = User::where('id', '=', $uId)->first();
                        if($getUser){
                            $call_status    = [];
                            $call_purpose   = [];
                            $feedback_tags  = [];
                            $moods          = [];

                            // call status
                            $getChildStats = LeadStatus::select('id', 'name', 'background_color', 'font_color')->where('status', '=', 1)->where('parent_id', '>', 0)->orderBy('rank', 'ASC')->get();
                            if($getChildStats){
                                foreach($getChildStats as $getChildStat){
                                    $call_status[]            = [
                                        'id'                  => $getChildStat->id,
                                        'name'                => $getChildStat->name,
                                        'background_color'    => $getChildStat->background_color,
                                        'font_color'          => $getChildStat->font_color,
                                    ];
                                }
                            }

                            // call purposes
                            $getPurposes = Purpose::select('id', 'name')->where('status', '=', 1)->orderBy('id', 'ASC')->get();
                            if($getPurposes){
                                foreach($getPurposes as $purpose){
                                    $call_purpose[]            = [
                                        'id'                  => $purpose->id,
                                        'name'                => $purpose->name
                                    ];
                                }
                            }

                            // feedback tags
                            $tags = FeedbackTag::select('id', 'name')->where('status', '=', 1)->orderBy('id', 'ASC')->get();
                            if($tags){
                                foreach($tags as $tag){
                                    $feedback_tags[]            = [
                                        'id'                  => $tag->id,
                                        'name'                => $tag->name
                                    ];
                                }
                            }

                            // moods
                            $getMoods = Mood::select('id', 'name', 'emoji', 'color')->where('status', '=', 1)->orderBy('id', 'ASC')->get();
                            if($getMoods){
                                foreach($getMoods as $tag){
                                    $moods[]            = [
                                        'id'                  => $tag->id,
                                        'name'                => $tag->name,
                                        'emoji'               => $tag->emoji,
                                        'color'               => $tag->color,
                                    ];
                                }
                            }

                            $apiResponse = [
                                'call_status'       => $call_status,
                                'call_purpose'      => $call_purpose,
                                'feedback_tags'     => $feedback_tags,
                                'moods'             => $moods,
                            ];

                            $apiStatus          = TRUE;
                            http_response_code(200);
                            $apiMessage         = 'Data Available !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        } else {
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'User Not Found !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        }
                    } else {
                        http_response_code(200);
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                        $apiStatus          = FALSE;
                        $apiMessage         = 'Something Went Wrong !!!';
                    }                                               
                } else {
                    http_response_code(200);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* get followup options */
        /* lead list */
            public function leadList(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source', 'page_no', 'per_page'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $app_access_token           = $headerData['authorization'][0];
                    $getTokenValue              = $this->tokenAuth($app_access_token);
                    if($getTokenValue['status']){
                        $uId                    = $getTokenValue['data'][1];
                        $expiry                 = date('d/m/Y H:i:s', $getTokenValue['data'][4]);
                        $getUser                = User::where('id', '=', $uId)->first();

                        $page_no                = $requestData['page_no'];
                        $per_page               = $requestData['per_page'];
                        $parent_status          = $requestData['parent_status'];
                        $child_status           = $requestData['child_status'];
                        if($getUser){
                            $branch_id                      = $getUser->branch_id;
                            $assigned_telecaller_id         = $uId;
                            $limit                          = $per_page; // per page elements
                            if($page_no == 1){
                                $offset         = 0;
                            } else {
                                $offset         = (($limit * $page_no) - $limit); // ((15 * 3) - 15)
                            }

                            if($parent_status == '' && $child_status == ''){
                                $leadNos                  = BranchLead::select('lead_sl_no', 'parent_status_id', 'child_status_id', 'next_followup_date', 'next_followup_time', 'created_at', 'campaign_type_id', 'campaign_id')
                                                        ->where('status', '=', 1)
                                                        ->where('branch_id', '=', $branch_id)
                                                        ->where('assigned_telecaller_id', '=', $assigned_telecaller_id)
                                                        ->orderBy('lead_sl_no', 'ASC')
                                                        ->offset($offset)
                                                        ->limit($limit)
                                                        ->get();
                            } elseif($parent_status != '' && $child_status == ''){
                                $leadNos                  = BranchLead::select('lead_sl_no', 'parent_status_id', 'child_status_id', 'next_followup_date', 'next_followup_time', 'created_at', 'campaign_type_id', 'campaign_id')
                                                        ->where('status', '=', 1)
                                                        ->where('branch_id', '=', $branch_id)
                                                        ->where('assigned_telecaller_id', '=', $assigned_telecaller_id)
                                                        ->where('parent_status_id', '=', $parent_status)
                                                        ->orderBy('lead_sl_no', 'ASC')
                                                        ->offset($offset)
                                                        ->limit($limit)
                                                        ->get();
                            } elseif($parent_status == '' && $child_status != ''){
                                $leadNos                  = BranchLead::select('lead_sl_no', 'parent_status_id', 'child_status_id', 'next_followup_date', 'next_followup_time', 'created_at', 'campaign_type_id', 'campaign_id')
                                                        ->where('status', '=', 1)
                                                        ->where('branch_id', '=', $branch_id)
                                                        ->where('assigned_telecaller_id', '=', $assigned_telecaller_id)
                                                        ->where('child_status_id', '=', $child_status)
                                                        ->orderBy('lead_sl_no', 'ASC')
                                                        ->offset($offset)
                                                        ->limit($limit)
                                                        ->get();
                            } elseif($parent_status != '' && $child_status != ''){
                                $leadNos                  = BranchLead::select('lead_sl_no', 'parent_status_id', 'child_status_id', 'next_followup_date', 'next_followup_time', 'created_at', 'campaign_type_id', 'campaign_id')
                                                        ->where('status', '=', 1)
                                                        ->where('branch_id', '=', $branch_id)
                                                        ->where('assigned_telecaller_id', '=', $assigned_telecaller_id)
                                                        ->where('parent_status_id', '=', $parent_status)
                                                        ->where('child_status_id', '=', $child_status)
                                                        ->orderBy('lead_sl_no', 'ASC')
                                                        ->offset($offset)
                                                        ->limit($limit)
                                                        ->get();
                            }

                            if($leadNos){
                                foreach($leadNos as $leadNo){
                                    $activity_count = LeadActivity::where('lead_sl_no', '=', $leadNo->lead_sl_no)->count();
                                    $last_activity  = LeadActivity::where('lead_sl_no', '=', $leadNo->lead_sl_no)->orderBy('id', 'DESC')->first();
                                    $next_schedule  = '';
                                    if($activity_count > 0){
                                        if($leadNo->next_followup_date != '' && $leadNo->next_followup_time != ''){
                                            $next_schedule = date_format(date_create($leadNo->next_followup_date), "M d Y") . ', ' . date_format(date_create($leadNo->next_followup_time), "h:i a");
                                        }
                                    }
                                    $getParentStatus    = LeadStatus::select('name')->where('id', '=', $leadNo->parent_status_id)->first();
                                    $getChildStatus     = LeadStatus::select('name')->where('id', '=', $leadNo->child_status_id)->first();
                                    $getMasterLead      = MasterLead::select('lead_no')->where('sl_no', '=', $leadNo->lead_sl_no)->first();
                                    $getCampaignType    = CampaignType::select('name')->where('id', '=', $leadNo->campaign_type_id)->first();
                                    $getCampaign        = Campaign::select('name')->where('id', '=', $leadNo->campaign_id)->first();

                                    $apiResponse[]      = [
                                        'sl_no'                 => $leadNo->lead_sl_no,
                                        'lead_no'               => (($getMasterLead)?$getMasterLead->lead_no:''),
                                        'company_name'          => $this->getHeaderValueByID($leadNo->lead_sl_no, 1),
                                        'contact_person_name'   => $this->getHeaderValueByID($leadNo->lead_sl_no, 2),
                                        'email'                 => $this->getHeaderValueByID($leadNo->lead_sl_no, 5),
                                        'phone_no'              => $this->getHeaderValueByID($leadNo->lead_sl_no, 4),
                                        'whatsapp_no'           => $this->getHeaderValueByID($leadNo->lead_sl_no, 14),
                                        'parent_status_id'      => (($leadNo->parent_status_id > 0)?$leadNo->parent_status_id:12),
                                        'parent_status_name'    => (($getParentStatus)?$getParentStatus->name:'New'),
                                        'child_status_id'       => (($leadNo->child_status_id > 0)?$leadNo->child_status_id:13),
                                        'child_status_name'     => (($getChildStatus)?$getChildStatus->name:'New'),
                                        'campaign_type_name'    => (($getCampaignType)?$getCampaignType->name:''),
                                        'campaign_name'         => (($getCampaign)?$getCampaign->name:''),
                                        'last_call'             => (($activity_count > 0)?date_format(date_create($last_activity->created_at), "M d Y, h:i a"):''),
                                        'next_schedule'         => $next_schedule,
                                        'activity_count'        => $activity_count,
                                        'telecaller_name'       => $getUser->first_name . ' ' . $getUser->last_name,
                                    ];
                                }
                            }
                            
                            // Helper::pr($apiResponse);

                            $apiStatus          = TRUE;
                            http_response_code(200);
                            $apiMessage         = 'Data Available !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        } else {
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'User Not Found !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        }
                    } else {
                        http_response_code(200);
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                        $apiStatus          = FALSE;
                        $apiMessage         = 'Something Went Wrong !!!';
                    }                                               
                } else {
                    http_response_code(200);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* lead list */
        /* lead details */
            public function leadDetail(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source', 'sl_no'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $app_access_token           = $headerData['authorization'][0];
                    $getTokenValue              = $this->tokenAuth($app_access_token);
                    if($getTokenValue['status']){
                        $uId                    = $getTokenValue['data'][1];
                        $expiry                 = date('d/m/Y H:i:s', $getTokenValue['data'][4]);
                        $getUser                = User::where('id', '=', $uId)->first();

                        $sl_no                  = $requestData['sl_no'];
                        if($getUser){
                            $leadNo                  = BranchLead::select('lead_sl_no', 'parent_status_id', 'child_status_id', 'next_followup_date', 'next_followup_time', 'created_at', 'campaign_type_id', 'campaign_id')
                                                        ->where('status', '=', 1)
                                                        ->where('lead_sl_no', '=', $sl_no)
                                                        ->first();

                            if($leadNo){
                                $activity_count = LeadActivity::where('lead_sl_no', '=', $sl_no)->count();
                                $last_activity  = LeadActivity::where('lead_sl_no', '=', $sl_no)->orderBy('id', 'DESC')->first();
                                $next_schedule  = '';
                                if($activity_count > 0){
                                    if($leadNo->next_followup_date != '' && $leadNo->next_followup_time != ''){
                                        $next_schedule = date_format(date_create($leadNo->next_followup_date), "M d, Y") . ', ' . date_format(date_create($leadNo->next_followup_time), "h:i A");
                                    }
                                }
                                $getParentStatus    = LeadStatus::select('name')->where('id', '=', $leadNo->parent_status_id)->first();
                                $getChildStatus     = LeadStatus::select('name')->where('id', '=', $leadNo->child_status_id)->first();
                                $getMasterLead      = MasterLead::select('lead_no')->where('sl_no', '=', $sl_no)->first();
                                $getCampaignType    = CampaignType::select('name')->where('id', '=', $leadNo->campaign_type_id)->first();
                                $getCampaign        = Campaign::select('name')->where('id', '=', $leadNo->campaign_id)->first();

                                $activities         = [];
                                $getActivities      = LeadActivity::where('lead_sl_no', '=', $sl_no)->get();
                                if($getActivities){
                                    foreach($getActivities as $getActivity){
                                        $getPurpose         = Purpose::select('name')->where('id', '=', $getActivity->purpose_id)->first();
                                        $getParentStatus    = LeadStatus::select('name')->where('id', '=', $getActivity->parent_status_id)->first();
                                        $getChildStatus     = LeadStatus::select('name')->where('id', '=', $getActivity->child_status_id)->first();
                                        $getTelecaller      = User::select('first_name', 'last_name')->where('id', '=', $getActivity->assigned_telecaller_id)->first();
                                        $getMood            = Mood::select('name', 'emoji', 'color')->where('id', '=', $getActivity->mood)->first();

                                        $next_schedule_activity  = '';
                                        if($getActivity->next_followup_date != '' && $getActivity->next_followup_time != ''){
                                            $next_schedule_activity = date_format(date_create($getActivity->next_followup_date), "M d, Y") . ', ' . date_format(date_create($getActivity->next_followup_time), "h:i A");
                                        }

                                        $feedback_tags = [];
                                        if($getActivity->feedback_tag_ids != ''){
                                            $feedback_tag_ids  = json_decode($getActivity->feedback_tag_ids);
                                            if(!empty($feedback_tag_ids)){
                                                for($f=0;$f<count($feedback_tag_ids);$f++){
                                                    $getTag             = FeedbackTag::select('name')->where('id', '=', $feedback_tag_ids[$f])->first();
                                                    $feedback_tags[]    = (($getTag)?$getTag->name:'');
                                                }
                                            }
                                        }

                                        $activities[]         = [
                                            'purpose_name'          => (($getPurpose)?$getPurpose->name:''),
                                            'comment'               => $getActivity->comment,
                                            'note'                  => $getActivity->note,
                                            'telecaller_name'       => (($getTelecaller)?$getTelecaller->first_name . ' ' . $getTelecaller->last_name:''),
                                            'parent_status_id'      => $getActivity->parent_status_id,
                                            'parent_status_name'    => (($getParentStatus)?$getParentStatus->name:''),
                                            'child_status_id'       => $getActivity->child_status_id,
                                            'child_status_name'     => (($getChildStatus)?$getChildStatus->name:''),
                                            'activity_timestamp'    => date_format(date_create($getActivity->note), "M d, Y h:i A"),
                                            'next_schedule'         => $next_schedule_activity,
                                            'feedback_tags'         => $feedback_tags,
                                            'mood_id'               => $getActivity->mood,
                                            'mood_name'             => (($getMood)?$getMood->name:''),
                                            'mood_emoji'            => (($getMood)?$getMood->emoji:''),
                                            'mood_color'            => (($getMood)?$getMood->color:''),
                                        ];
                                    }
                                }

                                $apiResponse        = [
                                    'sl_no'                 => $sl_no,
                                    'lead_no'               => (($getMasterLead)?$getMasterLead->lead_no:''),
                                    'company_name'          => $this->getHeaderValueByID($sl_no, 1),
                                    'contact_person_name'   => $this->getHeaderValueByID($sl_no, 2),
                                    'email'                 => $this->getHeaderValueByID($sl_no, 5),
                                    'phone_no'              => $this->getHeaderValueByID($sl_no, 4),
                                    'whatsapp_no'           => $this->getHeaderValueByID($sl_no, 14),
                                    'parent_status_id'      => (($leadNo->parent_status_id > 0)?$leadNo->parent_status_id:12),
                                    'parent_status_name'    => (($getParentStatus)?$getParentStatus->name:'New'),
                                    'child_status_id'       => (($leadNo->child_status_id > 0)?$leadNo->child_status_id:13),
                                    'child_status_name'     => (($getChildStatus)?$getChildStatus->name:'New'),
                                    'campaign_type_name'    => (($getCampaignType)?$getCampaignType->name:''),
                                    'campaign_name'         => (($getCampaign)?$getCampaign->name:''),
                                    'last_call'             => (($activity_count > 0)?date_format(date_create($last_activity->created_at), "M d Y, h:i a"):''),
                                    'next_schedule'         => $next_schedule,
                                    'telecaller_name'       => $getUser->first_name . ' ' . $getUser->last_name,
                                    'activity_count'        => $activity_count,
                                    'activities'            => $activities,
                                ];
                            }

                            $apiStatus          = TRUE;
                            http_response_code(200);
                            $apiMessage         = 'Data Available !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        } else {
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'User Not Found !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        }
                    } else {
                        http_response_code(200);
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                        $apiStatus          = FALSE;
                        $apiMessage         = 'Something Went Wrong !!!';
                    }                                               
                } else {
                    http_response_code(200);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* lead details */
        /* update lead status */
            public function updateLeadStatus(Request $request)
            {
                $apiStatus          = TRUE;
                $apiMessage         = '';
                $apiResponse        = [];
                $apiExtraField      = '';
                $apiExtraData       = '';
                $requestData        = $request->all();
                $requiredFields     = ['key', 'source', 'sl_no', 'child_status_id', 'next_schedule_date', 'next_schedule_time', 'purpose_id', 'mood_id', 'comment'];
                $headerData         = $request->header();
                if (!$this->validateArray($requiredFields, $requestData)){
                    $apiStatus          = FALSE;
                    $apiMessage         = 'All Data Are Not Present !!!';
                }
                if($headerData['key'][0] == env('PROJECT_KEY')){
                    $app_access_token           = $headerData['authorization'][0];
                    $getTokenValue              = $this->tokenAuth($app_access_token);
                    if($getTokenValue['status']){
                        $uId                    = $getTokenValue['data'][1];
                        $expiry                 = date('d/m/Y H:i:s', $getTokenValue['data'][4]);
                        $getUser                = User::where('id', '=', $uId)->first();

                        $sl_no                              = $requestData['sl_no'];
                        $child_status_id                    = $requestData['child_status_id'];
                        $comment                            = $requestData['comment'];
                        $next_schedule_date                 = $requestData['next_schedule_date'];
                        $next_schedule_time                 = $requestData['next_schedule_time'];
                        $purpose_id                         = $requestData['purpose_id'];
                        $mood_id                            = $requestData['mood_id'];
                        $feedback_tags                      = $requestData['feedback_tags'];
                        $note                               = $requestData['note'];
                        
                        if($getUser){
                            $leadNo                  = BranchLead::select('master_lead_id', 'lead_sl_no', 'upload_id', 'campaign_type_id', 'campaign_id', 'branch_id')
                                                        ->where('status', '=', 1)
                                                        ->where('lead_sl_no', '=', $sl_no)
                                                        ->first();

                            if($leadNo){
                                $getParentStatus    = LeadStatus::select('parent_id')->where('id', '=', $child_status_id)->first();
                                $fields = [
                                    'upload_id'                 => $leadNo->upload_id,
                                    'master_lead_id'            => $leadNo->master_lead_id,
                                    'lead_sl_no'                => $leadNo->lead_sl_no,
                                    'branch_id'                 => $leadNo->branch_id,
                                    'campaign_type_id'          => $leadNo->campaign_type_id,
                                    'campaign_id'               => $leadNo->campaign_id,
                                    'assigned_telecaller_id'    => $uId,
                                    'parent_status_id'          => (($getParentStatus)?$getParentStatus->parent_id:0),
                                    'child_status_id'           => $child_status_id,
                                    'comment'                   => $comment,
                                    'mood'                      => $mood_id,
                                    'purpose_id'                => $purpose_id,
                                    'feedback_tag_ids'          => ((!empty($feedback_tags))?json_encode($feedback_tags):null),
                                    'note'                      => $note,
                                    'next_followup_date'        => (($next_schedule_date != '')?date_format(date_create($next_schedule_date), "Y-m-d"):''),
                                    'next_schedule_time'        => (($next_schedule_time != '')?date_format(date_create($next_schedule_time), "H:i:s"):''),
                                    'created_by'                => $uId,
                                    'updated_by'                => $uId,
                                ];
                                // Helper::pr($fields,0);
                                LeadActivity::insert($fields);

                                $fields2 = [
                                    'assigned_telecaller_id'    => $uId,
                                    'parent_status_id'          => (($getParentStatus)?$getParentStatus->parent_id:0),
                                    'child_status_id'           => $child_status_id,
                                    'next_followup_date'        => (($next_schedule_date != '')?date_format(date_create($next_schedule_date), "Y-m-d"):''),
                                    'next_followup_time'        => (($next_schedule_time != '')?date_format(date_create($next_schedule_time), "H:i:s"):''),
                                ];
                                // Helper::pr($fields2);die;
                                BranchLead::where('lead_sl_no', '=', $sl_no)->update($fields);

                                $apiStatus          = TRUE;
                                http_response_code(200);
                                $apiMessage         = 'Lead Status Updated Successfully !!!';
                                $apiExtraField      = 'response_code';
                                $apiExtraData       = http_response_code();
                            } else {
                                $apiStatus          = FALSE;
                                http_response_code(200);
                                $apiMessage         = 'Lead Not Found !!!';
                                $apiExtraField      = 'response_code';
                                $apiExtraData       = http_response_code();
                            }
                        } else {
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'User Not Found !!!';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        }
                    } else {
                        http_response_code(200);
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                        $apiStatus          = FALSE;
                        $apiMessage         = 'Something Went Wrong !!!';
                    }                                               
                } else {
                    http_response_code(200);
                    $apiStatus          = FALSE;
                    $apiMessage         = $this->getResponseCode(http_response_code());
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
                $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
            }
        /* update lead status */
        /* update lead info */
        
        /* update lead info */
    /* after login lead */
    public function getHeaderValueByID($sl_no, $header_id){
        $header_value = '';
        $getHeaderValue = MasterLead::select('header_value')->where('sl_no', '=', $sl_no)->where('header_id', '=', $header_id)->first();
        if($getHeaderValue){
            $header_value = $getHeaderValue->header_value;
        } else {
            $header_value = '';
        }
        return $header_value;
    }
    /*
    Get http response code
    Author : Subhomoy
    */
    private function getResponseCode($code = NULL){
        if ($code !== NULL) {
            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Unauthenticated Request !!!'; break;
                case 401: $text = 'Token Not Found !!!'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Token Has Expired !!!'; break;
                case 404: $text = 'User Not Found !!!'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'All Data Are Not Present !!!'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
            }
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . $code . ' ' . $text);
            $GLOBALS['http_response_code'] = $code;
        } else {
            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
            $text = '';
        }
        return $text;
    }
    /*
    Generate JWT tokens for authentication
    Author : Subhomoy
    */
    private static function generateToken($userId, $email, $phone){
        $token      = array(
            'id'                => $userId,
            'email'             => $email,
            'phone'             => $phone,
            'exp'               => time() + (30 * 24 * 60 * 60) // 30 days
        );
        // pr($token);
        return JWT::encode($token, TOKEN_SECRET, 'HS256');
    }
    /*
    Check Authentication
    Author : Subhomoy
    */
    private function tokenAuth($appAccessToken){
        $headers = apache_request_headers();
        if (isset($appAccessToken) && !empty($appAccessToken)) :
            $userdata = $this->matchToken($appAccessToken);
            // pr($userdata);
            if ($userdata['status']) :
                $checkToken =  UserDevice::where('user_id', '=', $userdata['data']->id)->where('app_access_token', '=', $appAccessToken)->first();
                // echo $this->db->last_query();
                // pr($userdata);
                if (!empty($checkToken)) :
                    if ($userdata['data']->exp && $userdata['data']->exp > time()) :
                        $tokenStatus = array(TRUE, $userdata['data']->id, $userdata['data']->email, $userdata['data']->phone, $userdata['data']->exp);
                    else :
                        $tokenStatus = array(FALSE, 'Token Has Expired 1 !!!');
                    endif;
                else :
                    $tokenStatus = array(FALSE, 'Token Has Expired 2 !!!');
                endif;
            else :
                $tokenStatus = array(FALSE, 'Token Not Found !!!');
            endif;
        else :
            $tokenStatus = array(FALSE, 'Token Not Found In Request !!!');
        endif;
        if ($tokenStatus[0]) :
            $this->userId           = $tokenStatus[1];
            $this->userEmail        = $tokenStatus[2];
            $this->userMobile       = $tokenStatus[3];
            $this->userExpiry       = $tokenStatus[4];
            // pr($tokenStatus);
            return array('status' => TRUE, 'data' => $tokenStatus);
        else :
            return array('status' => FALSE, 'data' => $tokenStatus[1]);
            // $this->response_to_json(FALSE, $tokenStatus[1]);
        endif;
    }
    /*
    Match JWT token with user token saved in database
    Author : Subhomoy
    */
    private static function matchToken($token){
        // try{
        //     // $decoded    = JWT::decode($token, TOKEN_SECRET, 'HS256');
        //     $decoded    = JWT::decode($token, new Key(TOKEN_SECRET, 'HS256'));
        //     // pr($decoded);
        // } catch (\Exception $e) {
        //     //echo 'Caught exception: ',  $e->getMessage(), "\n";
        //     return array('status' => FALSE, 'data' => '');
        // }
        
        // return array('status' => TRUE, 'data' => $decoded);


        try{
            $key = "1234567890qwertyuiopmnbvcxzasdfghjkl";
            $decoded = JWT::decode($token, $key, array('HS256'));
            // $decodedData = (array) $decoded;
        } catch (\Exception $e) {
            //echo 'Caught exception: ',  $e->getMessage(), "\n";
            return array('status' => FALSE, 'data' => '');
        }
        return array('status' => TRUE, 'data' => $decoded);
    }
}
