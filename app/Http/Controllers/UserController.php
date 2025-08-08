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
    
    public function page($slug){
        $data['setting']                = GeneralSetting::where('id', '=', 1)->first();
        $data['page_content']           = Page::where('page_slug', '=', $slug)->first();
        $title                          = (($data['page_content'])?$data['page_content']->page_name:'');
        $page_name                      = 'page-content';
        $data = $this->siteAuthService->admin_before_login_layout($title, $page_name, $data);
        return view('maincontents.' . $page_name, $data);
    }
}
