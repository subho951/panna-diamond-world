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


class UploadLeadController extends Controller
{
    protected $siteAuthService;
    protected $data;
    public function __construct()
    {
        $this->siteAuthService = new SiteAuthService();
        $this->data = array(
            'title'             => 'Lead',
            'controller'        => 'UploadLeadController',
            'controller_route'  => 'upload-lead',
            'primary_key'       => 'id',
        );
    }

    /* upload */
    public function upload(Request $request)
    {
        $data['module']           = $this->data;
        if ($request->isMethod('post')) {
            $postData = $request->all();

            $rules = [
                'branch_id'             => 'required',
                'telecaller_id'         => 'required',
                'lead_title'            => 'required',
                'lead_date'             => 'required',
                'lead_file'             => 'required',
            ];
            if ($this->validate($request, $rules)) {

                $csv = $request->file('lead_file');
                $csvName = time() . '_' . $csv->getClientOriginalName();
                $ext = $csv->getClientOriginalExtension();
                if ($ext == 'csv') {
                    $csv->move(public_path('uploads/lead/'), $csvName);
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

                    $uploadLead = new UploadLead();

                    $uploadLead->branch_id             = strip_tags($postData['branch_id']);
                    $uploadLead->telecaller_id         = json_encode($postData['telecaller_id']);
                    $uploadLead->title                 = strip_tags($postData['lead_title']);
                    $uploadLead->campaign_type_id      = (isset($postData['campaign_type_id']) ? strip_tags($postData['campaign_type_id']) : 0);
                    $uploadLead->campaign_id           = (isset($postData['campaign_id']) ? strip_tags($postData['campaign_id']) : 0);
                    //$uploadLead->lead_date       = date_format(date_create(strip_tags($postData['lead_date'])), "Y-m-d"),
                    $uploadLead->lead_date             = strip_tags($postData['lead_date']);
                    $uploadLead->filename              = strip_tags($csvName);

                    $uploadLead->save();
                    $lastInsertId = $uploadLead->id;

                    //handling csv file and insert data to master_leads table                    
                    $csvPath = public_path('uploads/lead/' . $csvName); // Open file
                    $handle = fopen($csvPath, 'r');
                    $header = fgetcsv($handle); // First row (column headers)                   
                    array_shift($header); // Remove first column from header
                    // Initialize column-wise array
                    $csvArray = [];
                    foreach ($header as $column) {
                        $csvArray[$column] = []; // initialize each column
                    }
                    // Loop through remaining rows
                    while (($row = fgetcsv($handle)) !== false) {
                        array_shift($row); // Remove the first column value
                        foreach ($header as $index => $column) {
                            $csvArray[$column][] = $row[$index] ?? null;
                        }
                    }
                    fclose($handle);

                    // max length among all columns
                    $maxLength = max(array_map('count', $csvArray));

                    $skippedRows = 0;
                    $insertedRows = 0;
                   
                    for ($i = 0; $i < $maxLength; $i++) 
                    {
                        $sl_no = MasterLead::orderBy('id', 'desc')->value('sl_no') ?? 0;
                        $sl_no++;
                        $lead_no = str_pad($sl_no, 8, '0', STR_PAD_LEFT);

                        $csvRow = [];
                        foreach ($csvArray as $key => $value)
                        {
                           
                            $header_id = LeadHeader::where('name', '=', $key)->where('status', '=', 1)->first()->id  ?? '';
                            if ($header_id) 
                            {
                                $csvCell = [];
                                $csvCell = [
                                    "header_id" => $header_id,
                                    "header_value" => strip_tags(isset($value[$i]) ? $value[$i] : ''),
                                    "upload_id"  => $lastInsertId,
                                    "sl_no" => $sl_no,
                                    "lead_no" => $lead_no
                                ];
                                
                                if($key == 'Phone')
                                {
                                    $phone = MasterLead::where('header_id', '=', 4)->where('header_value', '=', strip_tags($value[$i]))->exists();
                                    if($phone) //true
                                    {
                                        $skippedRows++;
                                        $csvRow = [];
                                        break;
                                    }
                                }

                                $csvRow[] = $csvCell;
                                
                                
                            } else {
                                return redirect()->back()->with('error_message', 'Please Maintain Proper CSV Format !!!');
                            }    
                        }

                        if (!empty($csvRow)) 
                        {
                            $cellsPerRow = 0;
                            foreach($csvRow as $cell)   //uploading 1 lead i.e., 1 csv row
                            {
                                $masterLead = new MasterLead();

                                $masterLead->header_id        = $cell["header_id"];       
                                $masterLead->header_value     = $cell["header_value"];
                                $masterLead->upload_id        = $cell["upload_id"];
                                $masterLead->sl_no            = $cell["sl_no"];
                                $masterLead->lead_no          = $cell["lead_no"];

                                $masterLead->save();

                                if($cellsPerRow == 0)
                                {
                                    $branchLead =  new BranchLead();

                                    $branchLead->upload_id = $masterLead->upload_id ;
                                    $branchLead->master_lead_id = $masterLead->id ;
                                    $branchLead->lead_sl_no = $masterLead->sl_no ;
                                    $branchLead->branch_id = $uploadLead->branch_id ;
                                    $branchLead->campaign_type_id = $uploadLead->campaign_type_id ;                                   
                                    $branchLead->campaign_id = $uploadLead->campaign_id ;
                                    $branchLead->assigned_telecaller_id = 0 ;
                                    
                                    $branchLead->save();                                    
                                }
                               
                                $cellsPerRow++;
                            }           
                           
                        }

                    }

                    $insertedBranchLead = BranchLead::where('upload_id', '=', $lastInsertId)->where('status', '=', 1)->get();

                    // Get telecaller IDs array
                    $telecallerIds = json_decode($uploadLead->telecaller_id, true);
                    $telecallerCount = count($telecallerIds);
 
                    // Update each lead with assigned telecaller in circular manner
                    foreach ($insertedBranchLead as $index => $lead) {
                        $lead->assigned_telecaller_id = $telecallerIds[$index % $telecallerCount];
                        $lead->update();
                    }
                   

                    $insertedRows = $maxLength - $skippedRows;

                } else {
                    return redirect()->back()->with('error_message', 'Please Upload CSV File !!!');
                }

                return redirect($this->data['controller_route'] )->with('success_message',  $insertedRows. ' Lead(s) Uploaded Successfully !!!' . ($skippedRows > 0 ? ' And ' . $skippedRows . ' Lead(s) Skipped !!!' : ''));
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }


        $data['module']                 = $this->data;
        $title                          = 'Upload' . ' ' . $this->data['title'];
        $page_name                      = 'upload-lead.upload';
        $data['row']                    = [];
        $data['branches']               = Branch::where('status', '=', 1)->get();
        $data['campaign_types']         = CampaignType::where('status', '=', 1)->get();
        $data                           = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
        return view('maincontents.' . $page_name, $data);
    }
    /* upload */

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
}
