<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\Page;
use App\Models\EmailLog;
use App\Models\UserActivity;
use App\Services\SiteAuthService;
use App\Models\User;
use Session;
use App\Helpers\Helper;
use Hash;
use DB;

class UserController extends Controller
{
    protected $siteAuthService;
    protected $data;
    function __construct()
    {
        $this->siteAuthService = new SiteAuthService();
    }
    
    public function page($slug){
        // $data['setting']                = GeneralSetting::where('id', '=', 1)->first();
        // $data['page_content']           = Page::where('page_slug', '=', $slug)->first();
        // $title                          = (($data['page_content'])?$data['page_content']->page_name:'');
        // $page_name                      = 'page-content';
        // $data = $this->siteAuthService->admin_before_login_layout($title, $page_name, $data);
        // return view('maincontents.' . $page_name, $data);
        $data['page_content']           = Page::where('page_slug', '=', $slug)->first();
        return view('maincontents.page-content', $data);
    }
    public function deleteAccountRequest(){
        return view('maincontents.delete-account-request');
    }
    public function deleteaccount(Request $request)
    {
        if($request->isMethod('post')){
            $postData           = $request->all();
            // Helper::pr($postData);
            // $user_type         = $postData['user_type'];
            // $Entityname         = $postData['entity_name'];
            // $email             = $postData['email'];
            // $phone           = $postData['phone'];
            // $comment           = !empty($request->comment) ? $request->comment : null;
            // $rules = [                                 
            //     'user_type'           => 'required',
            //     'entity_name'         => 'required',
            //     'email'               => 'required|email',
            //     'phone'               => 'required|numeric',                
            // ];
            
            // if ($this->validate($request, $rules)) {
            //     $email_validation    = DeleteAccountRequest::where('email', $email)->first();               
            //     if($email_validation){
            //         $user_id           = $email_validation->id;
            //         $fields = [
            //             'user_type'       => $user_type,
            //             'entity_name'     => $Entityname,
            //             'email'           => $email,
            //             'is_email_verify' => 1,
            //             'is_phone_verify' => 1,
            //             'phone'           => $phone,
            //             'comments'         => $comment,
            //             'created_at'      => date('Y-m-d H:i:s'), 
            //             'updated_at'    => date('Y-m-d H:i:s'),             
            //             'status'          => 1,                                  
            //         ];
            //         DeleteAccountRequest::where('id', $user_id)->update($fields);
            //     }
            //     $fields2 = [
            //         'user_type'       => $user_type,
            //         'entity_name'     => $Entityname,
            //         'email'           => $email,
            //         'is_email_verify' => 1,
            //         'is_phone_verify' => 1,
            //         'phone'           => $phone,
            //         'comments'         => $comment,
            //         'created_at'      => date('Y-m-d H:i:s'),                    
            //         'status'          => 1,                                  
            //     ];                
            //     DeleteAccountRequest::insert($fields2);                
            //     return redirect('delete-account')->with('success_message', 'Delete account request send successfully');
            // } else {
            //     return redirect('delete-account')->with('error_message', 'Please enter valid data');
                
            // }
            return redirect('delete-account')->with('success_message', 'Delete account request submitted successfully');
        }        
    }
}
