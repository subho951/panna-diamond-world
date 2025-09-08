<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\SiteAuthService;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

use App\Models\EmailLog;
use App\Models\User;
use App\Models\Branch;
use App\Models\LeadActivity;
use App\Models\GeneralSetting;
use App\Models\UserActivity;
use App\Helpers\Helper;
use App\Models\BranchLead;
use Carbon\Carbon;
use Session;
use DB;

class AuthController extends Controller
{
    protected $siteAuthService;
    protected $data;
    function __construct()
    {
        $this->siteAuthService = new SiteAuthService();
    }
    // Shows the page (like a form or a button)
    public function showEmailTestPage()
    {
        return view('test-email'); // Your Blade view
    }

    // Handles the email sending
    public function testEmailFunction(Request $request)
    {
        $to = 'subhomoysamanta1989@gmail.com';
        $subject = "Test Email Subject On " . now();
        $message = "Test Email Body On " . now();

        try {
            $this->sendMail($to, $subject, $message);
            return redirect('/test-email-function')->with('success_message', 'Test Email Sent Successfully');
        } catch (\Exception $e) {
            return redirect('/test-email-function')->with('error_message', 'Failed to send email: ' . $e->getMessage());
        }
    }
    /* authentication */
        public function showLogin()
        {
            return view('maincontents.signin');
        }
        public function login(Request $request)
        {
            if ($request->isMethod('post'))
            {
                $postData = $request->all();                
                // Get reCAPTCHA token from form POST data
                $recaptchaResponse = $postData['g-recaptcha-response'];

                // Google reCAPTCHA verification URL
                $verifyURL = 'https://www.google.com/recaptcha/api/siteverify';

                // Your Google reCAPTCHA secret key 
                $secretKey = '6Le9GMArAAAAAO1-3Pjo4OHxPb9ySll1MaRQe78l';
                                                       
                // Prepare the POST request
                $data = array(
                    'secret' => $secretKey,
                    'response' => $recaptchaResponse,                       
                );

                // Initiate cURL
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $verifyURL);
                curl_setopt($ch, option: option: CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);

                // Decode JSON response
                $responseData = json_decode($response);

                // Check if reCAPTCHA validation was successful
                if ($responseData->success && $responseData->score >= 0.5)
                {

                    $authData = $request->validate([
                                'email'     => ['required', 'email'],
                                'password'  => ['required'],
                            ]);
        
                    // Add extra conditions to the authData array
                    $authData['status']  = 1;
                    // $authData['role_id'] = 1;
        
                    if (Auth::attempt($authData)) {
                        $request->session()->regenerate();
        
                        // Store selected user info in session array
                        $user = Auth::user();
                        session([
                                'user_data'     => [
                                    'user_id'       => $user->id,
                                    'name'          => $user->first_name . ' ' . $user->last_name,
                                    'email'         => $user->email,
                                    'role_id'       => $user->role_id,
                                    'is_user_login' => 1,
                                ]
                        ]);
                        /* user activity */
                            $activityData = [
                                'user_email'        => $user->email,
                                'user_name'         => $user->first_name . ' ' . $user->last_name,
                                'user_type'         => 'ADMIN',
                                'ip_address'        => $request->ip(),
                                'activity_type'     => 1,
                                'activity_details'  => 'Login Success',
                                'platform_type'     => 'WEB',
                            ];
                            UserActivity::insert($activityData);
                        /* user activity */
                        return redirect('dashboard/')->with('success_message', 'Sign-in successfull');
                    }
                    /* user activity */
                        $activityData = [
                            'user_email'        => $authData['email'],
                            'user_name'         => 'Master Admin',
                            'user_type'         => 'ADMIN',
                            'ip_address'        => $request->ip(),
                            'activity_type'     => 0,
                            'activity_details'  => 'Invalid Email Or Password',
                            'platform_type'     => 'WEB',
                        ];
                        UserActivity::insert($activityData);
                    /* user activity */
                    return redirect()->back()->with('error_message', 'Invalid credentials or access denied.');
                
                }
                else 
                {                        
                    return redirect()->back()->with('error_message', 'reCAPTCHA v3 validation failed. Please try again.');                        
                } 

            }



        }
        public function logout(Request $request)
        {
            $user_email                             = Auth::user()->email;
            $user_name                              = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            /* user activity */
                $activityData = [
                    'user_email'        => $user_email,
                    'user_name'         => $user_name,
                    'user_type'         => 'ADMIN',
                    'ip_address'        => $request->ip(),
                    'activity_type'     => 2,
                    'activity_details'  => 'You Are Successfully Logged Out',
                    'platform_type'     => 'WEB',
                ];
                UserActivity::insert($activityData);
            /* user activity */
            $request->session()->forget(['user_data']);
            Auth::guard('user')->logout();
            return redirect('/')->with('success_message', 'You Are Successfully Logged Out');
        }
    /* authentication */
    /* forgot password */
        public function forgotPassword(Request $request){
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                            'email' => 'required|email|max:255',
                        ];
                if($this->validate($request, $rules)){
                    $checkEmail                   = User::where('email','=',strip_tags($postData['email']))->get();
                    if(count($checkEmail) > 0){
                        $row     =  User::where('email', '=', strip_tags($postData['email']))->first();
                        $otp     =  rand(100000,999999);
                        $fields  =  [
                                        'remember_token' => $otp
                                    ];
                        User::where('id', '=', $row->id)->update($fields);
                        $to = $row->email;
                        $subject = "Reset Password";
                        $message = "Your OTP For Reset Password is :" . $otp;
                        $this->sendMail($postData['email'],$subject,$message);
                        return redirect('validate-otp/'.Helper::encoded($row->id))->with('success_message', 'OTP Sent To Your Registered Email');
                    }else{
                        return redirect()->back()->with('error_message', 'Please Enter a Registered Email');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required');
                }
            }
            $data                           = [];
            $title                          = 'Forgot Password';
            $page_name                      = 'forgot-password';
            $data = $this->siteAuthService->admin_before_login_layout($title, $page_name, $data);
            return view('maincontents.' . $page_name, $data);
        }
        public function validateOtp(Request $request, $id){
            $id                             = Helper::decoded($id);
            $data['id']                     = $id;
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                            'otp1'     => 'required|max:1',
                            'otp2'     => 'required|max:1',
                            'otp3'     => 'required|max:1',
                            'otp4'     => 'required|max:1',
                            'otp5'     => 'required|max:1',
                            'otp6'     => 'required|max:1',
                        ];
                if($this->validate($request, $rules)){
                    $otp1   = strip_tags($postData['otp1']);
                    $otp2   = strip_tags($postData['otp2']);
                    $otp3   = strip_tags($postData['otp3']);
                    $otp4   = strip_tags($postData['otp4']);
                    $otp5   = strip_tags($postData['otp5']);
                    $otp6   = strip_tags($postData['otp6']);
                    $newotp    = ($otp1.$otp2.$otp3.$otp4.$otp5.$otp6);
                    $checkUser = User::where('id', '=', $id)->first();
                    if($checkUser){
                        $otp = $checkUser->remember_token;
                        if($otp == $newotp){
                            $postData = [
                                            'remember_token'        => '',
                                        ];
                            User::where('id', '=', $checkUser->id)->update($postData);
                            return redirect('reset-password/'.Helper::encoded($checkUser->id))->with('success_message', 'OTP Verified. Now Reset Your Password');
                        } else {
                            return redirect()->back()->with('error_message', 'OTP Mismatched');
                        }
                    } else {
                        return redirect()->back()->with('error_message', 'We Don\'t Recognize You');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required');
                }
            }
            $title                          = 'Verify OTP';
            $page_name                      = 'validate-otp';
            $data = $this->siteAuthService->admin_before_login_layout($title, $page_name, $data);
            return view('maincontents.' . $page_name, $data);
        }
        public function resendOtp(Request $request, $id){
            $id                             = Helper::decoded($id);
            $checkEmail                     = User::where('id','=',$id)->get();
            if(count($checkEmail) > 0){
                $row     =  User::where('id','=',$id)->first();
                $otp     =  rand(100000,999999);
                $fields  =  [
                                'remember_token' => $otp
                            ];
                User::where('id', '=', $row->id)->update($fields);
                $to         = $row->email;
                $subject    = "Reset Password";
                $message    = "Your OTP For Reset Password is :" . $otp;
                // $this->sendMail($postData['email'],$subject,$message);
                return redirect('validate-otp/'.Helper::encoded($row->id))->with('success_message', 'New OTP Resend To Your Registered Email');
            }else{
                return redirect()->back()->with('error_message', 'Please Enter a Registered Email');
            }
        }
        public function resetPassword(Request $request ,$id){
            $ID         = Helper::decoded($id);
            $adminData  = User::where('id', '=', $ID)->first();
            if($request->isMethod('post')){
                $rules = [
                            'password'              => 'required|max:15|min:8',
                            'confirm-password'      => 'required|max:15|min:8'
                        ];
                if($this->validate($request, $rules)){
                    $postData = $request->all();
                    if($postData['password'] != $postData['confirm-password'] ){
                        return redirect()->back()->with('error_message', 'Password Doesn\'t match');
                    } else {
                        if(Hash::check($postData['password'], $adminData->password)){
                            return redirect()->back()->with('error_message', 'You can\'t use previously used password');
                        } else {
                            $fields = [
                                            'password'        => Hash::make(strip_tags($postData['password'])),
                                        ];
                            User::where('id', '=', $ID)->update($fields);
                            return redirect('/')->with('success_message', 'Password Reset Successfully. Please Sign In');
                        }
                    }
                }
            }
            $data['user']                   = User::where('id','=',$ID)->first();
            $title                          = 'Reset Password';
            $page_name                      = 'reset-password';
            $data = $this->siteAuthService->admin_before_login_layout($title, $page_name, $data);
            return view('maincontents.' . $page_name, $data);
        }
    /* forgot password */
    /* dashboard */
        public function dashboard(Request $request)
        {
            $data = [];
            
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

            $title                                  = 'Dashboard';
            $page_name                              = 'dashboard';
            $data = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
            return view('maincontents.' . $page_name, $data)->with(['branchWiseTelecallerActivity' => $branchWiseTelecallerActivity ]);
        }
        public function getMonthYearList($startDate) {
            $start = new DateTime($startDate);
            $end = new DateTime(); // Current date
            $end->modify('last day of this month'); // End of the current month
            $interval = new DateInterval('P1M'); // Interval of 1 month
            $period = new DatePeriod($start, $interval, $end->add($interval)); // Period from start to end

            $monthYearList = [];
            foreach ($period as $date) {
                // $monthYearList[] = $date->format('Y-m'); // Format as "YYYY-MM"
                $monthYearList[] = [
                    'month' => $date->format('m'), // Full month name
                    'month_name' => $date->format('M'), // Full month name
                    'year' => $date->format('Y'),  // Year
                ];
            }

            return $monthYearList;
        }
        public function userAllActivity(Request $request){
            $data['rows']                                               = DB::table('user_website_activities')
                                                                                ->join('users', 'user_website_activities.user_id', '=', 'users.id')
                                                                                ->select('user_website_activities.*', 'users.profile_image')
                                                                                ->orderBy('user_website_activities.id', 'DESC')
                                                                                ->get();
            $title                                                      = 'User All Activity';
            $page_name                                                  = 'user-all-activity';
            $data = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
            return view('maincontents.' . $page_name, $data);
        }
    /* dashboard */
    /* email logs */
        public function emailLogs(){
            $data                           = [];
            $title                          = 'Email Logs';
            $page_name                      = 'email-logs';
            $data = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
            return view('maincontents.' . $page_name, $data);
        }
        public function emailLogsDetails(Request $request,$id ){
            $id = Helper::decoded($id);
            $data['logData']                = EmailLog::where('id', '=', $id)->orderBy('id', 'DESC')->first();
            $title                          = 'Email Logs Details';
            $page_name                      = 'email-logs-info';
            $data = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
            return view('maincontents.' . $page_name, $data);
        }
    /* email logs */
    /* login logs */
        public function loginLogs(){
            $data                           = [];
            $title                          = 'Login Logs';
            $page_name                      = 'login-logs';
            $data = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
            return view('maincontents.' . $page_name, $data);
        }
    /* login logs */
    /* User Activity Logs */
        public function userActivityLogs(){
            $data                           = [];
            $title                          = 'User Activity Logs';
            $page_name                      = 'user-activity-logs';
            $data = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
            return view('maincontents.' . $page_name, $data);
        }
    /* User Activity Logs */
    /* image gallery */
        public function imageGallery(Request $request){
            $title                          = 'Image Gallery';
            $page_name                      = 'image-gallery';
            $data['rows']                   = ImageGallery::where('status', '!=', 3)->orderBy('id', 'DESC')->paginate(12);
            if($request->isMethod('post')){
                $image_array            = $request->file('image_file');
                if(!empty($image_array)){
                    $uploadedFile       = $this->commonFileArrayUpload('public/uploads/gallery/', $image_array, 'image');
                    if(!empty($uploadedFile)){
                        $images    = $uploadedFile;
                    } else {
                        $images    = [];
                    }
                }
                // Helper::pr($images);
                if(!empty($images)){
                    for($i=0;$i<count($images);$i++){
                        $image_link = env('UPLOADS_URL').'gallery/'.$images[$i];
                        $fields2 = [
                            'image_file'            => $images[$i],
                            'image_link'            => $image_link
                        ];
                        ImageGallery::insert($fields2);
                    }
                }
                return redirect("admin/image-gallery")->with('success_message', 'Images Uploaded Successfully');
            }
            $data = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
            return view('maincontents.' . $page_name, $data);
        }
    /* image gallery */
    /* common delete image */
        public function commonDeleteImage($pageLink, $tableName, $fieldName, $primaryField, $refId){
            $postData = [$fieldName => ''];
            $pageLink = Helper::decoded($pageLink);
            DB::table($tableName)
                    ->where($primaryField, '=', $refId)
                    ->update($postData);
            return redirect()->to($pageLink)->with('success_message', 'Image Deleted Successfully');
        }
    /* common delete image */
    /* settings */
        public function settings(Request $request){
            $uId                            = session('user_data')['user_id'];
            $data['setting']                = GeneralSetting::where('id', '=', 1)->first();
            $data['user']                   = User::where('id', '=', $uId)->first();
            $title                          = 'Settings';
            $page_name                      = 'settings';
            $data = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
            return view('maincontents.' . $page_name, $data);
        }
        public function profile_settings(Request $request){
            $uId        = session('user_data')['user_id'];
            $row        = User::where('id', '=', $uId)->first();
            $postData   = $request->all();
            $rules      = [
                'first_name'        => 'required',
                'last_name'         => 'required',
                'phone'             => 'required',
                'email'             => 'required',
            ];
            if($this->validate($request, $rules)){
                /* profile image */
                $imageFile      = $request->file('profile_image');
                if($imageFile != ''){
                    $imageName      = $imageFile->getClientOriginalName();
                    $uploadedFile   = $this->upload_single_file('profile_image', $imageName, '', 'image');
                    if($uploadedFile['status']){
                        $profile_image = 'uploads/' . $uploadedFile['newFilename'];
                    } else {
                        return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                    }
                } else {
                    $profile_image = $row->profile_image;
                }
                /* profile image */
                $fields = [
                    'first_name'            => strip_tags($postData['first_name']),
                    'last_name'             => strip_tags($postData['last_name']),
                    'phone'                 => strip_tags($postData['phone']),
                    'email'                 => strip_tags($postData['email']),
                    'profile_image'         => $profile_image
                ];
                // Helper::pr($fields);
                User::where('id', '=', $uId)->update($fields);
                return redirect()->back()->with('success_message', 'Profile Settings Updated Successfully');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required');
            }
        }
        public function general_settings(Request $request){
            $postData   = $request->all();
            $rules      = [
                'site_name'            => 'required',
                'site_phone'           => 'required',
                'site_mail'            => 'required',
                'system_email'         => 'required',
            ];
            if($this->validate($request, $rules)){
                unset($postData['_token']);
                /* site logo */
                    $imageFile      = $request->file('site_logo');
                    if($imageFile != ''){
                        $imageName      = $imageFile->getClientOriginalName();
                        $uploadedFile   = $this->upload_single_file('site_logo', $imageName, '', 'image');
                        if($uploadedFile['status']){
                            $site_logo = 'uploads/' . $uploadedFile['newFilename'];
                        } else {
                            return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                        }
                    } else {
                        $site_logo = Helper::getSettingValue('site_logo');
                    }
                /* site logo */
                /* site footer logo */
                    $imageFile      = $request->file('site_footer_logo');
                    if($imageFile != ''){
                        $imageName      = $imageFile->getClientOriginalName();
                        $uploadedFile   = $this->upload_single_file('site_footer_logo', $imageName, '', 'image');
                        if($uploadedFile['status']){
                            $site_footer_logo = 'uploads/' . $uploadedFile['newFilename'];
                        } else {
                            return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                        }
                    } else {
                        $site_footer_logo = Helper::getSettingValue('site_footer_logo');
                    }
                /* site footer logo */
                /* site favicon */
                    $imageFile      = $request->file('site_favicon');
                    if($imageFile != ''){
                        $imageName      = $imageFile->getClientOriginalName();
                        $uploadedFile   = $this->upload_single_file('site_favicon', $imageName, '', 'image');
                        if($uploadedFile['status']){
                            $site_favicon = 'uploads/' . $uploadedFile['newFilename'];
                        } else {
                            return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                        }
                    } else {
                        $site_favicon = Helper::getSettingValue('site_favicon');
                    }
                /* site favicon */
                if(!empty($postData)){
                    foreach($postData as $key => $value){
                        $fields = [
                            'value'            => strip_tags($postData[$key])
                        ];
                        GeneralSetting::where('key', '=', $key)->where('is_active', '=', 1)->update($fields);
                    }
                }
                $fields2 = [
                    'value'            => $site_logo
                ];
                GeneralSetting::where('key', '=', 'site_logo')->where('is_active', '=', 1)->update($fields2);
                $fields3 = [
                    'value'            => $site_footer_logo
                ];
                GeneralSetting::where('key', '=', 'site_footer_logo')->where('is_active', '=', 1)->update($fields3);
                $fields4 = [
                    'value'            => $site_favicon
                ];
                GeneralSetting::where('key', '=', 'site_favicon')->where('is_active', '=', 1)->update($fields4);
                return redirect()->back()->with('success_message', 'General Settings Updated Successfully');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required');
            }
        }
        public function change_password(Request $request){
            $uId        = session('user_data')['user_id'];
            $adminData  = User::where('id', '=', $uId)->first();
            $postData   = $request->all();
            $rules      = [
                'old_password'            => 'required|max:15|min:8',
                'new_password'            => 'required|max:15|min:8',
                'confirm_password'        => 'required|max:15|min:8',
            ];
            if($this->validate($request, $rules)){
                $old_password       = strip_tags($postData['old_password']);
                $new_password       = strip_tags($postData['new_password']);
                $confirm_password   = strip_tags($postData['confirm_password']);
                if(Hash::check($old_password, $adminData->password)){
                    if($new_password == $confirm_password){
                        $fields = [
                            'password'            => Hash::make($new_password)
                        ];
                        User::where('id', '=', $uId)->update($fields);
                        return redirect()->back()->with('success_message', 'Password Changed Successfully');
                    } else {
                        return redirect()->back()->with('error_message', 'New & Confirm Password Does Not Matched');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'Current Password Is Incorrect');
                }
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required');
            }
        }
        public function email_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'from_email'            => 'required',
                'from_name'             => 'required',
                'smtp_host'             => 'required',
                'smtp_username'         => 'required',
                'smtp_password'         => 'required',
                'smtp_port'             => 'required',
            ];
            if($this->validate($request, $rules)){
                unset($postData['_token']);
                if(!empty($postData)){
                    foreach($postData as $key => $value){
                        $fields = [
                            'value'            => strip_tags($postData[$key])
                        ];
                        GeneralSetting::where('key', '=', $key)->where('is_active', '=', 1)->update($fields);
                    }
                }
                return redirect()->back()->with('success_message', 'Email Settings Updated Successfully');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required');
            }
        }
        public function testEmail(){
            $to = 'kulavisudip@gmail.com';
            $subject = "Test Email Subject On " . date('Y-m-d H:i:s');
            $message = "Test Email Body On " . date('Y-m-d H:i:s');
            $this->sendMail($to,$subject,$message);
            return redirect('/settings/')->with('success_message', 'Test Email Sent Successfully');
        }
        public function email_template(Request $request){
            $postData = $request->all();
            $rules = [
                'email_template_user_signup'            => 'required',
                'email_template_forgot_password'        => 'required',
                'email_template_change_password'        => 'required',
                'email_template_failed_login'           => 'required',
                'email_template_contactus'              => 'required',
            ];
            if($this->validate($request, $rules)){
                unset($postData['_token']);
                if(!empty($postData)){
                    foreach($postData as $key => $value){
                        $fields = [
                            'value'            => strip_tags($postData[$key])
                        ];
                        GeneralSetting::where('key', '=', $key)->where('is_active', '=', 1)->update($fields);
                    }
                }
                return redirect()->back()->with('success_message', 'Email Templates Updated Successfully');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required');
            }
        }
        public function sms_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'sms_authentication_key'            => 'required',
                'sms_sender_id'                     => 'required',
                'sms_base_url'                      => 'required',
            ];
            if($this->validate($request, $rules)){
                unset($postData['_token']);
                if(!empty($postData)){
                    foreach($postData as $key => $value){
                        $fields = [
                            'value'            => strip_tags($postData[$key])
                        ];
                        GeneralSetting::where('key', '=', $key)->where('is_active', '=', 1)->update($fields);
                    }
                }
                return redirect()->back()->with('success_message', 'SMS Settings Updated Successfully');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required');
            }
        }
        public function footer_settings(Request $request){
            $postData = $request->all();
            // Helper::pr($postData);
            $rules = [
                'footer_text'                       => 'required',
            ];
            if($this->validate($request, $rules)){
                $footer_link_name_array = $postData['footer_link_name'];
                $footer_link_name       = [];
                if(!empty($footer_link_name_array)){
                    for($f=0;$f<count($footer_link_name_array);$f++){
                        if($footer_link_name_array[$f]){
                            $footer_link_name[]       = $footer_link_name_array[$f];
                        }
                    }
                }
                $footer_link_array = $postData['footer_link'];
                $footer_link       = [];
                if(!empty($footer_link_array)){
                    for($f=0;$f<count($footer_link_array);$f++){
                        if($footer_link_array[$f]){
                            $footer_link[]       = $footer_link_array[$f];
                        }
                    }
                }
                $footer_link_name_array2 = $postData['footer_link_name2'];
                $footer_link_name2       = [];
                if(!empty($footer_link_name_array2)){
                    for($f=0;$f<count($footer_link_name_array2);$f++){
                        if($footer_link_name_array2[$f]){
                            $footer_link_name2[]       = $footer_link_name_array2[$f];
                        }
                    }
                }
                $footer_link_array2 = $postData['footer_link2'];
                $footer_link2       = [];
                if(!empty($footer_link_array2)){
                    for($f=0;$f<count($footer_link_array2);$f++){
                        if($footer_link_array2[$f]){
                            $footer_link2[]       = $footer_link_array2[$f];
                        }
                    }
                }
                $footer_link_name_array3 = $postData['footer_link_name3'];
                $footer_link_name3       = [];
                if(!empty($footer_link_name_array3)){
                    for($f=0;$f<count($footer_link_name_array3);$f++){
                        if($footer_link_name_array3[$f]){
                            $footer_link_name3[]       = $footer_link_name_array3[$f];
                        }
                    }
                }
                $footer_link_array3 = $postData['footer_link3'];
                $footer_link3       = [];
                if(!empty($footer_link_array3)){
                    for($f=0;$f<count($footer_link_array3);$f++){
                        if($footer_link_array3[$f]){
                            $footer_link3[]       = $footer_link_array3[$f];
                        }
                    }
                }
                // Helper::pr($postData);
                $footer_data = [];
                if(!empty($postData)){
                    $counter = 0;
                    foreach($postData as $key => $value){
                        if($counter > 0){
                            if($key == 'footer_text'){
                                $field = [
                                    'value'            => strip_tags($postData[$key])
                                ];
                                $footer_data['footer_text'] = strip_tags($postData[$key]);
                            }
                            if($key == 'copyright_statement'){
                                $field = [
                                    'value'            => strip_tags($postData[$key])
                                ];
                                $footer_data['copyright_statement'] = strip_tags($postData[$key]);
                            }

                            if($key == 'footer_link_name'){
                                $field = [
                                    'value'            => json_encode($footer_link_name)
                                ];
                                $footer_data['column1']['link_name'] = json_encode($footer_link_name);
                            }
                            if($key == 'footer_link'){
                                $field = [
                                    'value'            => json_encode($footer_link)
                                ];
                                $footer_data['column1']['link_url'] = json_encode($footer_link);
                            }

                            if($key == 'footer_link_name2'){
                                $field = [
                                    'value'            => json_encode($footer_link_name2)
                                ];
                                $footer_data['column2']['link_name'] = json_encode($footer_link_name2);
                            }
                            if($key == 'footer_link2'){
                                $field = [
                                    'value'            => json_encode($footer_link2)
                                ];
                                $footer_data['column2']['link_url'] = json_encode($footer_link2);
                            }

                            if($key == 'footer_link_name3'){
                                $field = [
                                    'value'            => json_encode($footer_link_name3)
                                ];
                                $footer_data['column3']['link_name'] = json_encode($footer_link_name3);
                            }
                            if($key == 'footer_link3'){
                                $field = [
                                    'value'            => json_encode($footer_link3)
                                ];
                                $footer_data['column3']['link_url'] = json_encode($footer_link3);
                            }
                            // Helper::pr($field,0);
                            GeneralSetting::where('key', '=', $key)->where('is_active', '=', 1)->update($field);
                        }
                        $counter++;
                    }
                    $column1 = [];
                    $column2 = [];
                    $column3 = [];
                    $final_footer_data = [];
                    if($footer_data){
                        $column1_link_name  = $footer_link_name;
                        $column1_link_url   = $footer_link;
                        if(!empty($column1_link_name)){
                            for($k=0;$k<count($column1_link_name);$k++){
                                $column1[] = [
                                    'link_name' => $column1_link_name[$k],
                                    'link_url'  => ((array_key_exists($k,$column1_link_url))?$column1_link_url[$k]:''),
                                ];
                            }
                        }

                        $column2_link_name  = $footer_link_name2;
                        $column2_link_url   = $footer_link2;
                        if(!empty($column2_link_name)){
                            for($k=0;$k<count($column2_link_name);$k++){
                                $column2[] = [
                                    'link_name' => $column2_link_name[$k],
                                    'link_url'  => ((array_key_exists($k,$column2_link_url))?$column2_link_url[$k]:''),
                                ];
                            }
                        }

                        $column3_link_name  = $footer_link_name3;
                        $column3_link_url   = $footer_link3;
                        if(!empty($column3_link_name)){
                            for($k=0;$k<count($column3_link_name);$k++){
                                $column3[] = [
                                    'link_name' => $column3_link_name[$k],
                                    'link_url'  => ((array_key_exists($k,$column3_link_url))?$column3_link_url[$k]:''),
                                ];
                            }
                        }
                    }
                    $final_footer_data = [
                        'footer_text'           => $footer_data['footer_text'],
                        'copyright_statement'   => $footer_data['copyright_statement'],
                        'address'               => Helper::getSettingValue('address'),
                        'phone'                 => Helper::getSettingValue('site_phone') . '/' . Helper::getSettingValue('site_phone2'),
                        'email'                 => Helper::getSettingValue('site_mail'),
                        'logo'                  => url('/public/') . '/' . Helper::getSettingValue('site_footer_logo'),
                        'facebook_profile'      => Helper::getSettingValue('facebook_profile'),
                        'instagram_profile'     => Helper::getSettingValue('instagram_profile'),
                        'linkedin_profile'      => Helper::getSettingValue('linkedin_profile'),
                        'signature'             => 'Designed and developed by ITIFFY Consultants',
                        'column1'               => $column1,
                        'column2'               => $column2,
                        'column3'               => $column3,
                    ];
                    // Helper::pr($footer_data,0);
                    // Helper::pr($final_footer_data);
                    // die;
                    GeneralSetting::where('key', '=', 'footer_data')->where('is_active', '=', 1)->update(['value' => json_encode($final_footer_data)]);
                }
                return redirect()->back()->with('success_message', 'Footer Settings Updated Successfully');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required');
            }
        }
        public function seo_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'meta_title'            => 'required',
                'meta_description'      => 'required',
                'meta_keywords'         => 'required'
            ];
            if($this->validate($request, $rules)){
                unset($postData['_token']);
                if(!empty($postData)){
                    foreach($postData as $key => $value){
                        $fields = [
                            'value'            => strip_tags($postData[$key])
                        ];
                        GeneralSetting::where('key', '=', $key)->where('is_active', '=', 1)->update($fields);
                    }
                }
                return redirect()->back()->with('success_message', 'SEO Settings Updated Successfully');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required');
            }
        }
        public function payment_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'stripe_payment_type'       => 'required',
                'stripe_sandbox_sk'         => 'required',
                'stripe_sandbox_pk'         => 'required',
                'stripe_live_sk'            => 'required',
                'stripe_live_pk'            => 'required',
            ];
            if($this->validate($request, $rules)){
                unset($postData['_token']);
                if(!empty($postData)){
                    foreach($postData as $key => $value){
                        $fields = [
                            'value'            => strip_tags($postData[$key])
                        ];
                        GeneralSetting::where('key', '=', $key)->where('is_active', '=', 1)->update($fields);
                    }
                }
                return redirect()->back()->with('success_message', 'Stripe Payment Settings Updated Successfully');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required');
            }
        }
        public function color_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'color_theme'               => 'required',
                'color_button'              => 'required',
                'color_title'               => 'required',
                'color_panel_bg'            => 'required',
                'color_panel_text'          => 'required',
                'color_accept_button'       => 'required',
                'color_reject_button'       => 'required',
                'color_transfer_button'     => 'required',
                'color_complete_button'     => 'required',
            ];
            if($this->validate($request, $rules)){
                unset($postData['_token']);
                if(!empty($postData)){
                    foreach($postData as $key => $value){
                        $fields = [
                            'value'            => strip_tags($postData[$key])
                        ];
                        GeneralSetting::where('key', '=', $key)->where('is_active', '=', 1)->update($fields);
                    }
                }
                return redirect()->back()->with('success_message', 'Color Settings Updated Successfully');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required');
            }
        }
    /* settings */
}
