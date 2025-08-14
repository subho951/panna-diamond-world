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


    /* list upload leads */
    public function list()
    {
        $data['module']                 = $this->data;
        $title                          = 'Upload' . ' ' . $this->data['title'];
        $page_name                      = 'upload-lead.upload';
        // $data['row']                    = [];
        $data['branches']               = Branch::where('status', '=', 1)->get();
        $data['campaign_types']         = CampaignType::where('status', '=', 1)->get();

        //listing purpose
        $uploadedLeadsArr               = UploadLead::where('status', '!=', 3)->orderBy('id', 'desc')->get();
        $leadListArr = [];
        foreach ($uploadedLeadsArr as $leadRow) {
            $leadRow['branch_name'] = Branch::where('id', '=', $leadRow->branch_id)->value('name') ?? 'N/A';
            $leadRow['campaign_type_name'] = CampaignType::where('id', '=', $leadRow->campaign_type_id)->value('name')  ?? 'N/A';
            $leadRow['campaign_name'] = Campaign::where('id', '=', $leadRow->campaign_id)->value('name')  ?? 'N/A';

            $telecallerArr = json_decode($leadRow->telecaller_id, true);
            $telecallerNameArr = [];
            foreach ($telecallerArr as $telecaller_id) {
                $telecallerNameArr[] = User::where('id', '=', $telecaller_id)->first()->first_name . ' ' . User::where('id', '=', $telecaller_id)->first()->last_name;
            }
            $leadRow['telecaller_name_arr'] = $telecallerNameArr ?? 'N/A';

            $leadRow['encodedId'] = Helper::encoded($leadRow->id);

            $leadListArr[] = $leadRow;
        }

        // dd($leadListArr) ;

        $data                           = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
        return view('maincontents.' . $page_name, $data)->with(["leadListArr" => $leadListArr]);
    }
    /* list upload leads */


    /* preview upload leads */
    public function preview(Request $request)
    {
        if ($request->isMethod('post')) {
            $rules = [
                'lead_file' => 'required|mimes:csv',
                'branch_id' => 'required',
                'lead_title' => 'required',
                'telecaller_id' => 'required|array|min:1',
                'lead_date' => 'required|date',
            ];
            if ($this->validate($request, $rules)) {

                $file = $request->file('lead_file');
                $ext = $file->getClientOriginalExtension();
                if ($ext == 'csv') {
                    $path = $file->storeAs('', time() . '_' . $file->getClientOriginalName(), 'tmp');
                    $fullPath = storage_path('app/tmp/' . basename($path));

                    $handle = fopen($fullPath, 'r');
                    $headerRow = fgetcsv($handle);
                    if (!$headerRow) {
                        return back()->with('error_message', 'Invalid CSV File !!!');
                    }

                    // Remove first column (#)
                    array_shift($headerRow);

                    $sluggedHeaders = [];
                    foreach ($headerRow as $h) {
                        $sluggedHeaders[] = strtolower(Helper::clean(strip_tags($h)));
                    }

                    // Get headers from LeadHeader table in the correct order
                    // $expectedHeaders = LeadHeader::where('status', '=',1)
                    //     ->orderBy('rank', 'asc')
                    //     ->pluck('slug')
                    //     ->toArray();

                    $expectedHeaders = LeadHeader::where('status', '=',1)->pluck('slug')->toArray();

                    // Compare count first
                    if (count($expectedHeaders) !== count($sluggedHeaders)) {
                        return redirect()->back()->with('error_message', 'CSV Header Count Does Not Match Expected Format !!!');
                    }

                    // Compare each header in the same order
                    // if ($expectedHeaders !== $sluggedHeaders) {
                    //     // Find differences for better error reporting
                    //     $mismatched = [];
                    //     foreach ($expectedHeaders as $index => $expectedHeader) {
                    //         if (!isset($sluggedHeaders[$index]) || $sluggedHeaders[$index] !== $expectedHeader) {
                    //             $mismatched[] = "Expected: {$expectedHeader}, Found: " . ($sluggedHeaders[$index] ?? 'MISSING');
                    //         }
                    //     }

                    //     $errorDetails = implode('; ', $mismatched);
                    //     return redirect()->back()->with('error_message', "Invalid CSV Header Format !!! Differences: {$errorDetails}");
                    // }



                    // Required fields slugs
                    $requiredSlugs = LeadHeader::where('is_required', '=', 1)->where('status', '=', 1)->pluck('slug')->toArray();

                    // Telecaller setup
                    $telecallerIds = $request->telecaller_id;
                    $telecallerCount = count($telecallerIds);
                    // $telecallerMap = User::whereIn('id', $telecallerIds)->pluck('name', 'id')->toArray();
                    $telecallerMap = User::whereIn('id', $telecallerIds)
                        ->get()
                        ->mapWithKeys(function ($user) {
                            return [$user->id => trim($user->first_name . ' ' . $user->last_name)];
                        })
                        ->toArray();
                    // dd($telecallerMap);

                    $rows = [];
                    $total = 0;
                    $invalid = 0;
                    $duplicate = 0;
                    $duplicateWRTCampaign = 0;
                    $duplicateWOCampaign = 0;
                    $duplicateInFile = 0;
                    $new = 0;
                    $teleIndex = 0;
                    

                    $seenPhones = []; // For checking duplicates in current file
                    $seenWhatsapp = []; // For checking duplicates in current file

                    while (($data = fgetcsv($handle)) !== FALSE) 
                    {
                        array_shift($data); // Remove first column (#)
                        $total++;

                        $status = 'Success';
                        $isInvalid = false;
                        $isDuplicateWRTCampaign = false;
                        $isDuplicateWOCampaign = false;
                        $isDuplicate = false;
                        $isDuplicateInFile = false;
                        $comment = '<span class="text-info">New</span>';
                        $phoneValue = null;
                        $sl_no_Duplicate = 0;

                        // Build row associative array
                        $rowData = array_combine($sluggedHeaders, $data);

                        // Check required fields
                        if (count($requiredSlugs) > 0) {
                            $commentArr = [];
                            foreach ($requiredSlugs as $reqSlug) {
                                if (empty($rowData[$reqSlug])) {
                                    $isInvalid = true;
                                    $commentArr[] =  $reqSlug;
                                    // $comment = 'Invalid: Missing ' . $reqSlug;
                                    // break;
                                    $rowData[$reqSlug] = '<span class="text-danger">Not Found</span>';
                                }
                            }

                            if (!empty($commentArr)) {
                                // $comment = '<span class="text-danger">Invalid: Missing ' . implode(', ', $commentArr) . '</span>';
                                $comment = '<span class="text-danger">Invalid</span>';
                            }
                        }

                        $invalidCommentArr = [];
                        $duplicateCommentArr = [];

                        if(!empty($rowData['phone']))
                        {
                            // Phone validation
                            if (!$isInvalid && isset($rowData['phone'])) 
                            {
                                $phoneValue = preg_replace('/\D/', '', $rowData['phone']);
                                if (!ctype_digit($phoneValue) || strlen($phoneValue) !== 10) 
                                {
                                    $isInvalid = true;
                                    // $comment = '<span class="text-danger">Invalid: Bad phone</span>';
                                    $invalidCommentArr[] = 'Bad phone-number' ;
                                    $rowData['phone'] = '<span class="text-danger">Bad Phone Number</span>';
                                }
                            }
    
                            // Duplicate check (only if not invalid)
                            if (!$isInvalid && $phoneValue) 
                            {
                                $sl_no_Duplicate =  MasterLead::where('header_id', '=', 4)->where('header_value', '=', $phoneValue)->where('status', '!=', 3)->value('sl_no') ?? 0;

                                if (in_array($phoneValue, $seenPhones)) 
                                {
                                    $isDuplicate = true;
                                    // $comment = '<span class="text-warning">Duplicate: Already in file</span>';
                                    $duplicateCommentArr[] = 'phone-number already in file';
                                } 
                                else 
                                {
                                    $seenPhones[] = $phoneValue;
                                    // Check in DB
                                    $existsInDB = MasterLead::where('header_id', '=', 4)
                                        ->where('header_value', $phoneValue)
                                        ->where('status', '!=', 3)
                                        ->exists();
                                    if ($existsInDB) {
                                        $isDuplicate = true;
                                        // $comment = '<span class="text-warning">Duplicate: Exists in DB</span>';
                                        $duplicateCommentArr[] = 'phone-number exists in DB';
                                    }
                                }
                            }
                        }

                        if(!empty($rowData['whatsapp-number']))
                        {
                            // Whatsapp validation
                            if (!$isInvalid && isset($rowData['whatsapp-number'])) 
                            {
                                $whatsappValue = preg_replace('/\D/', '', $rowData['whatsapp-number']);
                                if (!ctype_digit($whatsappValue) || strlen($whatsappValue) !== 10) 
                                {
                                    $isInvalid = true;
                                    // $comment = '<span class="text-danger">Invalid: Bad whatsapp</span>';
                                    $invalidCommentArr[] = 'Bad whatsapp-number' ;
                                    $rowData['whatsapp-number'] = '<span class="text-danger">Bad Whatsapp Number</span>';
                                }
                            }
    
                            // Duplicate check (only if not invalid)
                            if (!$isInvalid && $whatsappValue) 
                            {
                                $sl_no_Duplicate = MasterLead::where('header_id', '=', 14)->where('header_value', '=', $whatsappValue)->where('status', '!=', 3)->value('sl_no') ?? 0;

                                if (in_array($whatsappValue, $seenWhatsapp)) 
                                {
                                    $isDuplicate = true;
                                    // $comment = '<span class="text-warning">Duplicate: Already in file</span>';
                                    $duplicateCommentArr[] = 'whatsapp-number already in file';
                                } 
                                else 
                                {
                                    $seenWhatsapp[] = $whatsappValue;
                                    // Check in DB
                                    $existsInDB = MasterLead::where('header_id','=', 14)
                                        ->where('header_value', $whatsappValue)
                                        ->where('status', '!=', 3)
                                        ->exists();
                                    if ($existsInDB) {
                                        $isDuplicate = true;
                                        // $comment = '<span class="text-warning">Duplicate: Exists in DB</span>';
                                        $duplicateCommentArr[] = 'whatsapp-number exists in DB';
                                    }
                                }
                            }
                        }

                        if(!empty($invalidCommentArr))
                        {
                            // $comment = '<span class="text-danger">Invalid: ' . implode(', ', $invalidCommentArr) . '</span>';
                            $comment = '<span class="text-danger">Invalid</span>';
                        }
                        elseif(!empty($duplicateCommentArr)) // Duplicate check (only if not invalid)
                        {
                            // $comment = '<span class="text-warning">Duplicate: ' . implode(', ', $duplicateCommentArr) .'</span>';
                            if( (in_array('phone-number already in file' , $duplicateCommentArr) ) || ( in_array('whatsapp-number already in file' , $duplicateCommentArr)) )
                            {
                                $isDuplicateInFile = true;
                                $comment = '<span class="text-secondary">Already In File</span>';
                            }
                            else
                            {
                                $comment = '<span class="text-warning">Existing</span>';
                            }
                        }
                        
                        if($sl_no_Duplicate)
                        {
                            $duplicateData = MasterLead::where('sl_no', '=', $sl_no_Duplicate)->first();
                
                            //showing that, same lead is not inserted in BranchLead table w.r.t same campaign
                            $campaign_check = false;
                            if(($request->campaign_type_id != 0) && ($request->campaign_id != 0))
                            {
                                $campaign_check = BranchLead::where('campaign_type_id', '=', $request->campaign_type_id)->where('campaign_id', '=', $request->campaign_id)->where('master_lead_id', '=', $duplicateData->id)->where('status', '!=', 3)->exists();
                            }
                            elseif(($request->campaign_type_id == 0) && ($request->campaign_id == 0))
                            {
                                $isDuplicateWOCampaign = true;

                                if(BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '=', 0)->where('campaign_id', '=', 0)->exists()) // true
                                {
                                    //without selecting campaign and also exist in db without campaign
                                    $isDuplicateWOCampaign = true;
                                    $comment = '<span class="text-warning">Already Exists</span>';
                                    if( (in_array('phone-number already in file' , $duplicateCommentArr) ) || ( in_array('whatsapp-number already in file' , $duplicateCommentArr)) )
                                    {
                                        $isDuplicateInFile = true;
                                        $comment = '<span class="text-secondary">Already In File</span>';
                                    }
                                }

                                if(BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '!=', 0)->where('campaign_id', '!=', 0)->exists()) // true
                                {
                                    // without selecting campaign and also exist in db with another campaign
                                    // insertion allowed in BranchLead
                                    $comment = '<span class="text-warning">Existing</span>';
                                    $isDuplicate = true;
                                    $isDuplicateWOCampaign = false;
                                    if( (in_array('phone-number already in file' , $duplicateCommentArr) ) || ( in_array('whatsapp-number already in file' , $duplicateCommentArr)) )
                                    {
                                        $isDuplicateInFile = true;
                                        $comment = '<span class="text-secondary">Already In File</span>';
                                    }
                                }

                                if((BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '=', 0)->where('campaign_id', '=', 0)->exists()) && (BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '!=', 0)->where('campaign_id', '!=', 0)->exists())) // true
                                {   
                                    //both
                                    $isDuplicateWOCampaign = true;
                                    $comment = '<span class="text-warning">Already Exists</span>';
                                    if( (in_array('phone-number already in file' , $duplicateCommentArr) ) || ( in_array('whatsapp-number already in file' , $duplicateCommentArr)) )
                                    {
                                        $isDuplicateInFile = true;
                                        $comment = '<span class="text-secondary">Already In File</span>';
                                    }
                                }
                            }

                            if($campaign_check)
                            {
                                $isDuplicateWRTCampaign = true;
                                $comment = '<span class="text-warning">Exists w.r.t. Campaign</span>';
                                if( (in_array('phone-number already in file' , $duplicateCommentArr) ) || ( in_array('whatsapp-number already in file' , $duplicateCommentArr)) )
                                {
                                    $isDuplicateInFile = true;
                                    $comment = '<span class="text-secondary">Already In File</span>';
                                }
                            }
                        }

                        // Decide status
                        if ($isInvalid) {
                            $status = '<i class="fa-solid fa-circle-xmark"></i>';
                            $invalid++;
                            $telecallerAssign = 'no';

                        }
                        elseif($isDuplicateInFile)
                        {
                            $status = '<i class="fa-regular fa-calendar-check"></i>';
                            $duplicateInFile++;
                            $telecallerAssign = 'no';

                        }
                        elseif($isDuplicateWOCampaign)
                        {
                            $status = '<i class="fa-regular fa-calendar-check"></i>';
                            $duplicateWOCampaign++;
                            $duplicate++;
                            $telecallerAssign = 'no';

                        }
                        elseif($isDuplicateWRTCampaign)
                        {
                            $status = '<i class="fa-regular fa-calendar-check"></i>';
                            $duplicateWRTCampaign++;
                            $duplicate++;
                            $telecallerAssign = 'no';

                        }
                        elseif ($isDuplicate) {
                            $status = '<i class="fa-solid fa-star"></i>';
                            $duplicate++;
                            $telecallerAssign = 'yes';
                            $teleIndex++;
                        }
                        else {
                            $status = '<i class="fa-solid fa-check"></i>';
                            $new++;
                            $telecallerAssign = 'yes';
                            $teleIndex++;
                        }

                       

                        // Telecaller assignment only if not invalid
                        $telecallerName = ($telecallerAssign == 'yes')
                            ? $telecallerMap[$telecallerIds[($teleIndex - 1) % $telecallerCount]]
                            : '---------------';


                        $rows[] = [
                            // 'original' => $data,
                            'original' => $rowData,
                            'telecaller' => $telecallerName,
                            'status' => $status,
                            'comment' => $comment
                        ];
                    }
                    fclose($handle);

                    $data['headers'] = $headerRow;
                    $data['rows'] = $rows;
                    $data['counts'] = [
                        'total' => $total,
                        'invalid' => $invalid,
                        'duplicate' => $duplicate,
                        'duplicateWRTCampaign' => $duplicateWRTCampaign,
                        'duplicateWOCampaign'=> $duplicateWOCampaign,
                        'duplicateInFile' => $duplicateInFile,
                        'new' => $new,
                        'assinableLead' => $teleIndex,
                    ];
                    $data['branch_name'] = Branch::where('id', '=', $request->branch_id)->where('status', '=', 1)->value('name');

                    $data['branch_id'] = $request->branch_id;
                    $data['telecaller_id'] = $request->telecaller_id;
                    $data['lead_title'] = $request->lead_title;
                    $data['lead_date'] = $request->lead_date;
                    $data['campaign_type_id'] = $request->campaign_type_id;
                    $data['campaign_id'] = $request->campaign_id;
                    $data['tempFile'] = $fullPath;
                    
                    $data['campaign_type_name'] = CampaignType::where('id', '=', $request->campaign_type_id)->where('status', '!=', 3)->value('name') ?? '';
                    $data['campaign_name'] = Campaign::where('id', '=', $request->campaign_id)->where('status', '!=', 3)->value('name') ?? '';

                    $data['module']                 = $this->data;
                    $title         = 'Preview' . ' ' . $this->data['title'];
                    $page_name     = 'upload-lead.upload-preview';
                    $data          = $this->siteAuthService->admin_after_login_layout($title, $page_name, $data);
                    // Pass data to view
                    return view('maincontents.' . $page_name, $data);
                } else {
                    return redirect()->back()->with('error_message', 'Please Upload CSV File !!!');
                }
            } else {
                return redirect()->back()->with('error_message', 'Star Marked Fields Are Required !!!');
            }
        }
    }
    /* preview upload leads */


    /* store upload leads */
    public function store(Request $request)
    {
        if ($request->isMethod('post')) 
        {
            $postData = $request->all();

            $rules = [
                'branch_id'             => 'required',
                'telecaller_id'         => 'required|array|min:1',
                'lead_title'            => 'required',
                'lead_date'             => 'required|date',
                'temp_file'             => 'required',
            ];
            if ($this->validate($request, $rules)) {

                // Temp file path from preview
                $tempFilePath = $request->input('temp_file');

                if (!file_exists($tempFilePath)) {
                    return redirect()->back()->with('error_message', 'Failed To Upload !!!');
                }

                // Prepare final file name and path
                $csvName = time() . '_' . basename($tempFilePath);
                $destinationPath = public_path('uploads/lead/');

                // Move file from storage/app/tmp to public/uploads/lead
                if (!rename($tempFilePath, $destinationPath . $csvName)) {
                    return redirect()->back()->with('error_message', 'Failed To Upload !!!');
                }

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
                //$uploadLead->lead_date           = date_format(date_create(strip_tags($postData['lead_date'])), "Y-m-d"),
                $uploadLead->lead_date             = strip_tags($postData['lead_date']);
                $uploadLead->filename              = strip_tags($csvName);

                $uploadLead->save();
                $lastInsertId = $uploadLead->id;

                //handling csv file and insert data to master_leads table                    
                $csvPath = public_path('uploads/lead/' . $csvName); // Open file
                $handle = fopen($csvPath, 'r');
                $header = fgetcsv($handle); // First row (column headers)                   
                array_shift($header); // Remove first column from header

                $csvArray = []; // Initialize column-wise array
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

                // max length among all columns i.e., total no. of rows(leads) trying to upload
                $maxLength = max(array_map('count', $csvArray));


                $skippedRows = 0;
                $insertedRows = 0;
                $totalLeadAssignedToTelecaller = 0;

                for ($i = 0; $i < $maxLength; $i++) 
                {
                    $sl_no = MasterLead::orderBy('id', 'desc')->value('sl_no') ?? 0;
                    $sl_no++;
                    $lead_no = str_pad($sl_no, 8, '0', STR_PAD_LEFT);

                    $csvRow = [];
                    $isDuplicatePhone = false;
                    $isDuplicateWhatsapp = false;
                    $sl_no_Duplicate = 0;
                    foreach ($csvArray as $key => $value) 
                    {
                        $slug = strtolower(Helper::clean(strip_tags($key)));
                        $header_id = LeadHeader::where('slug', '=', $slug)->where('status', '=', 1)->first()->id  ?? '';
                        
                        if ($header_id) 
                        {
                            $csvCell = [];
                            $csvCell = [
                                "header_id" => $header_id,
                                "header_value" => isset($value[$i]) && trim($value[$i]) !== '' ? strip_tags(trim($value[$i])) : NULL,
                                "upload_id"  => $lastInsertId,
                                "sl_no" => $sl_no,
                                "lead_no" => $lead_no,
                                "created_by" => session('user_data')['user_id'],
                                "updated_by" => session('user_data')['user_id'],
                            ];

                            //handling required fields
                            $isRequiredArr = LeadHeader::where('is_required', '=', 1)->where('status', '=', 1)->pluck('slug')->toArray();
                            // dd($isRequiredArr);
                            if (count($isRequiredArr) > 0) 
                            {
                                if (in_array($slug, $isRequiredArr)) 
                                {
                                    if ($csvCell["header_value"] == NULL) 
                                    {
                                        $skippedRows++;
                                        $csvRow = [];
                                        break;
                                    }
                                }
                            }

                            if ($slug == 'phone')   //validating with respect to phone
                            {
                                if(!empty($value[$i]))
                                {
                                    $phoneValue = strip_tags($value[$i]);
                                    $phoneValue = preg_replace('/\D/', '', $phoneValue); // remove all non-digit characters
    
                                    // Check if it's numeric and 10 digits
                                    if (!ctype_digit($phoneValue) || strlen($phoneValue) !== 10) {
                                        $skippedRows++;
                                        $csvRow = [];
                                        break;
                                    }
                                    // Check if it's already exists
                                    $phone = MasterLead::where('header_id', '=', 4)->where('header_value', '=', $phoneValue)->where('status', '!=', 3)->exists();
                                    if ($phone) //true
                                    {
                                        $isDuplicatePhone = true;
                                        $sl_no_Duplicate =  MasterLead::where('header_id', '=', 4)->where('header_value', '=', $phoneValue)->where('status', '!=', 3)->value('sl_no');
                                        $skippedRows++;
                                        $csvRow = [];
                                        break;
                                    }
    
                                    $csvCell["header_value"] = $phoneValue; //extra cleaning for phone number
                                }
                            }

                            if ($slug == 'whatsapp-number')   //validating with respect to whatsapp-number
                            {
                                if(!empty($value[$i]))
                                {
                                    $whatsappValue = strip_tags($value[$i]);
                                    $whatsappValue = preg_replace('/\D/', '', $whatsappValue); // remove all non-digit characwhatsapp
                                    // Check if it's numeric and 10 digits
                                    if (!ctype_digit($whatsappValue) || strlen($whatsappValue) !== 10) {
                                        $skippedRows++;
                                        $csvRow = [];
                                        break;
                                    }
                                    // Check if it's already exists
                                    $whatsapp = MasterLead::where('header_id', '=', 14)->where('header_value', '=', $whatsappValue)->where('status', '!=', 3)->exists();
                                    if ($whatsapp) //true
                                    {
                                        $isDuplicateWhatsapp = true;
                                        $sl_no_Duplicate = MasterLead::where('header_id', '=', 14)->where('header_value', '=', $whatsappValue)->where('status', '!=', 3)->value('sl_no');
                                        $skippedRows++;
                                        $csvRow = [];
                                        break;
                                    }
    
                                    $csvCell["header_value"] = $whatsappValue; //extra cleaning for whatsapp number
                                }
                            }
                            $csvRow[] = $csvCell;
                        }
                        else {

                            $deleteLead = UploadLead::find($lastInsertId);
                            unlink(public_path('uploads/lead/' . $deleteLead->filename));
                            $deleteLead->forceDelete();
                            return redirect()->back()->with('error_message', 'Please Maintain Proper CSV Format !!!');
                        }
                    }

                    Helper::pr($csvRow);

                    if (!empty($csvRow)) 
                    {
                        $cellsPerRow = 0;
                        foreach ($csvRow as $cell)   //uploading 1 lead i.e., 1 csv row
                        {
                            $masterLead                     = new MasterLead();

                            $masterLead->header_id          = $cell["header_id"];
                            $masterLead->header_value       = $cell["header_value"];
                            $masterLead->upload_id          = $cell["upload_id"];
                            $masterLead->sl_no              = $cell["sl_no"];
                            $masterLead->lead_no            = $cell["lead_no"];
                            $masterLead->created_by         = session('user_data')['user_id'];
                            $masterLead->updated_by         = session('user_data')['user_id'];

                            $masterLead->save();

                            if ($cellsPerRow == 0) {
                                $branchLead                             =  new BranchLead();

                                $branchLead->upload_id                  = $masterLead->upload_id;
                                $branchLead->master_lead_id             = $masterLead->id;
                                $branchLead->lead_sl_no                 = $masterLead->sl_no;
                                $branchLead->branch_id                  = $uploadLead->branch_id;
                                $branchLead->campaign_type_id           = $uploadLead->campaign_type_id;
                                $branchLead->campaign_id                = $uploadLead->campaign_id;
                                $branchLead->assigned_telecaller_id     = 0;
                                $branchLead->created_by                 = session('user_data')['user_id'];
                                $branchLead->updated_by                 = session('user_data')['user_id'];

                                $branchLead->save();
                                $totalLeadAssignedToTelecaller++;
                            }

                            $cellsPerRow++;
                        }
                    }

                    if($isDuplicatePhone || $isDuplicateWhatsapp)
                    {
                        if($sl_no_Duplicate)
                        {
                            $duplicateData = MasterLead::where('sl_no', '=', $sl_no_Duplicate)->first();
                
                            //Restricting that, same lead is not inserted in BranchLead table w.r.t same campaign
                            $campaign_check = false;
                            if(($uploadLead->campaign_type_id != 0) && ($uploadLead->campaign_id != 0))
                            {
                                $campaign_check = BranchLead::where('campaign_type_id', '=', $uploadLead->campaign_type_id)->where('campaign_id', '=', $uploadLead->campaign_id)->where('master_lead_id', '=', $duplicateData->id)->where('status', '!=', 3)->exists();
                            }
                            elseif(($uploadLead->campaign_type_id == 0) && ($uploadLead->campaign_id == 0))
                            {
                                $campaign_check = false;
                                
                                if(BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '=', 0)->where('campaign_id', '=', 0)->exists()) // true
                                {
                                    //without selecting campaign and also exist in db without campaign
                                    $campaign_check = true;
                                }

                                if(BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '!=', 0)->where('campaign_id', '!=', 0)->exists()) // true
                                {
                                    //without selecting campaign and also exist in db with another campaign
                                    $campaign_check = false; // insertion allowed
                                }

                                if((BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '=', 0)->where('campaign_id', '=', 0)->exists()) && (BranchLead::where('lead_sl_no', '=', $sl_no_Duplicate)->where('master_lead_id', '=', $duplicateData->id)->where('campaign_type_id', '!=', 0)->where('campaign_id', '!=', 0)->exists())) // true
                                {   
                                    //both
                                    $campaign_check = true;
                                }

                            }
                           
                            if(!$campaign_check) // !false
                            {
                                //Preventing same lead from a single csv can't be inserted in BranchLead table
                                $upload_id_check = false;
                                $upload_id_check = BranchLead::where('master_lead_id', '=', $duplicateData->id)->where('upload_id', '=' , $lastInsertId)->where('status', '!=', 3)->exists();
                                
                                if(!$upload_id_check) // !false
                                {                                 
                                    $branchLead =  new BranchLead();
    
                                    $branchLead->upload_id = $lastInsertId;
                                    $branchLead->master_lead_id = $duplicateData->id;
                                    $branchLead->lead_sl_no = $sl_no_Duplicate;
                                    $branchLead->branch_id = $uploadLead->branch_id;
                                    $branchLead->campaign_type_id = $uploadLead->campaign_type_id;
                                    $branchLead->campaign_id = $uploadLead->campaign_id;
                                    $branchLead->assigned_telecaller_id = 0;
                                    $branchLead->created_by = session('user_data')['user_id'];
                                    $branchLead->updated_by = session('user_data')['user_id'];
    
                                    $branchLead->save();
                                    $totalLeadAssignedToTelecaller++;
                                }
                            }

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

                $uploadLead->total_upload = $maxLength;
                $uploadLead->success_upload = $insertedRows;
                $uploadLead->failed_upload = $skippedRows;
                $uploadLead->total_assigned = $totalLeadAssignedToTelecaller;
                $uploadLead->update();


                return redirect($this->data['controller_route'])->with('success_message',  $insertedRows . ' Lead(s) Uploaded Successfully !!!' . ($skippedRows > 0 ? ' And ' . $skippedRows . ' Lead(s) Skipped !!!' : '') . ' And ' . $totalLeadAssignedToTelecaller . ' Lead(s) Assigned To Telecaller(s) !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
    }
    /* store upload leads */


    /* cancel Upload */
    public function cancelUpload(Request $request)
    {
        $tempFile = $request->tempFile; // file name 

        if ($tempFile) {
            $filePath = storage_path('app/tmp/' . $tempFile);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        return redirect($this->data['controller_route']);
    }
    /* cancel Upload */


    /* download uploaded csv */
    public function csvDownload(Request $request, $id)
    {
        $id = Helper::decoded($id);

        try {
            $lead = UploadLead::find($id);

            if (!$lead) {
                return redirect()->back()->with('error_message', 'Lead not found');
            }

            $filePath = public_path('uploads/lead/' . $lead->filename);

            if (!file_exists($filePath)) {
                return redirect()->back()->with('error_message', 'File not found');
            }

            /* user activity */
            $activityData = [
                'user_email'        => session('user_data')['email'],
                'user_name'         => session('user_data')['name'],
                'user_type'         => 'ADMIN',
                'ip_address'        => $request->ip(),
                'activity_type'     => 3,
                'activity_details'  => $lead->title . ' ' . $this->data['title'] . ' Downloaded',
                'platform_type'     => 'WEB',
            ];
            UserActivity::insert($activityData);
            /* user activity */

            return response()->download($filePath);
        } catch (\Exception $e) {
            return redirect()->back()->with('error_message', 'Error downloading file');
        }
    }
    /* download uploaded csv */


    /* delete upload leads */
    public function delete(Request $request, $id)
    {
        $id = Helper::decoded($id);
        $uploadLead = UploadLead::find($id);

        $fields = [
            'status'             => 3,
            'deleted_at'         => date('Y-m-d H:i:s'),
        ];
        UploadLead::where('id', '=', $id)->update($fields);
        MasterLead::where('upload_id', '=', $id)->update($fields);
        BranchLead::where('upload_id', '=', $id)->update($fields);

        /* user activity */
        $activityData = [
            'user_email'        => session('user_data')['email'],
            'user_name'         => session('user_data')['name'],
            'user_type'         => 'ADMIN',
            'ip_address'        => $request->ip(),
            'activity_type'     => 3,
            'activity_details'  => $uploadLead->title . ' ' . $this->data['title'] . ' Deleted',
            'platform_type'     => 'WEB',
        ];
        UserActivity::insert($activityData);
        /* user activity */
        return redirect($this->data['controller_route'])->with('success_message', $uploadLead->title . ' ' . $this->data['title'] . ' Deleted Successfully !!!');
    }
    /* delete upload leads */


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
    /* ajax requests */
}
